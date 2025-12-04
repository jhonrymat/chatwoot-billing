<?php

// ============================================
// app/Filament/Widgets/MyChatwootMetrics.php
// php artisan make:filament-widget MyChatwootMetrics
// ============================================

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MyChatwootMetrics extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->isSubscriber() && auth()->user()->activeChatwootAccount;
    }

    protected function getStats(): array
    {
        $account = auth()->user()->activeChatwootAccount;

        if (!$account) {
            return [];
        }

        $metrics = $account->getLatestMetrics();

        if (!$metrics) {
            return [
                Stat::make('Métricas', 'No disponibles')
                    ->description('Sincronizando...')
                    ->descriptionIcon('heroicon-o-arrow-path')
                    ->color('gray'),
            ];
        }

        return [
            Stat::make('Conversaciones', $metrics->total_conversations)
                ->description("{$metrics->open_conversations} abiertas")
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color('primary')
                ->chart($this->getConversationsChart()),

            Stat::make('Agentes Activos', $metrics->active_agents)
                ->description("De {$metrics->total_agents} totales")
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),

            Stat::make('Inboxes', $metrics->total_inboxes)
                ->description('Canales configurados')
                ->descriptionIcon('heroicon-o-inbox-stack')
                ->color('info'),

            Stat::make('Contactos', $metrics->total_contacts)
                ->description('En tu base de datos')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('warning'),
        ];
    }

    protected function getConversationsChart(): array
    {
        $account = auth()->user()->activeChatwootAccount;

        $last7Days = \App\Models\ChatwootMetric::where('chatwoot_account_id', $account->id)
            ->whereBetween('metrics_date', [now()->subDays(6), now()])
            ->orderBy('metrics_date')
            ->pluck('total_conversations')
            ->toArray();

        return array_pad($last7Days, 7, 0);
    }
}
