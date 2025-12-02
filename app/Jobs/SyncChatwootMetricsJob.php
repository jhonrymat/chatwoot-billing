<?php
namespace App\Jobs;

use App\Models\ChatwootAccount;
use App\Models\ChatwootMetric;
use App\Services\ChatwootService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncChatwootMetricsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 60;

    /**
     * Constructor
     */
    public function __construct(
        public ChatwootAccount $chatwootAccount
    ) {}

    /**
     * Ejecutar el job
     */
    public function handle(ChatwootService $chatwootService): void
    {
        try {
            // Verificar que la cuenta esté activa
            if ($this->chatwootAccount->status !== 'active') {
                Log::info('Skipping metrics sync for inactive account', [
                    'account_id' => $this->chatwootAccount->chatwoot_account_id,
                ]);
                return;
            }

            // Obtener métricas comprehensivas
            $metrics = $chatwootService->getComprehensiveMetrics(
                $this->chatwootAccount->chatwoot_account_id
            );

            // Guardar o actualizar métricas del día
            ChatwootMetric::updateOrCreate(
                [
                    'chatwoot_account_id' => $this->chatwootAccount->id,
                    'metrics_date' => now()->toDateString(),
                ],
                [
                    'total_conversations' => $metrics['conversations']['total'],
                    'open_conversations' => $metrics['conversations']['open'],
                    'resolved_conversations' => $metrics['conversations']['resolved'],
                    'pending_conversations' => $metrics['conversations']['pending'],
                    'total_agents' => $metrics['agents']['total'],
                    'active_agents' => $metrics['agents']['active'],
                    'total_inboxes' => $metrics['inboxes']['total'],
                    'total_contacts' => $metrics['contacts']['total'],
                    'avg_first_response_time' => $metrics['response_times']['first_response'],
                    'avg_resolution_time' => $metrics['response_times']['resolution'],
                    'raw_data' => $metrics,
                ]
            );

            // Actualizar timestamp de sincronización
            $this->chatwootAccount->markAsSynced();

            Log::info('Chatwoot metrics synced successfully', [
                'account_id' => $this->chatwootAccount->chatwoot_account_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to sync Chatwoot metrics', [
                'account_id' => $this->chatwootAccount->chatwoot_account_id,
                'error' => $e->getMessage(),
            ]);

            // Registrar error en la cuenta
            $this->chatwootAccount->recordSyncError($e->getMessage());

            throw $e;
        }
    }

    /**
     * Manejar fallo del job
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SyncChatwootMetricsJob failed', [
            'account_id' => $this->chatwootAccount->chatwoot_account_id,
            'error' => $exception->getMessage(),
        ]);

        $this->chatwootAccount->recordSyncError($exception->getMessage());
    }
}
