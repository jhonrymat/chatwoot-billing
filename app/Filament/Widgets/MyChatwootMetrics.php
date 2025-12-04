<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MyChatwootMetrics extends BaseWidget
{
    // ✅ Agregar polling para actualización automática
    protected ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return auth()->user()->hasRole('subscriber')
            && auth()->user()->activeChatwootAccount;
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
                    ->description('Sincronizando con Chatwoot...')
                    ->descriptionIcon('heroicon-o-arrow-path')
                    ->color('gray'),
            ];
        }

        return [
            Stat::make('Conversaciones', number_format($metrics->total_conversations))
                ->description($metrics->open_conversations . " abiertas")
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color('primary')
                ->chart($this->getConversationsChart($account)),

            Stat::make('Agentes', $metrics->active_agents . ' / ' . $metrics->total_agents)
                ->description("Activos")
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),

            Stat::make('Inboxes', $metrics->total_inboxes)
                ->description('Canales configurados')
                ->descriptionIcon('heroicon-o-inbox-stack')
                ->color('info'),

            Stat::make('Contactos', number_format($metrics->total_contacts))
                ->description('Total registrados')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('warning'),
        ];
    }

    protected function getConversationsChart($account): array
    {
        $last7Days = \App\Models\ChatwootMetric::where('chatwoot_account_id', $account->id)
            ->whereBetween('metrics_date', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->orderBy('metrics_date')
            ->pluck('total_conversations')
            ->toArray();

        // Rellenar con 0 si no hay suficientes datos
        return array_pad($last7Days, 7, 0);
    }
}
