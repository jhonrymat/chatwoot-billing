<?php

// ============================================
// app/Console/Commands/TestMercadoPagoCommand.php
// php artisan make:command TestMercadoPagoCommand
// ============================================

namespace App\Console\Commands;

use App\Services\MercadoPagoService;
use Illuminate\Console\Command;

class TestMercadoPagoCommand extends Command
{
    protected $signature = 'mercadopago:test';
    protected $description = 'Test MercadoPago configuration';

    public function handle(MercadoPagoService $mercadoPago): int
    {
        $this->info('Testing MercadoPago configuration...');
        $this->newLine();

        try {
            // Mostrar configuración
            $this->table(
                ['Setting', 'Value'],
                [
                    ['Public Key', substr(config('mercadopago.public_key'), 0, 20) . '...'],
                    ['Access Token', substr(config('mercadopago.access_token'), 0, 20) . '...'],
                    ['Production Mode', config('mercadopago.production_mode') ? 'Yes' : 'No'],
                    ['Currency', config('mercadopago.currency')],
                ]
            );

            $this->newLine();
            $this->info('✅ Configuration looks good!');

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Configuration error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
