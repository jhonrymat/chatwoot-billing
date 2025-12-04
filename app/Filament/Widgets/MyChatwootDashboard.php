<?php

// ============================================
// app/Filament/Pages/MyChatwootDashboard.php
// Vista embebida de métricas de Chatwoot
// ============================================

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Services\ChatwootService;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class MyChatwootDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected string $view = 'filament.pages.my-chatwoot-dashboard';

    protected static ?string $navigationLabel = 'Métricas Chatwoot';

    protected static string | \UnitEnum | null $navigationGroup = 'Chatwoot';

    protected static ?int $navigationSort = 1;

    public $metrics;
    public $limits;

    public static function canAccess(): bool
    {
        return auth()->user()->isSubscriber() && auth()->user()->activeChatwootAccount;
    }

    public function mount(ChatwootService $chatwootService)
    {
        $account = auth()->user()->activeChatwootAccount;

        if (!$account) {
            $this->redirect(route('filament.admin.pages.my-dashboard'));
            return;
        }

        try {
            $this->metrics = $chatwootService->getComprehensiveMetrics($account->chatwoot_account_id);

            $plan = auth()->user()->activeSubscription->plan;
            $this->limits = $chatwootService->checkPlanLimits(
                $account->chatwoot_account_id,
                [
                    'max_agents' => $plan->max_agents,
                    'max_inboxes' => $plan->max_inboxes,
                    'max_contacts' => $plan->max_contacts,
                ]
            );
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Error al obtener métricas')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function refreshMetrics()
    {
        $account = auth()->user()->activeChatwootAccount;
        \App\Jobs\SyncChatwootMetricsJob::dispatch($account);

        \Filament\Notifications\Notification::make()
            ->title('Sincronización iniciada')
            ->body('Las métricas se actualizarán en unos momentos')
            ->success()
            ->send();
    }
}
