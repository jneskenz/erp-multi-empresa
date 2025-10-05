<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GrupoEmpresa;
use App\Models\Workspace\Empresa;
use App\Models\Workspace\Sede;
use App\Models\Workspace\Local;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatosDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Creando datos de demostración...');
        
        // ==================== GRUPO EMPRESARIAL 1 ====================
        
        $grupo1 = GrupoEmpresa::create([
            'user_uuid' => \Illuminate\Support\Str::uuid(),
            'nombre' => 'Corporación TechSolutions',
            'slug' => 'techsolutions',
            'ruc' => '20123456789',
            'descripcion' => 'Grupo empresarial de tecnología y soluciones digitales',
            'plan_actual' => 'empresarial',
            'max_empresas' => 5,
            'max_usuarios' => 100,
            'modulos_habilitados' => ['erp', 'crm', 'rrhh', 'web'],
            'estado' => 'activo',
            'fecha_activacion' => now(),
            'fecha_vencimiento' => now()->addYear(),
            'configuracion_visual' => [
                'tema' => 'light',
                'color_primario' => '#3B82F6',
                'color_secundario' => '#10B981',
                'fuente' => 'Inter',
            ],
        ]);
        
        // Propietario del grupo
        $propietario1 = User::create([
            'grupo_empresa_id' => $grupo1->id,
            'name' => 'Carlos Mendoza',
            'email' => 'carlos.mendoza@techsolutions.com',
            'password' => Hash::make('password'),
            'documento_tipo' => 'DNI',
            'documento_numero' => '12345678',
            'telefono' => '+51 999 888 777',
            'activo' => true,
            'email_verified_at' => now(),
        ]);
        $propietario1->assignRole('propietario');
        
        // Asociar como propietario del grupo
        $grupo1->propietarios()->attach($propietario1->id, [
            'tipo' => 'principal',
            'porcentaje_participacion' => 100.00,
            'puede_modificar_plan' => true,
            'puede_crear_empresas' => true,
            'puede_asignar_usuarios' => true,
            'activo' => true,
        ]);
        
        // Administrador general
        $adminGeneral1 = User::create([
            'grupo_empresa_id' => $grupo1->id,
            'name' => 'María González',
            'email' => 'maria.gonzalez@techsolutions.com',
            'password' => Hash::make('password'),
            'documento_tipo' => 'DNI',
            'documento_numero' => '87654321',
            'telefono' => '+51 999 777 666',
            'activo' => true,
            'email_verified_at' => now(),
        ]);
        $adminGeneral1->assignRole('administrador_general');
        
        // --- EMPRESA 1 del Grupo 1 ---
        
        $empresa1 = Empresa::create([
            'grupo_empresa_id' => $grupo1->id,
            'nombre' => 'TechSolutions SAC',
            'slug' => 'techsolutions-sac',
            'ruc' => '20111222333',
            'razon_social' => 'TechSolutions Sociedad Anónima Cerrada',
            'nombre_comercial' => 'TechSolutions',
            'email' => 'contacto@techsolutions-sac.com',
            'telefono' => '+51 01 234 5678',
            'direccion' => 'Av. Javier Prado Este 4567',
            'distrito' => 'San Isidro',
            'provincia' => 'Lima',
            'departamento' => 'Lima',
            'modulos_activos' => ['erp', 'crm'],
            'activo' => true,
        ]);
        
        // Sede de Lima
        $sedeLima = Sede::create([
            'grupo_empresa_id' => $grupo1->id,
            'nombre' => 'Sede Lima',
            'slug' => 'lima',
            'codigo' => 'SL-001',
            'ciudad' => 'Lima',
            'departamento' => 'Lima',
            'telefono' => '+51 01 234 5678',
            'email' => 'lima@techsolutions.com',
            'direccion' => 'Av. Javier Prado Este 4567, San Isidro',
            'latitud' => -12.0935396,
            'longitud' => -77.0283712,
            'activo' => true,
            'es_principal' => true,
        ]);
        
        // Local Principal
        $localPrincipal = Local::create([
            'grupo_empresa_id' => $grupo1->id,
            'empresa_id' => $empresa1->id,
            'sede_id' => $sedeLima->id,
            'nombre' => 'Oficina Principal',
            'slug' => 'oficina-principal',
            'codigo' => 'LOC-001',
            'tipo' => 'oficina',
            'direccion' => 'Av. Javier Prado Este 4567, Piso 8',
            'referencia' => 'Edificio Torre Empresarial',
            'telefono' => '+51 01 234 5678',
            'horarios' => json_encode([
                'lunes' => ['inicio' => '08:00', 'fin' => '18:00'],
                'martes' => ['inicio' => '08:00', 'fin' => '18:00'],
                'miércoles' => ['inicio' => '08:00', 'fin' => '18:00'],
                'jueves' => ['inicio' => '08:00', 'fin' => '18:00'],
                'viernes' => ['inicio' => '08:00', 'fin' => '18:00'],
                'sábado' => null,
                'domingo' => null,
            ]),
            'activo' => true,
        ]);
        
        // Usuarios operativos de la empresa
        $adminEmpresa = User::create([
            'grupo_empresa_id' => $grupo1->id,
            'empresa_id' => $empresa1->id,
            'sede_id' => $sedeLima->id,
            'local_id' => $localPrincipal->id,
            'name' => 'Roberto Díaz',
            'email' => 'roberto.diaz@techsolutions-sac.com',
            'password' => Hash::make('password'),
            'documento_tipo' => 'DNI',
            'documento_numero' => '45678901',
            'telefono' => '+51 999 555 444',
            'activo' => true,
            'email_verified_at' => now(),
        ]);
        $adminEmpresa->assignRole('administrador_empresa');
        
        $gerente = User::create([
            'grupo_empresa_id' => $grupo1->id,
            'empresa_id' => $empresa1->id,
            'sede_id' => $sedeLima->id,
            'name' => 'Ana Torres',
            'email' => 'ana.torres@techsolutions-sac.com',
            'password' => Hash::make('password'),
            'documento_tipo' => 'DNI',
            'documento_numero' => '23456789',
            'telefono' => '+51 999 444 333',
            'activo' => true,
            'email_verified_at' => now(),
        ]);
        $gerente->assignRole('gerente');
        
        $vendedor1 = User::create([
            'grupo_empresa_id' => $grupo1->id,
            'empresa_id' => $empresa1->id,
            'sede_id' => $sedeLima->id,
            'name' => 'Luis Ramírez',
            'email' => 'luis.ramirez@techsolutions-sac.com',
            'password' => Hash::make('password'),
            'documento_tipo' => 'DNI',
            'documento_numero' => '34567890',
            'telefono' => '+51 999 333 222',
            'activo' => true,
            'email_verified_at' => now(),
        ]);
        $vendedor1->assignRole('vendedor');
        
        // --- EMPRESA 2 del Grupo 1 ---
        
        $empresa2 = Empresa::create([
            'grupo_empresa_id' => $grupo1->id,
            'nombre' => 'Digital Services EIRL',
            'slug' => 'digital-services',
            'ruc' => '20444555666',
            'razon_social' => 'Digital Services Empresa Individual de Responsabilidad Limitada',
            'nombre_comercial' => 'DigiServices',
            'email' => 'info@digiservices.com',
            'telefono' => '+51 01 987 6543',
            'direccion' => 'Av. Benavides 2345',
            'distrito' => 'Miraflores',
            'provincia' => 'Lima',
            'departamento' => 'Lima',
            'modulos_activos' => ['crm', 'rrhh'],
            'activo' => true,
        ]);
        
        // Sede de Miraflores
        $sedeMiraflores = Sede::create([
            'grupo_empresa_id' => $grupo1->id,
            'nombre' => 'Sede Miraflores',
            'slug' => 'miraflores',
            'codigo' => 'SM-001',
            'ciudad' => 'Lima',
            'departamento' => 'Lima',
            'telefono' => '+51 01 987 6543',
            'email' => 'miraflores@digiservices.com',
            'direccion' => 'Av. Benavides 2345, Miraflores',
            'activo' => true,
        ]);
        
        $localMiraflores = Local::create([
            'grupo_empresa_id' => $grupo1->id,
            'empresa_id' => $empresa2->id,
            'sede_id' => $sedeMiraflores->id,
            'nombre' => 'Oficina Miraflores',
            'slug' => 'oficina-miraflores',
            'codigo' => 'LOC-002',
            'tipo' => 'oficina',
            'direccion' => 'Av. Benavides 2345, Piso 3',
            'telefono' => '+51 01 987 6543',
            'activo' => true,
        ]);
        
        // ==================== GRUPO EMPRESARIAL 2 ====================
        
        $grupo2 = GrupoEmpresa::create([
            'user_uuid' => \Illuminate\Support\Str::uuid(),
            'nombre' => 'Comercial Andes Group',
            'slug' => 'andes-group',
            'ruc' => '20987654321',
            'descripcion' => 'Grupo comercial especializado en retail',
            'plan_actual' => 'profesional',
            'max_empresas' => 3,
            'max_usuarios' => 50,
            'modulos_habilitados' => ['erp', 'web'],
            'estado' => 'activo',
            'fecha_activacion' => now(),
            'fecha_vencimiento' => now()->addMonths(6),
        ]);
        
        $propietario2 = User::create([
            'grupo_empresa_id' => $grupo2->id,
            'name' => 'Patricia Salazar',
            'email' => 'patricia.salazar@andesgroup.com',
            'password' => Hash::make('password'),
            'documento_tipo' => 'DNI',
            'documento_numero' => '56789012',
            'telefono' => '+51 999 222 111',
            'activo' => true,
            'email_verified_at' => now(),
        ]);
        $propietario2->assignRole('propietario');
        
        $grupo2->propietarios()->attach($propietario2->id, [
            'tipo' => 'principal',
            'porcentaje_participacion' => 100.00,
            'puede_modificar_plan' => true,
            'puede_crear_empresas' => true,
            'puede_asignar_usuarios' => true,
            'activo' => true,
        ]);
        
        $empresa3 = Empresa::create([
            'grupo_empresa_id' => $grupo2->id,
            'nombre' => 'Andes Retail SAC',
            'slug' => 'andes-retail',
            'ruc' => '20777888999',
            'razon_social' => 'Andes Retail Sociedad Anónima Cerrada',
            'nombre_comercial' => 'Andes Store',
            'email' => 'contacto@andesretail.com',
            'telefono' => '+51 01 555 4444',
            'direccion' => 'Av. La Marina 1234',
            'distrito' => 'San Miguel',
            'provincia' => 'Lima',
            'departamento' => 'Lima',
            'modulos_activos' => ['erp'],
            'activo' => true,
        ]);
        
        $this->command->info('✅ Datos de demostración creados:');
        $this->command->info('');
        $this->command->info('📊 GRUPOS EMPRESARIALES:');
        $this->command->info('   1. Corporación TechSolutions (slug: techsolutions)');
        $this->command->info('   2. Comercial Andes Group (slug: andes-group)');
        $this->command->info('');
        $this->command->info('🏢 EMPRESAS:');
        $this->command->info('   1. TechSolutions SAC (slug: techsolutions-sac)');
        $this->command->info('   2. Digital Services EIRL (slug: digital-services)');
        $this->command->info('   3. Andes Retail SAC (slug: andes-retail)');
        $this->command->info('');
        $this->command->info('👥 USUARIOS DE PRUEBA:');
        $this->command->info('   Propietario: carlos.mendoza@techsolutions.com / password');
        $this->command->info('   Admin General: maria.gonzalez@techsolutions.com / password');
        $this->command->info('   Admin Empresa: roberto.diaz@techsolutions-sac.com / password');
        $this->command->info('   Gerente: ana.torres@techsolutions-sac.com / password');
        $this->command->info('   Vendedor: luis.ramirez@techsolutions-sac.com / password');
        $this->command->info('');
        $this->command->info('🔗 URLs de acceso:');
        $this->command->info('   Superusuario: /admin');
        $this->command->info('   Grupo 1: /techsolutions');
        $this->command->info('   Empresa 1: /techsolutions/erp/techsolutions-sac');
    }
}