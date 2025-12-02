<?php
// ============================================
// app/Enums/PaymentStatus.php
// ============================================

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case CHARGED_BACK = 'charged_back';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::APPROVED => 'Aprobado',
            self::REJECTED => 'Rechazado',
            self::CANCELLED => 'Cancelado',
            self::REFUNDED => 'Reembolsado',
            self::CHARGED_BACK => 'Contracargo',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::CANCELLED => 'gray',
            self::REFUNDED => 'info',
            self::CHARGED_BACK => 'danger',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
