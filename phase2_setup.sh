#!/bin/bash

# ========================================
# FASE 2: MIGRACIONES, MODELOS Y SEEDERS
# Chatwoot Billing System
# ========================================

echo "🚀 Iniciando Fase 2: Base de Datos y Modelos..."
echo ""

# ----------------------------------------
# 1. CREAR MIGRACIONES
# ----------------------------------------
echo "📝 Creando migraciones..."

php artisan make:migration create_plans_table
php artisan make:migration create_subscriptions_table
php artisan make:migration create_payment_methods_table
php artisan make:migration create_payments_table
php artisan make:migration create_chatwoot_accounts_table
php artisan make:migration create_chatwoot_metrics_table
php artisan make:migration create_webhook_logs_table
php artisan make:migration create_activity_logs_table

echo "✅ Migraciones creadas"
echo ""

# ----------------------------------------
# 2. CREAR MODELOS
# ----------------------------------------
echo "📦 Creando modelos..."

php artisan make:model Plan
php artisan make:model Subscription
php artisan make:model PaymentMethod
php artisan make:model Payment
php artisan make:model ChatwootAccount
php artisan make:model ChatwootMetric
php artisan make:model WebhookLog
php artisan make:model ActivityLog

echo "✅ Modelos creados"
echo ""

# ----------------------------------------
# 3. CREAR ENUMS
# ----------------------------------------
echo "🏷️  Creando Enums..."

# Crear directorio de Enums si no existe
mkdir -p app/Enums

# Crear archivos de enums
touch app/Enums/SubscriptionStatus.php
touch app/Enums/PaymentStatus.php
touch app/Enums/PaymentMethodType.php
touch app/Enums/ChatwootAccountStatus.php
touch app/Enums/BillingCycle.php
touch app/Enums/WebhookStatus.php
touch app/Enums/ActivityAction.php

echo "✅ Archivos de Enums creados"
echo ""

# ----------------------------------------
# 4. CREAR SEEDERS
# ----------------------------------------
echo "🌱 Creando seeders..."

php artisan make:seeder RoleSeeder
php artisan make:seeder PlanSeeder
php artisan make:seeder AdminUserSeeder
php artisan make:seeder DemoDataSeeder

echo "✅ Seeders creados"
echo ""

# ----------------------------------------
# 5. PUBLICAR MIGRACIÓN DE SPATIE
# ----------------------------------------
echo "📄 Publicando migración de Spatie Permission..."

php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

echo "✅ Migración de Spatie publicada"
echo ""

# ----------------------------------------
# INFORMACIÓN IMPORTANTE
# ----------------------------------------
echo "========================================="
echo "⚠️  PASOS MANUALES NECESARIOS"
echo "========================================="
echo ""
echo "Ahora debes:"
echo ""
echo "1️⃣  COPIAR EL CONTENIDO de cada migración desde los artefactos"
echo "   Ubicación: database/migrations/"
echo ""
echo "2️⃣  COPIAR EL CONTENIDO de cada modelo desde los artefactos"
echo "   Ubicación: app/Models/"
echo ""
echo "3️⃣  COPIAR EL CONTENIDO de cada Enum desde los artefactos"
echo "   Ubicación: app/Enums/"
echo ""
echo "4️⃣  COPIAR EL CONTENIDO de cada seeder desde los artefactos"
echo "   Ubicación: database/seeders/"
echo ""
echo "5️⃣  ACTUALIZAR el modelo User en app/Models/User.php"
echo ""
echo "========================================="
echo ""
echo "📋 Orden de archivos a copiar:"
echo ""
echo "MIGRACIONES (en orden):"
echo "  1. xxxx_create_plans_table.php"
echo "  2. xxxx_create_subscriptions_table.php"
echo "  3. xxxx_create_payment_methods_table.php"
echo "  4. xxxx_create_payments_table.php"
echo "  5. xxxx_create_chatwoot_accounts_table.php"
echo "  6. xxxx_create_chatwoot_metrics_table.php"
echo "  7. xxxx_create_webhook_logs_table.php"
echo "  8. xxxx_create_activity_logs_table.php"
echo ""
echo "MODELOS:"
echo "  • app/Models/Plan.php"
echo "  • app/Models/Subscription.php"
echo "  • app/Models/PaymentMethod.php"
echo "  • app/Models/Payment.php"
echo "  • app/Models/ChatwootAccount.php"
echo "  • app/Models/ChatwootMetric.php"
echo "  • app/Models/WebhookLog.php"
echo "  • app/Models/ActivityLog.php"
echo "  • app/Models/User.php (actualizar)"
echo ""
echo "ENUMS:"
echo "  • app/Enums/SubscriptionStatus.php"
echo "  • app/Enums/PaymentStatus.php"
echo "  • app/Enums/PaymentMethodType.php"
echo "  • app/Enums/ChatwootAccountStatus.php"
echo "  • app/Enums/BillingCycle.php"
echo "  • app/Enums/WebhookStatus.php"
echo "  • app/Enums/ActivityAction.php"
echo ""
echo "SEEDERS:"
echo "  • database/seeders/RoleSeeder.php"
echo "  • database/seeders/PlanSeeder.php"
echo "  • database/seeders/AdminUserSeeder.php"
echo "  • database/seeders/DemoDataSeeder.php"
echo "  • database/seeders/DatabaseSeeder.php (actualizar)"
echo ""
echo "========================================="
echo ""
echo "Una vez hayas copiado todo el contenido, ejecuta:"
echo ""
echo "  php artisan migrate:fresh --seed"
echo ""
echo "Esto creará todas las tablas y datos iniciales."
echo ""
echo "========================================="
echo ""
echo "🔐 CREDENCIALES POR DEFECTO:"
echo "========================================="
echo ""
echo "Admin Panel:"
echo "  Email: admin@chatwoot-billing.test"
echo "  Password: password"
echo ""
echo "Usuario Demo 1 (si ejecutas DemoDataSeeder):"
echo "  Email: juan@example.com"
echo "  Password: password"
echo ""
echo "Usuario Demo 2 (si ejecutas DemoDataSeeder):"
echo "  Email: maria@example.com"
echo "  Password: password"
echo ""
echo "========================================="
echo ""
echo "📊 RESUMEN DE LA BASE DE DATOS:"
echo "========================================="
echo ""
echo "Tablas principales: 8"
echo "  ✅ plans (Planes de suscripción)"
echo "  ✅ subscriptions (Suscripciones de usuarios)"
echo "  ✅ payment_methods (Métodos de pago)"
echo "  ✅ payments (Historial de pagos)"
echo "  ✅ chatwoot_accounts (Cuentas en Chatwoot)"
echo "  ✅ chatwoot_metrics (Métricas cacheadas)"
echo "  ✅ webhook_logs (Logs de webhooks)"
echo "  ✅ activity_logs (Auditoría)"
echo ""
echo "Tablas de Spatie Permission: 6"
echo "  ✅ roles"
echo "  ✅ permissions"
echo "  ✅ model_has_roles"
echo "  ✅ model_has_permissions"
echo "  ✅ role_has_permissions"
echo ""
echo "Modelos Eloquent: 9"
echo "Enums: 7"
echo "Roles: 2 (admin, subscriber)"
echo "Planes iniciales: 3 (Básico, Profesional, Empresarial)"
echo ""
echo "========================================="
echo ""
echo "🎯 PRÓXIMOS PASOS (Fase 3):"
echo "========================================="
echo ""
echo "1. Integración con Chatwoot API"
echo "2. Servicio de MercadoPago"
echo "3. Controladores de Webhooks"
echo "4. Jobs para procesamiento en cola"
echo ""
echo "========================================="
echo ""
echo "✅ FASE 2 - PREPARACIÓN COMPLETADA"
echo ""
echo "Ahora copia el contenido de los artefactos y ejecuta:"
echo "php artisan migrate:fresh --seed"
echo ""
echo "========================================="