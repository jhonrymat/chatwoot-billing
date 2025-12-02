<?php

// ============================================
// app/Enums/SubscriptionStatus.php
// ============================================

namespace App\Enums;

enum SubscriptionStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::ACTIVE => 'Activa',
            self::CANCELLED => 'Cancelada',
            self::EXPIRED => 'Expirada',
            self::SUSPENDED => 'Suspendida',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::ACTIVE => 'success',
            self::CANCELLED => 'danger',
            self::EXPIRED => 'gray',
            self::SUSPENDED => 'warning',
        };
    }

    public function badge(): string
    {
        return match($this) {
            self::PENDING => '⏳',
            self::ACTIVE => '✅',
            self::CANCELLED => '❌',
            self::EXPIRED => '⏹️',
            self::SUSPENDED => '⚠️',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
