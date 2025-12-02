<?php

namespace App\Console\Commands;

use App\Services\ChatwootService;
use Illuminate\Console\Command;

class TestChatwootConnectionCommand extends Command
{
    protected $signature = 'chatwoot:test-connection';
    protected $description = 'Test connection to Chatwoot API';

    public function handle(ChatwootService $chatwootService): int
    {
        $this->info('Testing Chatwoot connection...');
        $this->newLine();

        try {
            // Mostrar configuración
            $this->table(
                ['Setting', 'Value'],
                [
                    ['URL', config('chatwoot.url')],
                    ['API Key', substr(config('chatwoot.api_key'), 0, 10) . '...'],
                    ['Locale', config('chatwoot.default_locale')],
                    ['Timezone', config('chatwoot.default_timezone')],
                ]
            );

            $this->newLine();
            $this->info('Attempting to connect...');

            if ($chatwootService->testConnection()) {
                $this->info('✅ Connection successful!');
                return self::SUCCESS;
            } else {
                $this->error('❌ Connection failed.');
                return self::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error('❌ Connection failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
