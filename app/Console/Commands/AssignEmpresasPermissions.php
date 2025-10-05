<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignEmpresasPermissions extends Command
{
    protected $signature = 'assign:empresas-permissions';
    protected $description = 'Asigna permisos de empresas al rol admin';

    public function handle()
    {
        try {
            // Obtener el rol admin
            $admin = Role::where('name', 'admin')->first();
            
            if (!$admin) {
                $this->error('Rol admin no encontrado');
                return;
            }
            
            // Permisos a asignar
            $permissions = ['empresas.view', 'empresas.create', 'empresas.edit', 'empresas.delete'];
            
            foreach ($permissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && !$admin->hasPermissionTo($permission)) {
                    $admin->givePermissionTo($permission);
                    $this->info("Permiso {$permissionName} asignado al admin");
                } else {
                    $this->line("Permiso {$permissionName} ya existe o no se pudo crear");
                }
            }
            
            $this->info('Proceso completado');
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}