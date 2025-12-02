<?php

// ============================================
// app/Jobs/CreateChatwootAccountJob.php
// php artisan make:job CreateChatwootAccountJob
// ============================================

namespace App\Jobs;

use App\Models\Subscription;
use App\Models\ChatwootAccount;
use App\Services\ChatwootService;
use App\Notifications\ChatwootAccountCreatedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateChatwootAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [30, 60, 120]; // Reintentos progresivos

    /**
     * Constructor
     */
    public function __construct(
        public Subscription $subscription
    ) {}

    /**
     * Ejecutar el job
     */
    public function handle(ChatwootService $chatwootService): void
    {
        try {
            // Verificar que no exista ya una cuenta
            if ($this->subscription->hasChatwootAccount()) {
                Log::info('Chatwoot account already exists for subscription', [
                    'subscription_id' => $this->subscription->id,
                ]);
                return;
            }

            $user = $this->subscription->user;
            $plan = $this->subscription->plan;

            // Crear cuenta en Chatwoot
            $accountData = $chatwootService->createAccount(
                name: $user->company_name ?? "{$user->name}'s Account",
                locale: config('chatwoot.default_locale', 'es')
            );

            // Crear usuario administrador en la cuenta
            $userData = $chatwootService->createUser(
                accountId: $accountData['id'],
                userData: [
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $this->generateSecurePassword(),
                    'role' => 'administrator',
                ]
            );

            // Si está configurado, crear inbox por defecto
            if (config('chatwoot.account.auto_create_inbox', true)) {
                try {
                    $chatwootService->createWebInbox(
                        accountId: $accountData['id'],
                        name: 'Canal Web',
                        websiteUrl: config('app.url')
                    );
                } catch (\Exception $e) {
                    // No crítico, puede crearlo después
                    Log::warning('Failed to create default inbox', [
                        'account_id' => $accountData['id'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Guardar en base de datos
            $chatwootAccount = ChatwootAccount::create([
                'subscription_id' => $this->subscription->id,
                'user_id' => $user->id,
                'chatwoot_account_id' => $accountData['id'],
                'chatwoot_account_name' => $accountData['name'],
                'chatwoot_user_id' => $userData['id'] ?? null,
                'chatwoot_url' => config('chatwoot.url'),
                'chatwoot_dashboard_url' => $chatwootService->getDashboardUrl($accountData['id']),
                'status' => 'active',
                'locale' => config('chatwoot.default_locale', 'es'),
                'timezone' => config('chatwoot.default_timezone', 'America/Bogota'),
                'last_synced_at' => now(),
            ]);

            // Registrar actividad
            activity()
                ->causedBy($user)
                ->performedOn($chatwootAccount)
                ->log('Cuenta de Chatwoot creada exitosamente');

            // Enviar notificación al usuario
            $user->notify(new ChatwootAccountCreatedNotification($chatwootAccount));

            // Sincronizar métricas iniciales
            SyncChatwootMetricsJob::dispatch($chatwootAccount);

            Log::info('Chatwoot account created successfully', [
                'subscription_id' => $this->subscription->id,
                'chatwoot_account_id' => $accountData['id'],
                'user_id' => $user->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create Chatwoot account', [
                'subscription_id' => $this->subscription->id,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Si se agotaron los reintentos, notificar al admin
            if ($this->attempts() >= $this->tries) {
                // TODO: Notificar a admins sobre el error
            }

            throw $e; // Re-lanzar para que se reintente
        }
    }

    /**
     * Generar contraseña segura
     * Nota: En producción, considera usar la misma contraseña del usuario de Laravel
     */
    protected function generateSecurePassword(): string
    {
        // Opción 1: Usar la misma contraseña del usuario
        // return $this->subscription->user->password; // Ya hasheada, no funciona

        // Opción 2: Generar una aleatoria y enviarla por email
        return bin2hex(random_bytes(16));
    }

    /**
     * Manejar fallo del job
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CreateChatwootAccountJob failed permanently', [
            'subscription_id' => $this->subscription->id,
            'error' => $exception->getMessage(),
        ]);

        // Marcar suscripción como con problemas
        $this->subscription->update([
            'status' => 'suspended',
        ]);
    }
}
