<?php

// ============================================
// app/Filament/Pages/UpgradePlan.php
// Página para mejorar plan
// php artisan make:filament-page UpgradePlan
// ============================================

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class UpgradePlan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingUp;

    protected string $view = 'filament.pages.upgrade-plan';

    protected static ?string $navigationLabel = 'Mejorar Plan';

    protected static string | \UnitEnum | null $navigationGroup = 'Mi Cuenta';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()->isSubscriber() && auth()->user()->hasActiveSubscription();
    }
}
