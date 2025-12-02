<?php
// ============================================
// app/Enums/PaymentMethodType.php
// ============================================

namespace App\Enums;

enum PaymentMethodType: string
{
    case CREDIT_CARD = 'credit_card';
    case DEBIT_CARD = 'debit_card';
    case PSE = 'pse';
    case EFECTY = 'efecty';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::CREDIT_CARD => 'Tarjeta de Crédito',
            self::DEBIT_CARD => 'Tarjeta Débito',
            self::PSE => 'PSE',
            self::EFECTY => 'Efecty',
            self::OTHER => 'Otro',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::CREDIT_CARD => 'heroicon-o-credit-card',
            self::DEBIT_CARD => 'heroicon-o-credit-card',
            self::PSE => 'heroicon-o-building-library',
            self::EFECTY => 'heroicon-o-banknotes',
            self::OTHER => 'heroicon-o-question-mark-circle',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
