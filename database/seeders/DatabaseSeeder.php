<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PlanSeeder::class,
            AdminUserSeeder::class,
            PaymentGatewaySeeder::class, // Nuevo
        ]);

        // Descomentar solo en desarrollo para datos de prueba
        if (app()->environment('local')) {
            $this->call([
                DemoDataSeeder::class,
            ]);
        }

        $this->command->info('🎉 Base de datos inicializada correctamente');
    }
}
