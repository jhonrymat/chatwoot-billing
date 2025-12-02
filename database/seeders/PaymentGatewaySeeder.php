<?php

namespace Database\Seeders;

use App\Models\PaymentGatewayConfig;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        // MercadoPago (habilitado por defecto)
        PaymentGatewayConfig::create([
            'gateway_name' => 'mercadopago',
            'display_name' => 'MercadoPago',
            'is_enabled' => true,
            'is_default' => true,
            'credentials' => [
                'public_key' => config('mercadopago.public_key'),
                'access_token' => config('mercadopago.access_token'),
            ],
            'settings' => [
                'production_mode' => config('mercadopago.production_mode', false),
                'currency' => 'COP',
                'locale' => 'es-CO',
            ],
            'supported_countries' => ['AR', 'BR', 'CL', 'CO', 'MX', 'PE', 'UY'],
            'description' => 'Pasarela de pagos para América Latina. Acepta tarjetas de crédito, débito, PSE y efectivo.',
            'logo_url' => 'https://http2.mlstatic.com/frontend-assets/ui-navigation/5.18.5/mercadopago/logo__large.png',
        ]);

        // Stripe (deshabilitado por defecto, listo para habilitar)
        PaymentGatewayConfig::create([
            'gateway_name' => 'stripe',
            'display_name' => 'Stripe',
            'is_enabled' => false,
            'is_default' => false,
            'credentials' => [
                'public_key' => config('stripe.public_key'),
                'secret_key' => config('stripe.secret_key'),
                'webhook_secret' => config('stripe.webhook_secret'),
            ],
            'settings' => [
                'currency' => 'USD',
            ],
            'supported_countries' => ['US', 'CA', 'GB', 'AU', 'NZ', 'DE', 'FR', 'ES', 'IT', 'NL'],
            'description' => 'Pasarela de pagos internacional. Ideal para Estados Unidos y Europa.',
            'logo_url' => 'https://images.ctfassets.net/fzn2n1nzq965/3AGidihOJl4nH9D1vDjM84/9540155d584be52fc54c443b6efa4ae6/stripe.png',
        ]);

        // PayPal (deshabilitado, para futuro)
        PaymentGatewayConfig::create([
            'gateway_name' => 'paypal',
            'display_name' => 'PayPal',
            'is_enabled' => false,
            'is_default' => false,
            'credentials' => [
                'client_id' => config('paypal.client_id'),
                'secret' => config('paypal.secret'),
            ],
            'settings' => [
                'mode' => 'sandbox',
            ],
            'supported_countries' => ['US', 'CA', 'GB', 'AU', 'MX', 'BR'],
            'description' => 'PayPal - Pagos seguros en todo el mundo.',
            'logo_url' => 'https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_111x69.jpg',
        ]);

        $this->command->info('✅ Payment gateways configurados');
        $this->command->info('📌 Gateway por defecto: MercadoPago (habilitado)');
        $this->command->info('📌 Gateways adicionales: Stripe, PayPal (deshabilitados)');
    }
}
