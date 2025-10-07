<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GrupoEmpresa;
use App\Models\Workspace\Empresa;
use App\Models\Workspace\Pais;

class EmpresaMultiempresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o buscar el país Perú
        $peru = Pais::firstOrCreate(
            ['codigo' => 'PE'],
            [
                'descripcion' => 'Perú',
                'moneda' => 'Soles',
                'codigo_moneda' => 'PEN',
                'simbolo_moneda' => 'S/',
                'estado' => true,
            ]
        );

        $grupoABC = GrupoEmpresa::where('slug', 'abc')->first();
        $grupoXYZ = GrupoEmpresa::where('slug', 'xyz')->first();
        $grupoDEF = GrupoEmpresa::where('slug', 'def')->first();

        if (!$grupoABC || !$grupoXYZ || !$grupoDEF) {
            $this->command->warn('No se encontraron todos los grupos. Ejecuta GrupoEmpresaSeeder primero.');
            return;
        }

        $empresas = [
            // Grupo ABC
            [
                'grupo_empresa_id' => $grupoABC->id,
                'nombre_comercial' => 'ABC Retail',
                'razon_social' => 'ABC RETAIL S.A.C.',
                'ruc' => '20111222333',
                'slug' => 'abc-retail',
                'pais' => 'Perú',
                'telefono' => '01-2345678',
                'email' => 'contacto@abcretail.com',
                'direccion' => 'Av. Javier Prado 1234, San Isidro',
                'activo' => true,
            ],
            [
                'grupo_empresa_id' => $grupoABC->id,
                'nombre_comercial' => 'ABC Servicios',
                'razon_social' => 'ABC SERVICIOS GENERALES S.A.C.',
                'ruc' => '20111222444',
                'slug' => 'abc-servicios',
                'pais' => 'Perú',
                'telefono' => '01-2345679',
                'email' => 'contacto@abcservicios.com',
                'direccion' => 'Av. Javier Prado 1236, San Isidro',
                'activo' => true,
            ],
            [
                'grupo_empresa_id' => $grupoABC->id,
                'nombre_comercial' => 'ABC Distribuciones',
                'razon_social' => 'ABC DISTRIBUCIONES S.R.L.',
                'ruc' => '20111222555',
                'slug' => 'abc-distribuciones',
                'pais' => 'Perú',
                'telefono' => '01-2345680',
                'email' => 'contacto@abcdist.com',
                'direccion' => 'Av. Argentina 5678, Callao',
                'activo' => true,
            ],
            
            // Grupo XYZ
            [
                'grupo_empresa_id' => $grupoXYZ->id,
                'nombre_comercial' => 'XYZ Tech',
                'razon_social' => 'XYZ TECHNOLOGY SOLUTIONS S.A.C.',
                'ruc' => '20222333444',
                'slug' => 'xyz-tech',
                'pais' => 'Perú',
                'telefono' => '01-3456789',
                'email' => 'info@xyztech.com',
                'direccion' => 'Calle Los Negocios 456, Surco',
                'activo' => true,
            ],
            [
                'grupo_empresa_id' => $grupoXYZ->id,
                'nombre_comercial' => 'XYZ Consulting',
                'razon_social' => 'XYZ CONSULTING & ADVISORY S.A.C.',
                'ruc' => '20222333555',
                'slug' => 'xyz-consulting',
                'pais' => 'Perú',
                'telefono' => '01-3456790',
                'email' => 'info@xyzconsulting.com',
                'direccion' => 'Calle Los Negocios 458, Surco',
                'activo' => true,
            ],
            
            // Grupo DEF
            [
                'grupo_empresa_id' => $grupoDEF->id,
                'nombre_comercial' => 'DEF Manufactura',
                'razon_social' => 'DEF MANUFACTURING S.A.',
                'ruc' => '20333444555',
                'slug' => 'def-manufactura',
                'pais' => 'Perú',
                'telefono' => '01-4567890',
                'email' => 'contacto@defmanufactura.com',
                'direccion' => 'Av. Industrial 789, Ate',
                'activo' => true,
            ],
            [
                'grupo_empresa_id' => $grupoDEF->id,
                'nombre_comercial' => 'DEF Logística',
                'razon_social' => 'DEF LOGISTICA Y TRANSPORTE S.A.C.',
                'ruc' => '20333444666',
                'slug' => 'def-logistica',
                'pais' => 'Perú',
                'telefono' => '01-4567891',
                'email' => 'contacto@deflogistica.com',
                'direccion' => 'Av. Industrial 791, Ate',
                'activo' => true,
            ],
        ];

        foreach ($empresas as $empresaData) {
            $empresa = Empresa::create($empresaData);
            
            $grupo = GrupoEmpresa::find($empresaData['grupo_empresa_id']);
            $this->command->info("✓ Empresa creada: {$empresa->nombre_comercial} (Grupo: {$grupo->nombre})");
        }

        $this->command->info('');
        $this->command->info("Total: " . count($empresas) . " empresas creadas");
    }
}
