<?php

namespace App\Jobs;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use App\Services\ChatwootService;
use App\Services\WebhookDispatcher;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ActivateChatwootAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    /**
     * Constructor
     */
    public function __construct(
        public Subscription $subscription
    ) {
    }

    /**
     * Ejecutar el job
     */
    public function handle(ChatwootService $chatwootService): void
    {
        try {
            $chatwootAccount = $this->subscription->chatwootAccount;

            if (!$chatwootAccount) {
                Log::warning('No Chatwoot account to activate', [
                    'subscription_id' => $this->subscription->id,
                ]);
                return;
            }

            // Activar en Chatwoot
            $chatwootService->activateAccount($chatwootAccount->chatwoot_account_id);

            // Actualizar estado local
            $chatwootAccount->activate();

            WebhookDispatcher::dispatch('account.activated', $this->subscription->user, [
                'chatwoot_account_id' => $chatwootAccount->chatwoot_account_id,
                'activated_at' => now()->toIso8601String(),
            ]);

            // Registrar actividad
            activity()
                ->causedBy($this->subscription->user)
                ->performedOn($chatwootAccount)
                ->log('Cuenta de Chatwoot reactivada');

            // Sincronizar métricas
            SyncChatwootMetricsJob::dispatch($chatwootAccount);

            Log::info('Chatwoot account activated', [
                'subscription_id' => $this->subscription->id,
                'chatwoot_account_id' => $chatwootAccount->chatwoot_account_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to activate Chatwoot account', [
                'subscription_id' => $this->subscription->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
