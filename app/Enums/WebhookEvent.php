<?php

namespace App\Enums;

enum WebhookEvent: string
{
    case ACCOUNT_CREATED = 'account.created';
    case ACCOUNT_SUSPENDED = 'account.suspended';
    case ACCOUNT_ACTIVATED = 'account.activated';
    case PAYMENT_APPROVED = 'payment.approved';
    case PAYMENT_FAILED = 'payment.failed';
    case SUBSCRIPTION_ACTIVATED = 'subscription.activated';
    case SUBSCRIPTION_CANCELLED = 'subscription.cancelled';
    case SUBSCRIPTION_RENEWED = 'subscription.renewed';
    case PLAN_LIMIT_EXCEEDED = 'plan.limit_exceeded';

    public function label(): string
    {
        return match($this) {
            self::ACCOUNT_CREATED => 'Cuenta Chatwoot Creada',
            self::ACCOUNT_SUSPENDED => 'Cuenta Chatwoot Suspendida',
            self::ACCOUNT_ACTIVATED => 'Cuenta Chatwoot Activada',
            self::PAYMENT_APPROVED => 'Pago Aprobado',
            self::PAYMENT_FAILED => 'Pago Fallido',
            self::SUBSCRIPTION_ACTIVATED => 'Suscripción Activada',
            self::SUBSCRIPTION_CANCELLED => 'Suscripción Cancelada',
            self::SUBSCRIPTION_RENEWED => 'Suscripción Renovada',
            self::PLAN_LIMIT_EXCEEDED => 'Límite de Plan Excedido',
        };
    }
}
