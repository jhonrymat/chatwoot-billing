<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Customer\CustomerClient;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    protected PreferenceClient $preferenceClient;
    protected PaymentClient $paymentClient;
    protected CustomerClient $customerClient;

    public function __construct()
    {
        $accessToken = config('mercadopago.access_token');

        if (!$accessToken) {
            throw new \RuntimeException('MercadoPago access token not configured');
        }

        MercadoPagoConfig::setAccessToken($accessToken);

        $this->preferenceClient = new PreferenceClient();
        $this->paymentClient = new PaymentClient();
        $this->customerClient = new CustomerClient();
    }

    public function createSubscriptionPreference(User $user, Plan $plan): array
    {
        try {
            $preference = $this->preferenceClient->create([
                'items' => [[
                    'id' => (string) $plan->id,
                    'title' => $plan->name,
                    'description' => $plan->description,
                    'quantity' => 1,
                    'unit_price' => (float) $plan->price,
                    'currency_id' => $plan->currency,
                ]],
                'payer' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ? ['number' => $user->phone] : null,
                ],
                'back_urls' => [
                    'success' => route('payment.success'),
                    'failure' => route('payment.failure'),
                    'pending' => route('payment.pending'),
                ],
                'auto_return' => 'approved',
                'external_reference' => "subscription_{$user->id}_{$plan->id}",
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'type' => 'subscription',
                ],
                'statement_descriptor' => config('app.name'),
                'notification_url' => route('webhook.mercadopago'),
            ]);

            Log::info('MercadoPago preference created', [
                'preference_id' => $preference->id,
                'user_id' => $user->id,
            ]);

            return [
                'preference_id' => $preference->id,
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point,
            ];

        } catch (MPApiException $e) {
            Log::error('MercadoPago preference creation failed', [
                'error' => $e->getMessage(),
                'api_response' => $e->getApiResponse(),
            ]);
            throw $e;
        }
    }

    public function getPayment(string $paymentId): array
    {
        try {
            $payment = $this->paymentClient->get($paymentId);

            return [
                'id' => $payment->id,
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'amount' => $payment->transaction_amount,
                'currency' => $payment->currency_id,
                'payment_method' => $payment->payment_method_id,
                'payment_type' => $payment->payment_type_id,
                'payer_email' => $payment->payer->email ?? null,
                'external_reference' => $payment->external_reference,
                'metadata' => $payment->metadata ?? [],
            ];

        } catch (MPApiException $e) {
            Log::error('Failed to get payment', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function mapPaymentStatus(string $status): string
    {
        return match($status) {
            'approved' => 'approved',
            'pending', 'in_process', 'in_mediation' => 'pending',
            'rejected' => 'rejected',
            'cancelled' => 'cancelled',
            'refunded' => 'refunded',
            'charged_back' => 'charged_back',
            default => 'pending',
        };
    }

    public function getPublicKey(): string
    {
        return config('mercadopago.public_key');
    }

    public function isProduction(): bool
    {
        return config('mercadopago.production_mode', false);
    }

    public function validateWebhookSignature(array $headers, string $body): bool
    {
        $secret = config('mercadopago.webhook_secret');
        if (!$secret) {
            return true; // Skip validation in development
        }

        // Implementar validación según docs de MP
        return true;
    }
}
