<?php

namespace App\Services;

use App\Models\UserWebhook;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookDispatcher
{
    /**
     * Disparar webhooks para un evento específico
     */
    public static function dispatch(string $event, User $user, array $payload): void
    {
        $webhooks = UserWebhook::where('user_id', $user->id)
            ->where('event', $event)
            ->where('is_active', true)
            ->get();

        foreach ($webhooks as $webhook) {
            dispatch(function () use ($webhook, $payload) {
                self::send($webhook, $payload);
            })->afterResponse();
        }
    }

    /**
     * Enviar webhook con reintentos
     */
    protected static function send(UserWebhook $webhook, array $payload): void
    {
        $attempt = 0;
        $maxRetries = $webhook->max_retries;

        while ($attempt <= $maxRetries) {
            try {
                $signedPayload = self::signPayload($webhook, $payload);

                $response = Http::timeout($webhook->timeout)
                    ->withHeaders($webhook->headers ?? [])
                    ->post($webhook->url, $signedPayload);

                if ($response->successful()) {
                    $webhook->incrementSuccess();

                    Log::info('Webhook sent successfully', [
                        'webhook_id' => $webhook->id,
                        'event' => $webhook->event,
                        'url' => $webhook->url,
                    ]);

                    return; // Éxito, salir
                }

            } catch (\Exception $e) {
                Log::error('Webhook failed', [
                    'webhook_id' => $webhook->id,
                    'attempt' => $attempt + 1,
                    'error' => $e->getMessage(),
                ]);
            }

            $attempt++;
            if ($attempt <= $maxRetries) {
                sleep(pow(2, $attempt)); // Backoff exponencial
            }
        }

        $webhook->incrementFailure();
    }

    /**
     * Firmar payload para seguridad
     */
    protected static function signPayload(UserWebhook $webhook, array $payload): array
    {
        $enrichedPayload = [
            'event' => $webhook->event,
            'timestamp' => now()->toIso8601String(),
            'data' => $payload,
        ];

        if ($webhook->secret) {
            $enrichedPayload['signature'] = hash_hmac(
                'sha256',
                json_encode($enrichedPayload['data']),
                $webhook->secret
            );
        }

        return $enrichedPayload;
    }
}
