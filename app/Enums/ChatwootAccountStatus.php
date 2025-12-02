<?php
// ============================================
// app/Enums/ChatwootAccountStatus.php
// ============================================

namespace App\Enums;

enum ChatwootAccountStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case DELETED = 'deleted';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Activa',
            self::SUSPENDED => 'Suspendida',
            self::DELETED => 'Eliminada',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'success',
            self::SUSPENDED => 'warning',
            self::DELETED => 'danger',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
