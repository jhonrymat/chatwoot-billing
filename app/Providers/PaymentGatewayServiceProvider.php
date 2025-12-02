<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PaymentGateways\PaymentGatewayFactory;

class PaymentGatewayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registrar gateways personalizados desde config
        $customGateways = config('payment-gateways.custom', []);

        foreach ($customGateways as $name => $class) {
            PaymentGatewayFactory::register($name, $class);
        }
    }

    public function boot(): void
    {
        // Publicar configuración
        $this->publishes([
            __DIR__.'/../../config/payment-gateways.php' => config_path('payment-gateways.php'),
        ], 'payment-gateways');
    }
}
