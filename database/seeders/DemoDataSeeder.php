<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Este seeder crea datos de demostración para testing
     * NO ejecutar en producción
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->error('❌ No se puede ejecutar en producción');
            return;
        }

        // Crear usuarios de prueba
        $subscriber1 = User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'company_name' => 'Empresa Demo SA',
            'phone' => '3001234567',
        ]);
        $subscriber1->assignRole('subscriber');

        $subscriber2 = User::create([
            'name' => 'María García',
            'email' => 'maria@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'company_name' => 'Startups Colombia',
            'phone' => '3007654321',
        ]);
        $subscriber2->assignRole('subscriber');

        // Obtener planes
        $basicPlan = Plan::where('slug', 'basico')->first();
        $proPlan = Plan::where('slug', 'profesional')->first();

        // Crear suscripción activa para subscriber1
        $subscription1 = Subscription::create([
            'user_id' => $subscriber1->id,
            'plan_id' => $proPlan->id,
            'status' => 'active',
            'started_at' => now()->subDays(10),
            'next_billing_date' => now()->addDays(20),
            'gateway_subscription_id' => 'demo_sub_' . uniqid(),
        ]);

        // Crear pago aprobado para subscription1
        Payment::create([
            'subscription_id' => $subscription1->id,
            'user_id' => $subscriber1->id,
            'amount' => $proPlan->price,
            'currency' => 'COP',
            'status' => 'approved',
            'gateway_payment_id' => 'demo_pay_' . uniqid(),
            'paid_at' => now()->subDays(10),
        ]);

        // Crear suscripción pendiente para subscriber2
        $subscription2 = Subscription::create([
            'user_id' => $subscriber2->id,
            'plan_id' => $basicPlan->id,
            'status' => 'pending',
            'gateway_subscription_id' => 'demo_sub_' . uniqid(),
        ]);

        $this->command->info('✅ Datos de demostración creados');
        $this->command->info('👤 Suscriptor 1: juan@example.com / password');
        $this->command->info('👤 Suscriptor 2: maria@example.com / password');
    }
}
