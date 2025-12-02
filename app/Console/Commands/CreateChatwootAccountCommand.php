<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Subscription;
use App\Jobs\CreateChatwootAccountJob;
use Illuminate\Console\Command;

class CreateChatwootAccountCommand extends Command
{
    protected $signature = 'chatwoot:create-account
                            {subscription : Subscription ID}
                            {--sync : Execute synchronously instead of queuing}';

    protected $description = 'Create a Chatwoot account for a subscription';

    public function handle(): int
    {
        $subscriptionId = $this->argument('subscription');

        $subscription = Subscription::with(['user', 'plan', 'chatwootAccount'])
            ->find($subscriptionId);

        if (!$subscription) {
            $this->error("Subscription {$subscriptionId} not found.");
            return self::FAILURE;
        }

        if ($subscription->chatwootAccount) {
            $this->error('This subscription already has a Chatwoot account.');
            $this->info("Account ID: {$subscription->chatwootAccount->chatwoot_account_id}");
            return self::FAILURE;
        }

        $this->info("Creating Chatwoot account for:");
        $this->table(
            ['Field', 'Value'],
            [
                ['Subscription ID', $subscription->id],
                ['User', $subscription->user->name],
                ['Email', $subscription->user->email],
                ['Plan', $subscription->plan->name],
                ['Status', $subscription->status],
            ]
        );

        if (!$this->confirm('Continue?', true)) {
            return self::SUCCESS;
        }

        if ($this->option('sync')) {
            $this->info('Creating account synchronously...');

            try {
                CreateChatwootAccountJob::dispatchSync($subscription);
                $this->info('✅ Chatwoot account created successfully!');

                $subscription->refresh();
                if ($subscription->chatwootAccount) {
                    $this->info("Account ID: {$subscription->chatwootAccount->chatwoot_account_id}");
                    $this->info("Dashboard: {$subscription->chatwootAccount->full_dashboard_url}");
                }

                return self::SUCCESS;
            } catch (\Exception $e) {
                $this->error('❌ Failed: ' . $e->getMessage());
                return self::FAILURE;
            }
        } else {
            CreateChatwootAccountJob::dispatch($subscription);
            $this->info('✅ Job dispatched to queue.');
            $this->info('Monitor with: php artisan queue:work');
            return self::SUCCESS;
        }
    }
}
