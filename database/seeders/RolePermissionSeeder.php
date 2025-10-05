<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            'empresas.view',
            'empresas.create',
            'empresas.edit',
            'empresas.delete',
            'dashboard.access',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);
        $userRole = Role::create(['name' => 'user']);

        // Asignar permisos a roles
        $adminRole->givePermissionTo(Permission::all());
        
        $managerRole->givePermissionTo([
            'users.view',
            'users.create',
            'users.edit',
            'dashboard.access',
        ]);
        
        $userRole->givePermissionTo([
            'dashboard.access',
        ]);

        // Crear usuario administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@erpms.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('admin');

        // Crear usuario manager
        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@erpms.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $manager->assignRole('manager');

        // Crear usuario normal
        $user = User::create([
            'name' => 'Usuario Normal',
            'email' => 'user@erpms.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('user');
    }
}
