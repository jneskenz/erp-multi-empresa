<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LocalPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            'locales.view',
            'locales.create',
            'locales.edit',
            'locales.show',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web'
            ]);
        }

        // Asignar permisos al rol de administrador
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo($permisos);

        // Opcional: crear rol específico para locales
        $empresasRole = Role::firstOrCreate(['name' => 'gestor locales']);
        $empresasRole->givePermissionTo($permisos);

    }
}
