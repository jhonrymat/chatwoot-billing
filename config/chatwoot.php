<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chatwoot Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la integración con Chatwoot autohospedado
    |
    */

    'url' => env('CHATWOOT_URL'),
    'api_key' => env('CHATWOOT_API_KEY'),
    
    'auto_sync_metrics' => env('CHATWOOT_AUTO_SYNC_METRICS', true),
    'sync_interval' => env('CHATWOOT_SYNC_INTERVAL', 3600), // segundos
    
    'default_locale' => env('APP_LOCALE', 'es'),
    'default_timezone' => env('APP_TIMEZONE', 'America/Bogota'),
    
    // Configuración para creación de cuentas
    'account' => [
        'domain_suffix' => env('CHATWOOT_ACCOUNT_DOMAIN_SUFFIX', ''),
        'auto_create_inbox' => true,
        'default_inbox_type' => 'web',
    ],
    
    // Configuración para usuarios
    'user' => [
        'role' => 'administrator',
        'auto_invite' => false,
    ],
];
