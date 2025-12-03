<?php

// ============================================
// app/Filament/Widgets/StatsOverview.php
// ============================================

namespace App\Filament\Widgets;

use App\Models\Subscription;
use App\Models\Payment;
use App\Models\User;
use App\Models\ChatwootAccount;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Solo visible para admins
    public static function canView(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected function getStats(): array
    {
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $totalRevenue = Payment::where('status', 'approved')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
        $newUsers = User::whereMonth('created_at', now()->month)->count();
        $activeChatwoot = ChatwootAccount::where('status', 'active')->count();

        return [
            Stat::make('Suscripciones Activas', $activeSubscriptions)
                ->description('Total de suscripciones activas')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success')
                ->chart([7, 12, 15, 18, 22, 28, $activeSubscriptions]),

            Stat::make('Ingresos del Mes', '$' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Pagos aprobados este mes')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Nuevos Usuarios', $newUsers)
                ->description('Registros este mes')
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('info'),

            Stat::make('Cuentas Chatwoot', $activeChatwoot)
                ->description('Cuentas activas')
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color('warning'),
        ];
    }
}
