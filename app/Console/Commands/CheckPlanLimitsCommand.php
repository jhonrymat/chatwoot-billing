<?php

namespace App\Console\Commands;

use App\Jobs\CheckPlanLimitsJob;
use App\Models\Subscription;
use Illuminate\Console\Command;

class CheckPlanLimitsCommand extends Command
{
    protected $signature = 'chatwoot:check-limits
                            {--subscription= : Check specific subscription}';

    protected $description = 'Check plan limits for active subscriptions';

    public function handle(): int
    {
        $this->info('Checking plan limits...');

        $query = Subscription::active()->with(['plan', 'chatwootAccount']);

        if ($subscriptionId = $this->option('subscription')) {
            $query->where('id', $subscriptionId);
        }

        $subscriptions = $query->get();

        if ($subscriptions->isEmpty()) {
            $this->info('No active subscriptions to check.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($subscriptions->count());
        $bar->start();

        foreach ($subscriptions as $subscription) {
            if ($subscription->chatwootAccount) {
                CheckPlanLimitsJob::dispatch($subscription);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Dispatched limit checks for {$subscriptions->count()} subscriptions.");

        return self::SUCCESS;
    }
}
