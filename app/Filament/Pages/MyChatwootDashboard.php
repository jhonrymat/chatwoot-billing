<?php

// ============================================
// app/Filament/Pages/MyChatwootDashboard.php
// Vista embebida de métricas de Chatwoot
// ============================================

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Services\ChatwootService;
use Filament\Support\Icons\Heroicon;
use Filament\Notifications\Notification;

class MyChatwootDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected string $view = 'filament.pages.my-chatwoot-dashboard';

    protected static ?string $navigationLabel = 'Métricas Chatwoot';

    protected static string | \UnitEnum | null $navigationGroup = 'Chatwoot';

    protected static ?int $navigationSort = 1;

    public $metrics;
    public $limits;
    public $isLoading = false;

    public static function canAccess(): bool
    {
        return auth()->user()->isSubscriber() && auth()->user()->activeChatwootAccount;
    }
public function mount(ChatwootService $chatwootService)
    {
        $account = auth()->user()->activeChatwootAccount;

        if (!$account) {
            Notification::make()
                ->title('No tienes cuenta de Chatwoot')
                ->warning()
                ->send();

            $this->redirect(route('filament.admin.pages.my-dashboard'));
            return;
        }

        $this->loadMetrics($chatwootService);
    }

    protected function loadMetrics(ChatwootService $chatwootService)
    {
        $account = auth()->user()->activeChatwootAccount;

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
            Notification::make()
                ->title('Error al obtener métricas')
                ->body($e->getMessage())
                ->danger()
                ->send();

            // Métricas por defecto
            $this->metrics = [
                'conversations' => ['total' => 0, 'open' => 0, 'resolved' => 0, 'pending' => 0],
                'agents' => ['total' => 0, 'active' => 0],
                'inboxes' => ['total' => 0],
                'contacts' => ['total' => 0],
            ];

            $this->limits = null;
        }
    }

    // ✅ NUEVO: Acción en el header de la página
    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Actualizar Métricas')
                ->icon('heroicon-o-arrow-path')
                ->action(function (ChatwootService $chatwootService) {
                    $this->isLoading = true;

                    $account = auth()->user()->activeChatwootAccount;
                    \App\Jobs\SyncChatwootMetricsJob::dispatch($account);

                    Notification::make()
                        ->title('Sincronización iniciada')
                        ->body('Las métricas se actualizarán en breve')
                        ->success()
                        ->send();

                    // Recargar después de 3 segundos
                    $this->dispatch('refresh-metrics');
                    $this->isLoading = false;
                }),
        ];
    }
}
