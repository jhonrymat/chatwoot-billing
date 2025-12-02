<?php

// ============================================
// app/Notifications/ChatwootAccountCreatedNotification.php
// php artisan make:notification ChatwootAccountCreatedNotification
// ============================================

namespace App\Notifications;

use App\Models\ChatwootAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChatwootAccountCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ChatwootAccount $chatwootAccount
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('¡Tu cuenta de Chatwoot está lista! 🎉')
            ->greeting("¡Hola {$notifiable->name}!")
            ->line('Tu cuenta de Chatwoot ha sido creada exitosamente.')
            ->line('Ya puedes empezar a gestionar las conversaciones con tus clientes.')
            ->line('')
            ->line('**Detalles de acceso:**')
            ->line("URL: {$this->chatwootAccount->chatwoot_url}")
            ->line("Email: {$notifiable->email}")
            ->line("Nota: Usa las mismas credenciales de tu cuenta de facturación")
            ->line('')
            ->action('Acceder a Chatwoot', $this->chatwootAccount->full_dashboard_url)
            ->line('')
            ->line('**Próximos pasos:**')
            ->line('1. Configura tu perfil')
            ->line('2. Crea tus primeros canales de comunicación')
            ->line('3. Invita a tu equipo')
            ->line('')
            ->line('¿Necesitas ayuda? Consulta nuestra documentación o contáctanos.')
            ->salutation('Saludos,')
            ->salutation(config('app.name'));
    }
}
