<?php
// ============================================
// app/Enums/WebhookStatus.php
// ============================================

namespace App\Enums;

enum WebhookStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PROCESSED = 'processed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::PROCESSING => 'Procesando',
            self::PROCESSED => 'Procesado',
            self::FAILED => 'Fallido',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::PROCESSING => 'info',
            self::PROCESSED => 'success',
            self::FAILED => 'danger',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
