<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanLimitExceededNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public array $limits,
        public array $exceeded
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('⚠️ Has alcanzado el límite de tu plan')
            ->greeting("Hola {$notifiable->name},")
            ->line('Has alcanzado o excedido los límites de tu plan actual:')
            ->line('');

        // Agregar detalles de cada límite excedido
        foreach ($this->exceeded as $resource) {
            $limit = $this->limits[$resource];
            $message->line("**{$this->getResourceName($resource)}**: {$limit['current']} / {$limit['limit']}");
        }

        return $message
            ->line('')
            ->line('Para continuar agregando más recursos, considera mejorar tu plan.')
            ->action('Ver Planes Disponibles', url('/plans'))
            ->line('')
            ->line('Si tienes preguntas, no dudes en contactarnos.')
            ->salutation('Saludos,')
            ->salutation(config('app.name'));
    }

    protected function getResourceName(string $resource): string
    {
        return match($resource) {
            'agents' => 'Agentes',
            'inboxes' => 'Canales',
            'contacts' => 'Contactos',
            default => ucfirst($resource),
        };
    }
}
