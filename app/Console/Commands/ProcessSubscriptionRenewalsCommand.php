<?php
// ============================================
// app/Console/Commands/ProcessSubscriptionRenewalsCommand.php
// php artisan make:command ProcessSubscriptionRenewalsCommand
// ============================================

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\MercadoPagoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionRenewalsCommand extends Command
{
    protected $signature = 'subscriptions:process-renewals';
    protected $description = 'Process subscription renewals for today';

    public function handle(): int
    {
        $this->info('Processing subscription renewals...');

        // Suscripciones que deben renovarse hoy
        $subscriptions = Subscription::active()
            ->whereDate('next_billing_date', today())
            ->with(['user', 'plan'])
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('No subscriptions to renew today.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($subscriptions->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($subscriptions as $subscription) {
            try {
                // El cobro se hace automáticamente por MercadoPago
                // Aquí solo enviamos recordatorios si es necesario

                Log::info('Subscription renewal processed', [
                    'subscription_id' => $subscription->id,
                ]);

                $success++;

            } catch (\Exception $e) {
                Log::error('Subscription renewal failed', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);

                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Processed {$subscriptions->count()} renewals");
        $this->info("Success: {$success}");
        if ($failed > 0) {
            $this->warn("Failed: {$failed}");
        }

        return self::SUCCESS;
    }
}
