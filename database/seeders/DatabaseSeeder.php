<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Contracts\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Comentado: User::factory(10)->create();
        // Ya no necesitamos usuarios aleatorios, usaremos usuarios específicos

        $this->call([
            // ═══════════════════════════════════════════════════════════
            // FASE 1: PERMISOS Y ROLES (deben ejecutarse primero)
            // ═══════════════════════════════════════════════════════════
            RolePermissionSeeder::class,        // Roles y permisos base
            RolesYPermisosSeeder::class,        // Roles adicionales
            
            // ═══════════════════════════════════════════════════════════
            // FASE 2: ESTRUCTURA ORGANIZACIONAL
            // ═══════════════════════════════════════════════════════════
            GrupoEmpresaSeeder::class,          // 3 grupos empresariales
            EmpresaMultiempresaSeeder::class,   // 7 empresas
            SedeMultiempresaSeeder::class,      // 6 sedes
            LocalMultiempresaSeeder::class,     // 17 locales + relaciones empresa-local            // ═══════════════════════════════════════════════════════════
            // FASE 3: USUARIOS Y SUPERADMIN
            // ═══════════════════════════════════════════════════════════
            SuperAdminSeeder::class,            // Superusuario
            UsuarioMultiempresaSeeder::class,   // 13 usuarios con diferentes roles
            
            // ═══════════════════════════════════════════════════════════
            // FASE 4: DATOS ADICIONALES (OPCIONAL)
            // ═══════════════════════════════════════════════════════════
            // DatosDemoSeeder::class,           // Datos demo del ERP (si existen)
        ]);
        
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('✅ SEEDING COMPLETADO - Sistema Multiempresa');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('📊 RESUMEN:');
        $this->command->info('   • Grupos Empresariales: ' . \App\Models\GrupoEmpresa::count());
        $this->command->info('   • Empresas: ' . \App\Models\Workspace\Empresa::count());
        $this->command->info('   • Sedes: ' . \App\Models\Workspace\Sede::count());
        $this->command->info('   • Locales: ' . \App\Models\Workspace\Local::count());
        $this->command->info('   • Usuarios: ' . \App\Models\User::count());
        $this->command->info('');
        $this->command->info('🔐 Acceso de prueba:');
        $this->command->info('   Email: admin@admin.com');
        $this->command->info('   Pass:  12345678');
        $this->command->info('═══════════════════════════════════════════════════════');
    }
}
