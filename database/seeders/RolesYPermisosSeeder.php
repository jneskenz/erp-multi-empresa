<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesYPermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Resetear cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ==================== CREAR PERMISOS ====================
        
        $permisos = [
            // Gestión de grupos empresariales
            'ver_grupos',
            'crear_grupos',
            'editar_grupos',
            'eliminar_grupos',
            'activar_grupos',
            'suspender_grupos',
            'cambiar_plan_grupos',
            
            // Gestión de empresas
            'ver_empresas',
            'crear_empresas',
            'editar_empresas',
            'eliminar_empresas',
            'activar_empresas',
            'configurar_empresa',
            
            // Gestión de sedes
            'ver_sedes',
            'crear_sedes',
            'editar_sedes',
            'eliminar_sedes',
            
            // Gestión de locales
            'ver_locales',
            'crear_locales',
            'editar_locales',
            'eliminar_locales',
            
            // Gestión de usuarios
            'ver_usuarios',
            'crear_usuarios',
            'editar_usuarios',
            'eliminar_usuarios',
            'activar_usuarios',
            'desactivar_usuarios',
            'asignar_roles',
            'asignar_empresas',
            
            // Gestión de roles y permisos
            'ver_roles',
            'crear_roles',
            'editar_roles',
            'eliminar_roles',
            'asignar_permisos',
            
            // Módulo: Ventas
            'ver_ventas',
            'crear_ventas',
            'editar_ventas',
            'anular_ventas',
            'ver_cotizaciones',
            'crear_cotizaciones',
            'aprobar_cotizaciones',
            
            // Módulo: Inventario
            'ver_inventario',
            'crear_productos',
            'editar_productos',
            'eliminar_productos',
            'ajustar_stock',
            'transferir_stock',
            
            // Módulo: Compras
            'ver_compras',
            'crear_compras',
            'aprobar_compras',
            'recibir_compras',
            'gestionar_proveedores',
            
            // Módulo: Clientes (CRM)
            'ver_clientes',
            'crear_clientes',
            'editar_clientes',
            'eliminar_clientes',
            'ver_oportunidades',
            'gestionar_oportunidades',
            
            // Módulo: Contabilidad
            'ver_contabilidad',
            'crear_asientos',
            'editar_asientos',
            'cerrar_periodos',
            'ver_reportes_financieros',
            
            // Reportes
            'ver_reportes',
            'exportar_reportes',
            'ver_dashboard',
            
            // Configuración
            'ver_configuracion',
            'editar_configuracion',
            'ver_logs',
            'ver_actividad',
        ];
        
        foreach ($permisos as $permiso) {
            Permission::create(['name' => $permiso]);
        }
        
        // ==================== CREAR ROLES ====================
        
        // 1. SUPERUSUARIO (acceso total al sistema)
        $rolSuperusuario = Role::create(['name' => 'superusuario']);
        $rolSuperusuario->givePermissionTo(Permission::all());
        
        // 2. PROPIETARIO (dueño de grupo empresarial)
        $rolPropietario = Role::create(['name' => 'propietario']);
        $rolPropietario->givePermissionTo([
            // Gestión completa del grupo
            'ver_empresas', 'crear_empresas', 'editar_empresas', 'activar_empresas', 'configurar_empresa',
            'ver_sedes', 'crear_sedes', 'editar_sedes',
            'ver_locales', 'crear_locales', 'editar_locales',
            'ver_usuarios', 'crear_usuarios', 'editar_usuarios', 'activar_usuarios', 'desactivar_usuarios',
            'asignar_roles', 'asignar_empresas',
            'ver_roles', 'crear_roles', 'editar_roles', 'asignar_permisos',
            'ver_configuracion', 'editar_configuracion',
            'ver_reportes', 'exportar_reportes', 'ver_dashboard',
            'ver_logs', 'ver_actividad',
        ]);
        
        // 3. ADMINISTRADOR GENERAL (administra todo el grupo)
        $rolAdminGeneral = Role::create(['name' => 'administrador_general']);
        $rolAdminGeneral->givePermissionTo([
            'ver_empresas', 'crear_empresas', 'editar_empresas', 'activar_empresas', 'configurar_empresa',
            'ver_sedes', 'crear_sedes', 'editar_sedes',
            'ver_locales', 'crear_locales', 'editar_locales',
            'ver_usuarios', 'crear_usuarios', 'editar_usuarios', 'activar_usuarios', 'asignar_empresas',
            'ver_roles', 'asignar_permisos',
            'ver_configuracion',
            'ver_reportes', 'exportar_reportes', 'ver_dashboard',
            'ver_actividad',
        ]);
        
        // 4. ADMINISTRADOR DE EMPRESA
        $rolAdminEmpresa = Role::create(['name' => 'administrador_empresa']);
        $rolAdminEmpresa->givePermissionTo([
            'ver_usuarios', 'crear_usuarios', 'editar_usuarios',
            'ver_ventas', 'crear_ventas', 'editar_ventas', 'ver_cotizaciones', 'crear_cotizaciones', 'aprobar_cotizaciones',
            'ver_inventario', 'crear_productos', 'editar_productos', 'ajustar_stock',
            'ver_compras', 'crear_compras', 'aprobar_compras',
            'ver_clientes', 'crear_clientes', 'editar_clientes', 'ver_oportunidades', 'gestionar_oportunidades',
            'ver_contabilidad', 'ver_reportes_financieros',
            'ver_reportes', 'exportar_reportes', 'ver_dashboard',
            'configurar_empresa',
        ]);
        
        // 5. GERENTE
        $rolGerente = Role::create(['name' => 'gerente']);
        $rolGerente->givePermissionTo([
            'ver_ventas', 'crear_ventas', 'ver_cotizaciones', 'aprobar_cotizaciones',
            'ver_inventario', 'ajustar_stock',
            'ver_compras', 'aprobar_compras',
            'ver_clientes', 'crear_clientes', 'editar_clientes', 'gestionar_oportunidades',
            'ver_reportes', 'exportar_reportes', 'ver_dashboard',
        ]);
        
        // 6. VENDEDOR
        $rolVendedor = Role::create(['name' => 'vendedor']);
        $rolVendedor->givePermissionTo([
            'ver_ventas', 'crear_ventas',
            'ver_cotizaciones', 'crear_cotizaciones',
            'ver_clientes', 'crear_clientes', 'editar_clientes',
            'ver_inventario',
            'ver_dashboard',
        ]);
        
        // 7. ALMACENERO
        $rolAlmacenero = Role::create(['name' => 'almacenero']);
        $rolAlmacenero->givePermissionTo([
            'ver_inventario', 'ajustar_stock', 'transferir_stock',
            'ver_compras', 'recibir_compras',
            'ver_dashboard',
        ]);
        
        // 8. CONTADOR
        $rolContador = Role::create(['name' => 'contador']);
        $rolContador->givePermissionTo([
            'ver_contabilidad', 'crear_asientos', 'editar_asientos',
            'ver_reportes_financieros',
            'ver_ventas', 'ver_compras',
            'ver_reportes', 'exportar_reportes', 'ver_dashboard',
        ]);
        
        // 9. ASISTENTE
        $rolAsistente = Role::create(['name' => 'asistente']);
        $rolAsistente->givePermissionTo([
            'ver_ventas', 'ver_cotizaciones',
            'ver_clientes',
            'ver_inventario',
            'ver_dashboard',
        ]);
        
        // ==================== CREAR SUPERUSUARIO ====================
        
        $superusuario = User::create([
            'name' => 'Super Administrador',
            'email' => 'admin@erp-multisoft.com',
            'password' => Hash::make('Admin2025!'),
            'email_verified_at' => now(),
            'activo' => true,
        ]);
        
        $superusuario->assignRole('superusuario');
        
        activity()
            ->performedOn($superusuario)
            ->log('Superusuario creado desde seeder');
        
        $this->command->info('✅ Roles y permisos creados exitosamente');
        $this->command->info('✅ Superusuario creado:');
        $this->command->info('   Email: admin@erp-multisoft.com');
        $this->command->info('   Password: Admin2025!');
    }
}