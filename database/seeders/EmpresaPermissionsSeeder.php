<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmpresaPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos para empresas
        $permisos = [
            'empresas.view',
            'empresas.create',
            'empresas.edit',
            'empresas.delete'
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Asignar permisos al rol de administrador
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo($permisos);

        // Opcional: crear rol específico para empresas
        $empresasRole = Role::firstOrCreate(['name' => 'gestor empresas']);
        $empresasRole->givePermissionTo($permisos);
    }
}
