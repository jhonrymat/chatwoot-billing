<?php


namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MySubscriptionWidget extends BaseWidget
{
    // Solo visible para subscribers
    public static function canView(): bool
    {
        return auth()->user()->isSubscriber();
    }

    protected function getStats(): array
    {
        $subscription = auth()->user()->activeSubscription;
        $chatwootAccount = auth()->user()->activeChatwootAccount;

        if (!$subscription) {
            return [
                Stat::make('Estado', 'Sin suscripción activa')
                    ->description('Selecciona un plan para comenzar')
                    ->descriptionIcon('heroicon-o-exclamation-triangle')
                    ->color('warning'),
            ];
        }

        $stats = [
            Stat::make('Plan Actual', $subscription->plan->name)
                ->description("Próxima renovación: {$subscription->next_billing_date}")
                ->descriptionIcon('heroicon-o-credit-card')
                ->color('success'),

            Stat::make('Estado', ucfirst($subscription->status))
                ->description('Tu suscripción')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color($subscription->is_active ? 'success' : 'warning'),
        ];

        if ($chatwootAccount) {
            $stats[] = Stat::make('Chatwoot', 'Activo')
                ->description('Cuenta configurada')
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color('info')
                ->url($chatwootAccount->full_dashboard_url, shouldOpenInNewTab: true);
        }

        return $stats;
    }
}
