<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o actualizar el superusuario
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@erpmultisoft.com'], // Buscar por email
            [
                'name' => 'Super Administrador',
                'email' => 'superadmin@erpmultisoft.com',
                'password' => Hash::make('SuperAdmin123!'), // Cambiar por una contraseña segura
                'is_super_admin' => true,
                'email_verified_at' => now(), // IMPORTANTE: Email verificado
            ]
        );

        // Asegurar que el email esté verificado
        if (!$superAdmin->hasVerifiedEmail()) {
            $superAdmin->markEmailAsVerified();
        }

        $this->command->info('✅ Superusuario creado exitosamente:');
        $this->command->info('📧 Email: superadmin@erpmultisoft.com');
        $this->command->info('🔐 Password: SuperAdmin123!');
        $this->command->warn('⚠️  IMPORTANTE: Cambia la contraseña después del primer login');
        
        // También crear un usuario admin normal para comparación
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@erpmultisoft.com'],
            [
                'name' => 'Administrador Normal',
                'email' => 'admin@erpmultisoft.com', 
                'password' => Hash::make('Admin123!'),
                'is_super_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        // Asignar rol de Admin al usuario normal
        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }

        $this->command->info('✅ Usuario admin normal también creado:');
        $this->command->info('📧 Email: admin@erpmultisoft.com');
        $this->command->info('🔐 Password: Admin123!');
    }
}
