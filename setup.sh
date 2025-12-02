#!/bin/bash

# ========================================
# FASE 1: INSTALACIÓN Y CONFIGURACIÓN INICIAL
# Chatwoot Billing System
# ========================================

echo "🚀 Iniciando instalación del proyecto..."

# ----------------------------------------
# 1. CREAR PROYECTO LARAVEL 12
# ----------------------------------------
echo "📦 Creando proyecto Laravel 12..."
composer create-project laravel/laravel chatwoot-billing "12.*"
cd chatwoot-billing

# ----------------------------------------
# 2. INSTALAR FILAMENT 4
# ----------------------------------------
echo "🎨 Instalando Filament 4..."
composer require filament/filament:"^4.0"

# Instalar panel de administración
php artisan filament:install --panels

# ----------------------------------------
# 3. INSTALAR DEPENDENCIAS
# ----------------------------------------
echo "📚 Instalando dependencias adicionales..."

# Spatie Permission para roles
composer require spatie/laravel-permission

# MercadoPago SDK
composer require mercadopago/dx-php

# Guzzle para API calls (ya viene en Laravel, pero por si acaso)
composer require guzzlehttp/guzzle

# Laravel Excel para exportar facturas (opcional)
composer require maatwebsite/excel

# Spatie Laravel Backup (opcional pero recomendado)
composer require spatie/laravel-backup

# ----------------------------------------
# 4. CONFIGURAR BASE DE DATOS
# ----------------------------------------
echo "🗄️ Configurando base de datos..."

# Crear archivo .env si no existe
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

# Agregar variables de entorno al .env
cat >> .env << 'EOF'

# ========================================
# CHATWOOT CONFIGURATION
# ========================================
CHATWOOT_URL=https://tu-chatwoot.com
CHATWOOT_API_KEY=tu_api_key_super_admin
CHATWOOT_AUTO_SYNC_METRICS=true
CHATWOOT_SYNC_INTERVAL=3600

# ========================================
# MERCADOPAGO CONFIGURATION
# ========================================
MERCADOPAGO_PUBLIC_KEY=APP_USR-xxxxxxxxx
MERCADOPAGO_ACCESS_TOKEN=APP_USR-xxxxxxxxx
MERCADOPAGO_WEBHOOK_SECRET=
MERCADOPAGO_PRODUCTION_MODE=false

# ========================================
# APP CONFIGURATION
# ========================================
APP_LOCALE=es
APP_TIMEZONE=America/Bogota
APP_FALLBACK_LOCALE=en

# ========================================
# QUEUE CONFIGURATION
# ========================================
QUEUE_CONNECTION=database

# ========================================
# MAIL CONFIGURATION
# ========================================
MAIL_FROM_ADDRESS=noreply@tuapp.com
MAIL_FROM_NAME="${APP_NAME}"

# ========================================
# FEATURE FLAGS
# ========================================
ENABLE_TRIAL_PERIOD=false
TRIAL_DAYS=7
ENABLE_PLAN_CHANGES=true
ENABLE_CANCELLATIONS=true
EOF

echo "✅ Variables de entorno agregadas al .env"

# ----------------------------------------
# 5. PUBLICAR CONFIGURACIONES
# ----------------------------------------
echo "📄 Publicando configuraciones..."

# Publicar configuración de Spatie Permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Publicar configuración de Filament
php artisan vendor:publish --tag=filament-config

# ----------------------------------------
# 6. CREAR ESTRUCTURA DE DIRECTORIOS
# ----------------------------------------
echo "📁 Creando estructura de directorios..."

# Servicios
mkdir -p app/Services

# Observers
mkdir -p app/Observers

# Enums (para estados)
mkdir -p app/Enums

# Jobs
mkdir -p app/Jobs

# Traits
mkdir -p app/Traits

# DTOs (Data Transfer Objects)
mkdir -p app/DTOs

# Filament Pages personalizadas
mkdir -p app/Filament/Pages

# Filament Widgets
mkdir -p app/Filament/Widgets

# Http Controllers
mkdir -p app/Http/Controllers/Api
mkdir -p app/Http/Controllers/Webhook

# Middleware personalizado
mkdir -p app/Http/Middleware

# Requests
mkdir -p app/Http/Requests

# Resources
mkdir -p app/Http/Resources

echo "✅ Estructura de directorios creada"

# ----------------------------------------
# 7. CREAR ARCHIVO DE CONFIGURACIÓN PERSONALIZADO
# ----------------------------------------
echo "⚙️ Creando archivo de configuración personalizado..."

cat > config/chatwoot.php << 'EOF'
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
EOF

cat > config/mercadopago.php << 'EOF'
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
EOF

cat > config/billing.php << 'EOF'
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
EOF

echo "✅ Archivos de configuración creados"

# ----------------------------------------
# 8. CREAR .ENV.EXAMPLE ACTUALIZADO
# ----------------------------------------
echo "📝 Actualizando .env.example..."

cat > .env.example << 'EOF'
APP_NAME="Chatwoot Billing"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=America/Bogota
APP_URL=http://localhost
APP_LOCALE=es
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chatwoot_billing
DB_USERNAME=root
DB_PASSWORD=

# Chatwoot Configuration
CHATWOOT_URL=https://tu-chatwoot.com
CHATWOOT_API_KEY=tu_api_key_super_admin
CHATWOOT_AUTO_SYNC_METRICS=true
CHATWOOT_SYNC_INTERVAL=3600

# MercadoPago Configuration
MERCADOPAGO_PUBLIC_KEY=APP_USR-xxxxxxxxx
MERCADOPAGO_ACCESS_TOKEN=APP_USR-xxxxxxxxx
MERCADOPAGO_WEBHOOK_SECRET=
MERCADOPAGO_PRODUCTION_MODE=false

# Queue Configuration
QUEUE_CONNECTION=database

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@tuapp.com
MAIL_FROM_NAME="${APP_NAME}"

# Feature Flags
ENABLE_TRIAL_PERIOD=false
TRIAL_DAYS=7
ENABLE_PLAN_CHANGES=true
ENABLE_CANCELLATIONS=true
EOF

echo "✅ .env.example actualizado"

# ----------------------------------------
# 9. CREAR COMPOSER.JSON SCRIPTS
# ----------------------------------------
echo "📦 Agregando scripts útiles al composer.json..."

# Aquí normalmente editarías composer.json, pero lo dejamos para hacerlo manual
echo "⚠️  NOTA: Agrega estos scripts manualmente a composer.json:"
cat << 'EOF'

"scripts": {
    "setup": [
        "@php artisan migrate:fresh --seed",
        "@php artisan storage:link",
        "@php artisan optimize:clear"
    ],
    "reset-db": [
        "@php artisan migrate:fresh --seed"
    ],
    "test": [
        "@php artisan test"
    ]
}
EOF

# ----------------------------------------
# 10. CREAR README.md INICIAL
# ----------------------------------------
echo "📖 Creando README.md..."

cat > README.md << 'EOF'
# 🚀 Chatwoot Billing System

Sistema de facturación y gestión de suscripciones para Chatwoot autohospedado con integración de MercadoPago Colombia.

## 🌟 Características

- ✅ Gestión completa de planes de suscripción
- ✅ Integración con MercadoPago Colombia
- ✅ Creación automática de cuentas en Chatwoot
- ✅ Dashboard de métricas para suscriptores
- ✅ Panel administrativo completo con Filament 4
- ✅ Gestión de métodos de pago
- ✅ Sistema de roles (Admin y Suscriptor)
- ✅ Credenciales unificadas Laravel + Chatwoot

## 📋 Requisitos

- PHP 8.2+
- MySQL 8.0+
- Composer 2.x
- Laravel 12
- Chatwoot autohospedado con acceso API
- Cuenta de MercadoPago Colombia

## 🔧 Instalación

1. Clonar el repositorio
2. Copiar `.env.example` a `.env`
3. Configurar las variables de entorno:
   - Base de datos
   - Chatwoot URL y API Key
   - MercadoPago credenciales
4. Instalar dependencias: `composer install`
5. Generar key: `php artisan key:generate`
6. Migrar base de datos: `php artisan migrate --seed`
7. Crear usuario admin: `php artisan make:filament-user`

## ⚙️ Configuración

### Chatwoot

Debes obtener un API Key de super administrador desde tu instalación de Chatwoot:
1. Ingresa a Chatwoot como super admin
2. Ve a Configuración > Integraciones > API
3. Genera un nuevo token
4. Copia el token en `CHATWOOT_API_KEY`

### MercadoPago

1. Crea una aplicación en https://www.mercadopago.com.co/developers
2. Obtén tus credenciales de prueba/producción
3. Configura el webhook en MercadoPago apuntando a: `https://tudominio.com/webhook/mercadopago`

## 📚 Documentación

En desarrollo...

## 🤝 Contribuciones

Este es un proyecto de código abierto. Las contribuciones son bienvenidas.

## 📄 Licencia

MIT License
EOF

echo "✅ README.md creado"

# ----------------------------------------
# 11. CREAR GITIGNORE PERSONALIZADO
# ----------------------------------------
echo "📝 Actualizando .gitignore..."

cat >> .gitignore << 'EOF'

# Configuración local
/config/local.php

# Backups
/storage/backups/
*.sql
*.sql.gz

# IDE
.phpunit.result.cache
.php-cs-fixer.cache
EOF

# ----------------------------------------
# FINALIZACIÓN
# ----------------------------------------
echo ""
echo "========================================="
echo "✅ FASE 1 COMPLETADA"
echo "========================================="
echo ""
echo "📋 Próximos pasos:"
echo ""
echo "1. Configura tu archivo .env con las credenciales correctas"
echo "2. Crea la base de datos: CREATE DATABASE chatwoot_billing;"
echo "3. Ejecuta las migraciones: php artisan migrate"
echo "4. Crea un usuario admin: php artisan make:filament-user"
echo ""
echo "🔧 Comandos útiles:"
echo "  - php artisan serve (iniciar servidor)"
echo "  - php artisan queue:work (procesar jobs)"
echo "  - php artisan optimize:clear (limpiar cache)"
echo ""
echo "📂 Estructura creada:"
echo "  - app/Services/ (para lógica de negocio)"
echo "  - app/Enums/ (para estados y constantes)"
echo "  - app/Jobs/ (para trabajos en cola)"
echo "  - app/Http/Controllers/Webhook/ (para webhooks)"
echo ""
echo "⚠️  IMPORTANTE: Revisa el .env y configura:"
echo "  - CHATWOOT_URL y CHATWOOT_API_KEY"
echo "  - MERCADOPAGO_PUBLIC_KEY y MERCADOPAGO_ACCESS_TOKEN"
echo "  - Configuración de correo (MAIL_*)"
echo ""
echo "========================================="