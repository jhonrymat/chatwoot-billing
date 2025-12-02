<?php
// ============================================
// app/Enums/BillingCycle.php
// ============================================

namespace App\Enums;

enum BillingCycle: string
{
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

    public function label(): string
    {
        return match($this) {
            self::MONTHLY => 'Mensual',
            self::YEARLY => 'Anual',
        };
    }

    public function months(): int
    {
        return match($this) {
            self::MONTHLY => 1,
            self::YEARLY => 12,
        };
    }

    public function discount(): int
    {
        return match($this) {
            self::MONTHLY => 0,
            self::YEARLY => 20, // 20% de descuento en plan anual
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
