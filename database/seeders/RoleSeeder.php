<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear cache de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Planes
            'view_plans',
            'create_plans',
            'edit_plans',
            'delete_plans',

            // Suscripciones
            'view_subscriptions',
            'view_any_subscriptions',
            'create_subscriptions',
            'edit_subscriptions',
            'cancel_subscriptions',

            // Pagos
            'view_payments',
            'view_any_payments',
            'refund_payments',

            // Usuarios
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // Cuentas Chatwoot
            'view_chatwoot_accounts',
            'view_any_chatwoot_accounts',
            'create_chatwoot_accounts',
            'suspend_chatwoot_accounts',

            // Métricas
            'view_metrics',
            'view_any_metrics',
            'sync_metrics',

            // Webhooks
            'view_webhooks',
            'retry_webhooks',

            // Activity Logs
            'view_activity_logs',

            // Dashboard
            'view_admin_dashboard',
            'view_subscriber_dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear rol Admin con todos los permisos
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Crear rol Subscriber con permisos limitados
        $subscriberRole = Role::create(['name' => 'subscriber']);
        $subscriberRole->givePermissionTo([
            'view_subscriptions',
            'view_payments',
            'view_chatwoot_accounts',
            'view_metrics',
            'view_subscriber_dashboard',
        ]);
    }
}
