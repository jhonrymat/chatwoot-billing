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
use App\Notifications\PlanLimitExceededNotification;

class CheckPlanLimitsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;

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

            if (!$chatwootAccount || $chatwootAccount->status !== 'active') {
                return;
            }

            $plan = $this->subscription->plan;

            // Verificar límites
            $limits = $chatwootService->checkPlanLimits(
                $chatwootAccount->chatwoot_account_id,
                [
                    'max_agents' => $plan->max_agents,
                    'max_inboxes' => $plan->max_inboxes,
                    'max_contacts' => $plan->max_contacts,
                ]
            );

            // Si se excede algún límite, notificar
            $exceeded = collect($limits)->filter(fn($limit) => $limit['exceeded'])->keys();

            if ($exceeded->isNotEmpty()) {
                $this->subscription->user->notify(
                    new PlanLimitExceededNotification($limits, $exceeded->toArray())
                );

                WebhookDispatcher::dispatch('plan.limit_exceeded', $this->subscription->user, [
                    'limits' => $limits,
                    'exceeded' => $exceeded->toArray(),
                ]);

                Log::warning('Plan limits exceeded', [
                    'subscription_id' => $this->subscription->id,
                    'limits_exceeded' => $exceeded->toArray(),
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to check plan limits', [
                'subscription_id' => $this->subscription->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
