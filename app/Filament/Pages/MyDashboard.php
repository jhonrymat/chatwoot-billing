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

    // ✅ Getter para métricas (usado en la vista)
    public function getChatwootMetricsProperty()
    {
        $account = auth()->user()->activeChatwootAccount;

        if (!$account) {
            return [
                'agents' => ['current' => 0, 'limit' => 0],
                'inboxes' => ['current' => 0, 'limit' => 0],
                'contacts' => ['current' => 0, 'limit' => 0],
            ];
        }

        $metrics = $account->getLatestMetrics();
        $plan = auth()->user()->activeSubscription?->plan;

        return [
            'agents' => [
                'current' => $metrics?->active_agents ?? 0,
                'limit' => $plan?->max_agents ?? 0,
            ],
            'inboxes' => [
                'current' => $metrics?->total_inboxes ?? 0,
                'limit' => $plan?->max_inboxes ?? 0,
            ],
            'contacts' => [
                'current' => $metrics?->total_contacts ?? 0,
                'limit' => $plan?->max_contacts ?? 0,
            ],
        ];
    }

    public function getColumns(): int|string|array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'xl' => 3,
        ];
    }
}
