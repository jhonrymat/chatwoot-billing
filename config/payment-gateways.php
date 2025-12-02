<?php

return [
    'default' => env('PAYMENT_GATEWAY_DEFAULT', 'mercadopago'),

    'gateways' => [
        'mercadopago' => [
            'enabled' => env('MERCADOPAGO_ENABLED', true),
            'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
            'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
            'countries' => ['AR', 'BR', 'CL', 'CO', 'MX', 'PE', 'UY'],
        ],

        'stripe' => [
            'enabled' => env('STRIPE_ENABLED', false),
            'public_key' => env('STRIPE_PUBLIC_KEY'),
            'secret_key' => env('STRIPE_SECRET_KEY'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'countries' => ['US', 'CA', 'GB', 'DE', 'FR', 'ES'],
        ],

        'paypal' => [
            'enabled' => env('PAYPAL_ENABLED', false),
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
            'countries' => ['US', 'CA', 'GB', 'AU'],
        ],
    ],

    // Permitir gateways personalizados
    'custom' => [
        // 'custom_gateway' => App\CustomGateways\MyGateway::class,
    ],
];
