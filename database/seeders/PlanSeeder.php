<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Plan Básico',
                'slug' => 'basico',
                'description' => 'Perfecto para pequeños negocios que están comenzando con atención al cliente',
                'price' => 49900.00,
                'currency' => 'COP',
                'billing_cycle' => 'monthly',
                'max_agents' => 3,
                'max_inboxes' => 2,
                'max_contacts' => 1000,
                'max_conversations_per_month' => 500,
                'features' => [
                    'Hasta 3 agentes',
                    '2 canales de comunicación',
                    '1,000 contactos',
                    '500 conversaciones/mes',
                    'Soporte por email',
                    'Reportes básicos',
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Plan Profesional',
                'slug' => 'profesional',
                'description' => 'Ideal para empresas en crecimiento que necesitan más capacidad y funciones',
                'price' => 99900.00,
                'currency' => 'COP',
                'billing_cycle' => 'monthly',
                'max_agents' => 10,
                'max_inboxes' => 5,
                'max_contacts' => 5000,
                'max_conversations_per_month' => 2000,
                'features' => [
                    'Hasta 10 agentes',
                    '5 canales de comunicación',
                    '5,000 contactos',
                    '2,000 conversaciones/mes',
                    'Soporte prioritario',
                    'Reportes avanzados',
                    'Integraciones API',
                    'Etiquetas personalizadas',
                ],
                'is_active' => true,
                'is_popular' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Plan Empresarial',
                'slug' => 'empresarial',
                'description' => 'Solución completa para grandes organizaciones con necesidades avanzadas',
                'price' => 199900.00,
                'currency' => 'COP',
                'billing_cycle' => 'monthly',
                'max_agents' => 50,
                'max_inboxes' => 20,
                'max_contacts' => 50000,
                'max_conversations_per_month' => null, // Ilimitado
                'features' => [
                    'Hasta 50 agentes',
                    '20 canales de comunicación',
                    '50,000 contactos',
                    'Conversaciones ilimitadas',
                    'Soporte 24/7',
                    'Reportes personalizados',
                    'API completa',
                    'Integraciones premium',
                    'SLA garantizado',
                    'Gestor de cuenta dedicado',
                    'Whitelabel disponible',
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::create($planData);
        }

        $this->command->info('✅ Planes creados exitosamente');
    }
}
