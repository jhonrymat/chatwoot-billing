<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGatewayConfig;

class PaymentGatewayFactory
{
    protected static array $gateways = [
        'mercadopago' => MercadoPagoGateway::class,
        'stripe' => StripeGateway::class,
        'paypal' => PayPalGateway::class,
        // Fácil agregar más...
    ];

    /**
     * Obtener gateway por nombre
     */
    public static function make(string $gatewayName): PaymentGatewayInterface
    {
        if (!isset(self::$gateways[$gatewayName])) {
            throw new \InvalidArgumentException("Gateway {$gatewayName} no soportado");
        }

        $class = self::$gateways[$gatewayName];
        return app($class);
    }

    /**
     * Obtener gateway por defecto configurado
     */
    public static function default(): PaymentGatewayInterface
    {
        $defaultGateway = PaymentGatewayConfig::where('is_default', true)
            ->where('is_enabled', true)
            ->firstOrFail();

        return self::make($defaultGateway->gateway_name);
    }

    /**
     * Obtener gateway para un país específico
     */
    public static function forCountry(string $countryCode): PaymentGatewayInterface
    {
        $config = PaymentGatewayConfig::where('is_enabled', true)
            ->whereJsonContains('supported_countries', $countryCode)
            ->orderBy('is_default', 'desc')
            ->firstOrFail();

        return self::make($config->gateway_name);
    }

    /**
     * Listar todos los gateways disponibles
     */
    public static function available(): array
    {
        return PaymentGatewayConfig::where('is_enabled', true)->get()->toArray();
    }

    /**
     * Registrar un nuevo gateway (para extensibilidad)
     */
    public static function register(string $name, string $class): void
    {
        self::$gateways[$name] = $class;
    }
}
