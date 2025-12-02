<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MercadoPago Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la integración con MercadoPago Colombia
    |
    */

    'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
    'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
    'webhook_secret' => env('MERCADOPAGO_WEBHOOK_SECRET'),
    
    'production_mode' => env('MERCADOPAGO_PRODUCTION_MODE', false),
    
    'currency' => 'COP',
    'locale' => 'es-CO',
    
    // URLs de retorno
    'urls' => [
        'success' => env('APP_URL') . '/payment/success',
        'failure' => env('APP_URL') . '/payment/failure',
        'pending' => env('APP_URL') . '/payment/pending',
    ],
    
    // Configuración de webhooks
    'webhook' => [
        'events' => [
            'payment.created',
            'payment.updated',
            'subscription.authorized',
            'subscription.paused',
            'subscription.cancelled',
        ],
    ],
];
