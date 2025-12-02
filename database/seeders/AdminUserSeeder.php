<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@chatwoot-billing.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'company_name' => 'Chatwoot Billing Admin',
        ]);

        // Asignar rol de admin
        $admin->assignRole('admin');

        $this->command->info('✅ Usuario administrador creado');
        $this->command->info('📧 Email: admin@chatwoot-billing.test');
        $this->command->info('🔑 Password: password');
        $this->command->warn('⚠️  Cambia esta contraseña en producción');
    }
}
