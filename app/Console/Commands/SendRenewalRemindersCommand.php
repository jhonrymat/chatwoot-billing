<?php
// ============================================
// app/Console/Commands/SendRenewalRemindersCommand.php
// php artisan make:command SendRenewalRemindersCommand
// ============================================

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionRenewalReminderNotification;
use Illuminate\Console\Command;

class SendRenewalRemindersCommand extends Command
{
    protected $signature = 'subscriptions:send-reminders';
    protected $description = 'Send renewal reminders for subscriptions expiring soon';

    public function handle(): int
    {
        $this->info('Sending renewal reminders...');

        $daysBeforeReminder = config('billing.renewals.remind_days_before', 3);

        // Suscripciones que renovarán en los próximos N días
        $subscriptions = Subscription::active()
            ->whereDate('next_billing_date', '>=', today())
            ->whereDate('next_billing_date', '<=', today()->addDays($daysBeforeReminder))
            ->with(['user', 'plan'])
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('No reminders to send.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($subscriptions->count());
        $bar->start();

        foreach ($subscriptions as $subscription) {
            $subscription->user->notify(
                new SubscriptionRenewalReminderNotification($subscription)
            );
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Sent {$subscriptions->count()} reminders");

        return self::SUCCESS;
    }
}
