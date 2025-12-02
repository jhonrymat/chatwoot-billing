<?php

namespace App\Console\Commands;

use App\Jobs\SyncChatwootMetricsJob;
use App\Models\ChatwootAccount;
use Illuminate\Console\Command;

class SyncChatwootMetricsCommand extends Command
{
    protected $signature = 'chatwoot:sync-metrics
                            {--account= : Specific account ID to sync}
                            {--force : Force sync even if recently synced}';

    protected $description = 'Sync metrics from Chatwoot for all active accounts';

    public function handle(): int
    {
        $this->info('Starting Chatwoot metrics synchronization...');

        $query = ChatwootAccount::active();

        // Si se especificó una cuenta específica
        if ($accountId = $this->option('account')) {
            $query->where('id', $accountId);
        }

        // Filtrar cuentas que necesitan sincronización
        if (!$this->option('force')) {
            $syncInterval = config('chatwoot.sync_interval', 3600); // segundos
            $query->needsSyncing($syncInterval / 60); // convertir a minutos
        }

        $accounts = $query->get();

        if ($accounts->isEmpty()) {
            $this->info('No accounts need syncing.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($accounts->count());
        $bar->start();

        foreach ($accounts as $account) {
            SyncChatwootMetricsJob::dispatch($account);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Dispatched sync jobs for {$accounts->count()} accounts.");

        return self::SUCCESS;
    }
}
