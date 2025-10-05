<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SedesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos para Sedes
        $permissions = [
            'sedes.view' => 'Ver sedes',
            'sedes.create' => 'Crear sedes',
            'sedes.edit' => 'Editar sedes',
            'sedes.delete' => 'Eliminar sedes',
        ];

        foreach ($permissions as $permission => $description) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Asignar permisos al rol Admin (si existe)
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(array_keys($permissions));
        }

        // Asignar permisos básicos al rol User (si existe)
        $userRole = Role::where('name', 'User')->first();
        if ($userRole) {
            $userRole->givePermissionTo(['sedes.view']);
        }

        $this->command->info('Permisos de Sedes creados exitosamente.');
    }
}
