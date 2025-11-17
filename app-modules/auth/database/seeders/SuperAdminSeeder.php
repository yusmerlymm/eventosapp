<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario super administrador
        User::firstOrCreate(
            ['email' => 'admin@eventos.com'],
            [
                'name' => 'Super Administrador',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        echo "✓ Super Admin creado: admin@eventos.com / admin123\n";
    }
}
