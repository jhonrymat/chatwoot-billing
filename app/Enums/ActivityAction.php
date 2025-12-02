<?php
// ============================================
// app/Enums/ActivityAction.php
// ============================================

namespace App\Enums;

enum ActivityAction: string
{
    // Suscripciones
    case SUBSCRIPTION_CREATED = 'subscription.created';
    case SUBSCRIPTION_ACTIVATED = 'subscription.activated';
    case SUBSCRIPTION_CANCELLED = 'subscription.cancelled';
    case SUBSCRIPTION_SUSPENDED = 'subscription.suspended';
    case SUBSCRIPTION_RESUMED = 'subscription.resumed';
    case SUBSCRIPTION_EXPIRED = 'subscription.expired';
    case SUBSCRIPTION_PLAN_CHANGED = 'subscription.plan_changed';

    // Pagos
    case PAYMENT_CREATED = 'payment.created';
    case PAYMENT_APPROVED = 'payment.approved';
    case PAYMENT_REJECTED = 'payment.rejected';
    case PAYMENT_REFUNDED = 'payment.refunded';

    // Métodos de pago
    case PAYMENT_METHOD_ADDED = 'payment_method.added';
    case PAYMENT_METHOD_REMOVED = 'payment_method.removed';
    case PAYMENT_METHOD_SET_DEFAULT = 'payment_method.set_default';

    // Chatwoot
    case CHATWOOT_ACCOUNT_CREATED = 'chatwoot.account_created';
    case CHATWOOT_ACCOUNT_SUSPENDED = 'chatwoot.account_suspended';
    case CHATWOOT_ACCOUNT_ACTIVATED = 'chatwoot.account_activated';
    case CHATWOOT_METRICS_SYNCED = 'chatwoot.metrics_synced';

    // Usuarios
    case USER_REGISTERED = 'user.registered';
    case USER_LOGIN = 'user.login';
    case USER_LOGOUT = 'user.logout';
    case USER_PASSWORD_CHANGED = 'user.password_changed';

    public function label(): string
    {
        return match($this) {
            // Suscripciones
            self::SUBSCRIPTION_CREATED => 'Suscripción creada',
            self::SUBSCRIPTION_ACTIVATED => 'Suscripción activada',
            self::SUBSCRIPTION_CANCELLED => 'Suscripción cancelada',
            self::SUBSCRIPTION_SUSPENDED => 'Suscripción suspendida',
            self::SUBSCRIPTION_RESUMED => 'Suscripción reanudada',
            self::SUBSCRIPTION_EXPIRED => 'Suscripción expirada',
            self::SUBSCRIPTION_PLAN_CHANGED => 'Plan cambiado',

            // Pagos
            self::PAYMENT_CREATED => 'Pago creado',
            self::PAYMENT_APPROVED => 'Pago aprobado',
            self::PAYMENT_REJECTED => 'Pago rechazado',
            self::PAYMENT_REFUNDED => 'Pago reembolsado',

            // Métodos de pago
            self::PAYMENT_METHOD_ADDED => 'Método de pago agregado',
            self::PAYMENT_METHOD_REMOVED => 'Método de pago eliminado',
            self::PAYMENT_METHOD_SET_DEFAULT => 'Método de pago predeterminado',

            // Chatwoot
            self::CHATWOOT_ACCOUNT_CREATED => 'Cuenta Chatwoot creada',
            self::CHATWOOT_ACCOUNT_SUSPENDED => 'Cuenta Chatwoot suspendida',
            self::CHATWOOT_ACCOUNT_ACTIVATED => 'Cuenta Chatwoot activada',
            self::CHATWOOT_METRICS_SYNCED => 'Métricas sincronizadas',

            // Usuarios
            self::USER_REGISTERED => 'Usuario registrado',
            self::USER_LOGIN => 'Inicio de sesión',
            self::USER_LOGOUT => 'Cierre de sesión',
            self::USER_PASSWORD_CHANGED => 'Contraseña cambiada',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::SUBSCRIPTION_CREATED, self::SUBSCRIPTION_ACTIVATED => 'heroicon-o-check-circle',
            self::SUBSCRIPTION_CANCELLED, self::SUBSCRIPTION_EXPIRED => 'heroicon-o-x-circle',
            self::SUBSCRIPTION_SUSPENDED => 'heroicon-o-pause-circle',
            self::SUBSCRIPTION_RESUMED => 'heroicon-o-play-circle',
            self::SUBSCRIPTION_PLAN_CHANGED => 'heroicon-o-arrow-path',

            self::PAYMENT_CREATED, self::PAYMENT_APPROVED => 'heroicon-o-currency-dollar',
            self::PAYMENT_REJECTED => 'heroicon-o-exclamation-triangle',
            self::PAYMENT_REFUNDED => 'heroicon-o-arrow-uturn-left',

            self::PAYMENT_METHOD_ADDED => 'heroicon-o-credit-card',
            self::PAYMENT_METHOD_REMOVED => 'heroicon-o-trash',
            self::PAYMENT_METHOD_SET_DEFAULT => 'heroicon-o-star',

            self::CHATWOOT_ACCOUNT_CREATED => 'heroicon-o-chat-bubble-left-right',
            self::CHATWOOT_ACCOUNT_SUSPENDED => 'heroicon-o-pause',
            self::CHATWOOT_ACCOUNT_ACTIVATED => 'heroicon-o-play',
            self::CHATWOOT_METRICS_SYNCED => 'heroicon-o-arrow-path',

            self::USER_REGISTERED => 'heroicon-o-user-plus',
            self::USER_LOGIN => 'heroicon-o-arrow-right-on-rectangle',
            self::USER_LOGOUT => 'heroicon-o-arrow-left-on-rectangle',
            self::USER_PASSWORD_CHANGED => 'heroicon-o-key',
        };
    }
}
