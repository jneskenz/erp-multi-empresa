<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\GrupoEmpresa;
use App\Models\Workspace\Empresa;
use App\Models\Workspace\Local;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsuarioMultiempresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar que existen roles
        $rolesCount = Role::count();
        if ($rolesCount === 0) {
            $this->command->warn('No hay roles. Ejecuta RolePermissionSeeder primero.');
            return;
        }

        $grupoABC = GrupoEmpresa::where('slug', 'abc')->first();
        $grupoXYZ = GrupoEmpresa::where('slug', 'xyz')->first();
        $grupoDEF = GrupoEmpresa::where('slug', 'def')->first();

        if (!$grupoABC || !$grupoXYZ || !$grupoDEF) {
            $this->command->warn('No hay grupos. Ejecuta GrupoEmpresaSeeder primero.');
            return;
        }

        // Obtener superadmin existente para created_by
        $superAdmin = User::where('is_super_admin', true)->first();
        $createdById = $superAdmin ? $superAdmin->id : 1;

        $this->command->info('');
        $this->command->info('🔄 Creando usuarios...');
        $this->command->info('');

        // ═══════════════════════════════════════════════════════════════
        // GRUPO ABC - RETAIL
        // ═══════════════════════════════════════════════════════════════
        
        $empresaABC = Empresa::where('grupo_empresa_id', $grupoABC->id)->where('ruc', '20111222333')->first();
        $empresaServiciosABC = Empresa::where('ruc', '20111222444')->first();
        $empresaDistABC = Empresa::where('ruc', '20111222555')->first();

        // ADMINISTRADOR GENERAL - Grupo ABC
        $adminGeneral1 = User::create([
            'name' => 'Carlos Rodríguez',
            'email' => 'carlos.rodriguez@grupoabc.com',
            'password' => Hash::make('12345678'),
            'is_super_admin' => false,
            'grupo_empresa_id' => $grupoABC->id,
            'empresa_id' => null, // Admin general no tiene empresa específica
        ]);
        
        if (Role::where('name', 'Administrador General')->exists()) {
            $adminGeneral1->assignRole('Administrador General');
        }
        $this->command->info("✓ Admin General: {$adminGeneral1->email} (Grupo: {$grupoABC->nombre})");

        // PROPIETARIO - ABC Retail (acceso a todas las empresas del grupo)
        $propietario1 = User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@abcretail.com',
            'password' => Hash::make('12345678'),
            'is_super_admin' => false,
            'grupo_empresa_id' => $grupoABC->id,
            'empresa_id' => $empresaABC->id,
        ]);
        
        if (Role::where('name', 'Propietario')->exists()) {
            $propietario1->assignRole('Propietario');
        }
        
        // Asignar acceso a todas las empresas del grupo ABC
        $empresasABC = Empresa::where('grupo_empresa_id', $grupoABC->id)->get();
        foreach ($empresasABC as $empresa) {
            $propietario1->empresas()->attach($empresa->id, [
                'activo' => true,
                'fecha_asignacion' => now(),
                'created_by' => $createdById,
            ]);
        }
        $this->command->info("✓ Propietario: {$propietario1->email} ({$empresasABC->count()} empresas)");

        // GERENTE - ABC Servicios
        $gerente1 = User::create([
            'name' => 'Pedro López',
            'email' => 'pedro.lopez@abcservicios.com',
            'password' => Hash::make('12345678'),
            'is_super_admin' => false,
            'grupo_empresa_id' => $grupoABC->id,
            'empresa_id' => $empresaServiciosABC->id,
        ]);
        
        if (Role::where('name', 'Gerente de Empresa')->exists()) {
            $gerente1->assignRole('Gerente de Empresa');
        } elseif (Role::where('name', 'Gerente')->exists()) {
            $gerente1->assignRole('Gerente');
        }
        
        $gerente1->empresas()->attach($empresaServiciosABC->id, [
            'activo' => true,
            'fecha_asignacion' => now(),
            'created_by' => $createdById,
        ]);
        $this->command->info("✓ Gerente: {$gerente1->email} (Empresa: {$empresaServiciosABC->nombre_comercial})");

        // VENDEDOR - ABC Retail (Tienda San Isidro)
        $localTiendaSI = Local::where('codigo', 'L-SI-001')->first();
        $vendedor1 = User::create([
            'name' => 'Roberto Sánchez',
            'email' => 'roberto.sanchez@abcretail.com',
            'password' => Hash::make('12345678'),
            'is_super_admin' => false,
            'grupo_empresa_id' => $grupoABC->id,
            'empresa_id' => $empresaABC->id,
        ]);
        
        if (Role::where('name', 'Vendedor')->exists()) {
            $vendedor1->assignRole('Vendedor');
        }
        
        $vendedor1->empresas()->attach($empresaABC->id, [
            'activo' => true,
            'fecha_asignacion' => now(),
            'created_by' => $createdById,
        ]);
        $this->command->info("✓ Vendedor: {$vendedor1->email} (Local: {$localTiendaSI->nombre})");

        // VENDEDOR - ABC Retail (Tienda Miraflores)
        $localTiendaMF = Local::where('codigo', 'L-MF-001')->first();
        $vendedor2 = User::create([
            'name' => 'Carmen Flores',
            'email' => 'carmen.flores@abcretail.com',
            'password' => Hash::make('12345678'),
            'is_super_admin' => false,
            'grupo_empresa_id' => $grupoABC->id,
            'empresa_id' => $empresaABC->id,
        ]);
        
        if (Role::where('name', 'Vendedor')->exists()) {
            $vendedor2->assignRole('Vendedor');
        }
        
        $vendedor2->empresas()->attach($empresaABC->id, [
            'activo' => true,
            'fecha_asignacion' => now(),
            'created_by' => $createdById,
        ]);
        $this->command->info("✓ Vendedor: {$vendedor2->email} (Local: {$localTiendaMF->nombre})");

        // ALMACENERO - ABC Retail
        $localAlmacenSI = Local::where('codigo', 'A-SI-001')->first();
        $almacenero1 = User::create([
            'name' => 'Miguel Torres',
            'email' => 'miguel.torres@abcretail.com',
            'password' => Hash::make('12345678'),
            'is_super_admin' => false,
            'grupo_empresa_id' => $grupoABC->id,
            'empresa_id' => $empresaABC->id,
        ]);
        
        if (Role::where('name', 'Almacenero')->exists()) {
            $almacenero1->assignRole('Almacenero');
        }
        
        $almacenero1->empresas()->attach($empresaABC->id, [
            'activo' => true,
            'fecha_asignacion' => now(),
            'created_by' => $createdById,
        ]);
        $this->command->info("✓ Almacenero: {$almacenero1->email} (Local: {$localAlmacenSI->nombre})");

        // CAJERO - ABC Retail
        $cajero1 = User::create([
            'name' => 'Sofía Ramírez',
            'email' => 'sofia.ramirez@abcretail.com',
            'password' => Hash::make('12345678'),
            'is_super_admin' => false,
            'grupo_empresa_id' => $grupoABC->id,
            'empresa_id' => $empresaABC->id,
        ]);
        
        if (Role::where('name', 'Cajero')->exists()) {
            $cajero1->assignRole('Cajero');
        }
        
        $cajero1->empresas()->attach($empresaABC->id, [
            'activo' => true,
            'fecha_asignacion' => now(),
            'created_by' => $createdById,
        ]);
        $this->command->info("✓ Cajero: {$cajero1->email} (Local: {$localTiendaSI->nombre})");

        // ═══════════════════════════════════════════════════════════════
        // GRUPO XYZ - TECNOLOGÍA
        // ═══════════════════════════════════════════════════════════════
        
        $empresaXYZ = Empresa::where('grupo_empresa_id', $grupoXYZ->id)->where('ruc', '20222333444')->first();
        $empresaConsultingXYZ = Empresa::where('ruc', '20222333555')->first();

        // ADMINISTRADOR GENERAL - Grupo XYZ
        $adminGeneral2 = User::create([
            'name' => 'María González',
            'email' => 'maria.gonzalez@grupoxyz.com',
            'password' => Hash::make('12345678'),
            'is_super_admin' => false,
            'grupo_empresa_id' => $grupoXYZ->id,
            'empresa_id' => null,
        ]);
        
        if (Role::where('name', 'Administrador General')->exists()) {
            $adminGeneral2->assignRole('Administrador General');
        }
        $this->command->info("✓ Admin General: {$adminGeneral2->email} (Grupo: {$grupoXYZ->nombre})");

        // PROPIETARIO - XYZ Tech
        $propietario2 = User::create([
            'name' => 'Ana Martínez',
            'email' => 'ana.martinez@xyztech.com',
            'password' => Hash::make('12345678'),
            'is_super_admin' => false,
            'grupo_empresa_id' => $grupoXYZ->id,
            'empresa_id' => $empresaXYZ->id,
        ]);
        
        if (Role::where('name', 'Propietario')->exists()) {
            $propietario2->assignRole('Propietario');
        }
        
        $empresasXYZ = Empresa::where('grupo_empresa_id', $grupoXYZ->id)->get();
        foreach ($empresasXYZ as $empresa) {
            $propietario2->empresas()->attach($empresa->id, [
                'activo' => true,
                'fecha_asignacion' => now(),
                'created_by' => $createdById,
            ]);
        }
        $this->command->info("✓ Propietario: {$propietario2->email} ({$empresasXYZ->count()} empresas)");

        // GERENTE - XYZ Consulting
        $gerente2 = User::create([
            'name' => 'Laura Silva',
            'email' => 'laura.silva@xyzconsulting.com',
            'password' => Hash::make('12345678'),
            'is_super_admin' => false,
            'grupo_empresa_id' => $grupoXYZ->id,
            'empresa_id' => $empresaConsultingXYZ->id,
        ]);
        
        if (Role::where('name', 'Gerente de Empresa')->exists()) {
            $gerente2->assignRole('Gerente de Empresa');
        } elseif (Role::where('name', 'Gerente')->exists()) {
            $gerente2->assignRole('Gerente');
        }
        
        $gerente2->empresas()->attach($empresaConsultingXYZ->id, [
            'activo' => true,
            'fecha_asignacion' => now(),
            'created_by' => $createdById,
        ]);
        $this->command->info("✓ Gerente: {$gerente2->email} (Empresa: {$empresaConsultingXYZ->nombre_comercial})");

        // DESARROLLADOR - XYZ Tech
        $localLabDev = Local::where('codigo', 'L-SU-001')->first();
        $desarrollador = User::create([
            'name' => 'Diego Vargas',
            'email' => 'diego.vargas@xyztech.com',
            'password' => Hash::make('12345678'),
            'is_super_admin' => false,
            'grupo_empresa_id' => $grupoXYZ->id,
            'empresa_id' => $empresaXYZ->id,
        ]);
        
        if (Role::where('name', 'Desarrollador')->exists()) {
            $desarrollador->assignRole('Desarrollador');
        } elseif (Role::where('name', 'Usuario')->exists()) {
            $desarrollador->assignRole('Usuario');
        }
        
        $desarrollador->empresas()->attach($empresaXYZ->id, [
            'activo' => true,
            'fecha_asignacion' => now(),
            'created_by' => $createdById,
        ]);
        $this->command->info("✓ Desarrollador: {$desarrollador->email} (Local: {$localLabDev->nombre})");

        // ═══════════════════════════════════════════════════════════════
        // USUARIO CON ACCESO A MÚLTIPLES EMPRESAS
        // ═══════════════════════════════════════════════════════════════
        
        $usuarioMulti = User::create([
            'name' => 'Ricardo Mendoza',
            'email' => 'ricardo.mendoza@grupoabc.com',
            'password' => Hash::make('12345678'),
            'is_super_admin' => false,
            'grupo_empresa_id' => $grupoABC->id,
            'empresa_id' => $empresaABC->id, // Empresa principal
        ]);
        
        if (Role::where('name', 'Gerente de Operaciones')->exists()) {
            $usuarioMulti->assignRole('Gerente de Operaciones');
        } elseif (Role::where('name', 'Gerente')->exists()) {
            $usuarioMulti->assignRole('Gerente');
        }
        
        // Asignar a 3 empresas del grupo ABC
        $usuarioMulti->empresas()->attach($empresaABC->id, [
            'activo' => true,
            'fecha_asignacion' => now(),
            'created_by' => $createdById,
        ]);
        $usuarioMulti->empresas()->attach($empresaServiciosABC->id, [
            'activo' => true,
            'fecha_asignacion' => now(),
            'created_by' => $createdById,
        ]);
        $usuarioMulti->empresas()->attach($empresaDistABC->id, [
            'activo' => true,
            'fecha_asignacion' => now(),
            'created_by' => $createdById,
        ]);
        $this->command->info("✓ Usuario Multi-Empresa: {$usuarioMulti->email} (3 empresas)");

        // ═══════════════════════════════════════════════════════════════
        // USUARIO SIN EMPRESAS (para probar vista sin-empresas)
        // ═══════════════════════════════════════════════════════════════
        
        $usuarioSinEmpresas = User::create([
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo.usuario@test.com',
            'password' => Hash::make('12345678'),
            'is_super_admin' => false,
            'grupo_empresa_id' => $grupoABC->id,
            'empresa_id' => null,
        ]);
        
        if (Role::where('name', 'Usuario')->exists()) {
            $usuarioSinEmpresas->assignRole('Usuario');
        }
        $this->command->info("✓ Usuario sin empresas: {$usuarioSinEmpresas->email} (para testing)");

        $this->command->info('');
        $this->command->info("═══════════════════════════════════════════════════════");
        $this->command->info("Total de usuarios operativos creados: " . (User::count() - $rolesCount));
        $this->command->info("Total general de usuarios: " . User::count());
        $this->command->info("═══════════════════════════════════════════════════════");
        $this->command->info("");
        $this->command->info("📋 CREDENCIALES DE ACCESO:");
        $this->command->info("─────────────────────────────────────────────────────");
        $this->command->info("👔 ADMINISTRADORES GENERALES:");
        $this->command->info("   Email: carlos.rodriguez@grupoabc.com (Grupo ABC)");
        $this->command->info("   Email: maria.gonzalez@grupoxyz.com (Grupo XYZ)");
        $this->command->info("   Pass:  12345678");
        $this->command->info("");
        $this->command->info("🏢 PROPIETARIOS:");
        $this->command->info("   Email: juan.perez@abcretail.com (ABC Retail)");
        $this->command->info("   Email: ana.martinez@xyztech.com (XYZ Tech)");
        $this->command->info("   Pass:  12345678");
        $this->command->info("");
        $this->command->info("💼 USUARIOS OPERATIVOS:");
        $this->command->info("   Email: roberto.sanchez@abcretail.com (Vendedor)");
        $this->command->info("   Email: miguel.torres@abcretail.com (Almacenero)");
        $this->command->info("   Email: sofia.ramirez@abcretail.com (Cajero)");
        $this->command->info("   Email: diego.vargas@xyztech.com (Desarrollador)");
        $this->command->info("   Pass:  12345678");
        $this->command->info("");
        $this->command->info("🔀 USUARIO MULTI-EMPRESA:");
        $this->command->info("   Email: ricardo.mendoza@grupoabc.com (3 empresas)");
        $this->command->info("   Pass:  12345678");
        $this->command->info("");
        $this->command->info("❌ USUARIO SIN EMPRESAS:");
        $this->command->info("   Email: nuevo.usuario@test.com");
        $this->command->info("   Pass:  12345678");
        $this->command->info("═══════════════════════════════════════════════════════");
    }
}
