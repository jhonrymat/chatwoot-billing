<?php

namespace App\Observers;

use App\Models\Subscription;
use App\Jobs\CreateChatwootAccountJob;
use App\Jobs\SuspendChatwootAccountJob;
use App\Jobs\ActivateChatwootAccountJob;
use Illuminate\Support\Facades\Log;

class SubscriptionObserver
{
    /**
     * Handle the Subscription "created" event.
     */
    public function created(Subscription $subscription): void
    {
        Log::info('Subscription created', [
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'plan_id' => $subscription->plan_id,
        ]);
    }

    /**
     * Handle the Subscription "updated" event.
     */
    public function updated(Subscription $subscription): void
    {
        // Detectar cambio de estado
        if ($subscription->isDirty('status')) {
            $oldStatus = $subscription->getOriginal('status');
            $newStatus = $subscription->status;

            Log::info('Subscription status changed', [
                'subscription_id' => $subscription->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);

            // Activación de suscripción → Crear cuenta Chatwoot
            if ($oldStatus === 'pending' && $newStatus === 'active') {
                Log::info('Dispatching CreateChatwootAccountJob', [
                    'subscription_id' => $subscription->id,
                ]);
                CreateChatwootAccountJob::dispatch($subscription);
            }

            // Suscripción suspendida → Suspender cuenta Chatwoot
            if ($newStatus === 'suspended') {
                Log::info('Dispatching SuspendChatwootAccountJob', [
                    'subscription_id' => $subscription->id,
                ]);
                SuspendChatwootAccountJob::dispatch($subscription, 'subscription_suspended');
            }

            // Suscripción cancelada → Suspender cuenta Chatwoot
            if ($newStatus === 'cancelled') {
                Log::info('Dispatching SuspendChatwootAccountJob', [
                    'subscription_id' => $subscription->id,
                ]);
                SuspendChatwootAccountJob::dispatch($subscription, 'subscription_cancelled');
            }

            // Reactivación → Activar cuenta Chatwoot
            if (in_array($oldStatus, ['suspended', 'cancelled']) && $newStatus === 'active') {
                Log::info('Dispatching ActivateChatwootAccountJob', [
                    'subscription_id' => $subscription->id,
                ]);
                ActivateChatwootAccountJob::dispatch($subscription);
            }
        }
    }

    /**
     * Handle the Subscription "deleted" event.
     */
    public function deleted(Subscription $subscription): void
    {
        Log::warning('Subscription deleted', [
            'subscription_id' => $subscription->id,
        ]);

        // Suspender cuenta de Chatwoot si existe
        if ($subscription->chatwootAccount) {
            SuspendChatwootAccountJob::dispatch($subscription, 'subscription_deleted');
        }
    }
}
