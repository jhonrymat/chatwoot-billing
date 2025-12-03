<?php
// ============================================
// app/Notifications/SubscriptionRenewalReminderNotification.php
// php artisan make:notification SubscriptionRenewalReminderNotification
// ============================================

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionRenewalReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Subscription $subscription
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $daysUntil = now()->diffInDays($this->subscription->next_billing_date);

        return (new MailMessage)
            ->subject('Tu suscripción se renovará pronto')
            ->greeting("Hola {$notifiable->name},")
            ->line("Tu suscripción al plan {$this->subscription->plan->name} se renovará en {$daysUntil} días.")
            ->line("Fecha de renovación: {$this->subscription->next_billing_date}")
            ->line("Monto: \${$this->subscription->plan->formatted_price}")
            ->line('')
            ->line('El cargo se realizará automáticamente al método de pago registrado.')
            ->action('Ver Mi Suscripción', route('subscriptions.current'))
            ->line('Si deseas modificar o cancelar tu suscripción, puedes hacerlo desde tu panel.');
    }
}
