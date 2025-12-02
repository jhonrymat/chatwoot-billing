<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_gateway_configs', function (Blueprint $table) {
            $table->id();

            // Identificación del gateway
            $table->string('gateway_name', 50)->unique();
            $table->string('display_name', 100);

            // Estado
            $table->boolean('is_enabled')->default(false);
            $table->boolean('is_default')->default(false);

            // Credenciales (encriptadas)
            $table->json('credentials')->nullable()->comment('API keys y secrets encriptados');

            // Configuración adicional
            $table->json('settings')->nullable()->comment('Configuraciones específicas del gateway');

            // Países soportados
            $table->json('supported_countries')->nullable()->comment('Array de códigos ISO');

            // Información adicional
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();

            $table->timestamps();

            // Índices
            $table->index('gateway_name');
            $table->index('is_enabled');
            $table->index('is_default');
        });

        // DB::table('payment_gateway_configs')->insert([
        //     'gateway_name' => 'mercadopago',
        //     'display_name' => 'MercadoPago',
        //     'is_enabled' => true,
        //     'is_default' => true,
        //     'credentials' => json_encode([
        //         'public_key' => config('mercadopago.public_key'),
        //         'access_token' => config('mercadopago.access_token'),
        //     ]),
        //     'settings' => json_encode([
        //         'production_mode' => config('mercadopago.production_mode', false),
        //         'currency' => 'COP',
        //         'locale' => 'es-CO',
        //     ]),
        //     'supported_countries' => json_encode(['AR', 'BR', 'CL', 'CO', 'MX', 'PE', 'UY']),
        //     'description' => 'Pasarela de pagos para América Latina',
        //     'logo_url' => 'https://http2.mlstatic.com/frontend-assets/ui-navigation/5.18.5/mercadopago/logo__large.png',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_configs');
    }
};
