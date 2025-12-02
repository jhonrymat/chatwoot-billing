<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\ChatwootService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SuspendChatwootAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    /**
     * Constructor
     */
    public function __construct(
        public Subscription $subscription,
        public string $reason = 'payment_failed'
    ) {}

    /**
     * Ejecutar el job
     */
    public function handle(ChatwootService $chatwootService): void
    {
        try {
            $chatwootAccount = $this->subscription->chatwootAccount;

            if (!$chatwootAccount) {
                Log::warning('No Chatwoot account to suspend', [
                    'subscription_id' => $this->subscription->id,
                ]);
                return;
            }

            // Suspender en Chatwoot
            $chatwootService->suspendAccount($chatwootAccount->chatwoot_account_id);

            // Actualizar estado local
            $chatwootAccount->suspend();

            // Registrar actividad
            activity()
                ->causedBy($this->subscription->user)
                ->performedOn($chatwootAccount)
                ->withProperties(['reason' => $this->reason])
                ->log('Cuenta de Chatwoot suspendida');

            Log::info('Chatwoot account suspended', [
                'subscription_id' => $this->subscription->id,
                'chatwoot_account_id' => $chatwootAccount->chatwoot_account_id,
                'reason' => $this->reason,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to suspend Chatwoot account', [
                'subscription_id' => $this->subscription->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
