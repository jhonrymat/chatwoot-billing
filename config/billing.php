<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Billing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración general del sistema de facturación
    |
    */

    // Prueba gratuita
    'trial' => [
        'enabled' => env('ENABLE_TRIAL_PERIOD', false),
        'days' => env('TRIAL_DAYS', 7),
    ],
    
    // Cambio de planes
    'plan_changes' => [
        'enabled' => env('ENABLE_PLAN_CHANGES', true),
        'proration' => true,
    ],
    
    // Cancelaciones
    'cancellations' => [
        'enabled' => env('ENABLE_CANCELLATIONS', true),
        'immediate' => false, // Si es false, se cancela al final del periodo
    ],
    
    // Renovaciones
    'renewals' => [
        'remind_days_before' => 3,
        'retry_failed_payments' => true,
        'retry_attempts' => 3,
        'retry_delay_days' => 3,
    ],
    
    // Suspensión de cuentas
    'suspension' => [
        'grace_period_days' => 5,
        'delete_after_days' => 30, // Después de la suspensión
    ],
];
