<?php

// ============================================
// app/Filament/Pages/MyDashboard.php
// php artisan make:filament-page MyDashboard
// ============================================

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use App\Filament\Widgets\MyRecentActivity;
use App\Filament\Widgets\MyChatwootMetrics;
use App\Filament\Widgets\MySubscriptionWidget;

class MyDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected string $view = 'filament.pages.my-dashboard';

    protected static ?string $navigationLabel = 'Mi Dashboard';

    protected static ?int $navigationSort = 1;

    // Solo visible para subscribers
    public static function canAccess(): bool
    {
        return auth()->user()->isSubscriber();
    }

    public function getWidgets(): array
    {
        return [
            MySubscriptionWidget::class,
            MyChatwootMetrics::class,
            MyRecentActivity::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'xl' => 3,
        ];
    }
}
