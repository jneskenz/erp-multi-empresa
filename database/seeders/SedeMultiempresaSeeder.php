<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GrupoEmpresa;
use App\Models\Workspace\Sede;

class SedeMultiempresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grupoABC = GrupoEmpresa::where('slug', 'abc')->first();
        $grupoXYZ = GrupoEmpresa::where('slug', 'xyz')->first();
        $grupoDEF = GrupoEmpresa::where('slug', 'def')->first();
        
        if (!$grupoABC || !$grupoXYZ || !$grupoDEF) {
            $this->command->warn('No hay grupos. Ejecuta GrupoEmpresaSeeder primero.');
            return;
        }

        $sedes = [
            // Grupo ABC - Sedes en Lima
            [
                'grupo_empresa_id' => $grupoABC->id,
                'nombre' => 'Sede Central San Isidro',
                'slug' => 'sanisidro',
                'ciudad' => 'Lima',
                'departamento' => 'Lima',
                'pais' => 'Perú',
                'direccion' => 'Av. Javier Prado 1234, San Isidro',
                'telefono' => '01-2345678',
                'email' => 'sanisidro@grupoabc.com',
                'activo' => true,
                'es_principal' => true,
            ],
            [
                'grupo_empresa_id' => $grupoABC->id,
                'nombre' => 'Sede Miraflores',
                'slug' => 'miraflores',
                'ciudad' => 'Lima',
                'departamento' => 'Lima',
                'pais' => 'Perú',
                'direccion' => 'Av. Larco 567, Miraflores',
                'telefono' => '01-2345681',
                'email' => 'miraflores@grupoabc.com',
                'activo' => true,
                'es_principal' => false,
            ],
            [
                'grupo_empresa_id' => $grupoABC->id,
                'nombre' => 'Sede Callao',
                'slug' => 'callao',
                'ciudad' => 'Callao',
                'departamento' => 'Callao',
                'pais' => 'Perú',
                'direccion' => 'Av. Argentina 5678, Callao',
                'telefono' => '01-2345680',
                'email' => 'callao@grupoabc.com',
                'activo' => true,
                'es_principal' => false,
            ],
            
            // Grupo XYZ - Sedes en Lima
            [
                'grupo_empresa_id' => $grupoXYZ->id,
                'nombre' => 'Sede Principal Surco',
                'slug' => 'surco',
                'ciudad' => 'Lima',
                'departamento' => 'Lima',
                'pais' => 'Perú',
                'direccion' => 'Calle Los Negocios 456, Surco',
                'telefono' => '01-3456789',
                'email' => 'surco@grupoxyz.com',
                'activo' => true,
                'es_principal' => true,
            ],
            [
                'grupo_empresa_id' => $grupoXYZ->id,
                'nombre' => 'Sede San Isidro',
                'slug' => 'sanisidro-xyz',
                'ciudad' => 'Lima',
                'departamento' => 'Lima',
                'pais' => 'Perú',
                'direccion' => 'Av. República de Panamá 3456, San Isidro',
                'telefono' => '01-3456792',
                'email' => 'sanisidro@grupoxyz.com',
                'activo' => true,
                'es_principal' => false,
            ],
            
            // Grupo DEF - Sede Industrial
            [
                'grupo_empresa_id' => $grupoDEF->id,
                'nombre' => 'Sede Industrial Ate',
                'slug' => 'ate',
                'ciudad' => 'Lima',
                'departamento' => 'Lima',
                'pais' => 'Perú',
                'direccion' => 'Av. Industrial 789, Ate',
                'telefono' => '01-4567890',
                'email' => 'ate@grupodef.com',
                'activo' => true,
                'es_principal' => true,
            ],
        ];

        foreach ($sedes as $sedeData) {
            $sede = Sede::create($sedeData);
            
            $grupo = GrupoEmpresa::find($sedeData['grupo_empresa_id']);
            $this->command->info("✓ Sede creada: {$sede->nombre} (Grupo: {$grupo->nombre})");
        }

        $this->command->info('');
        $this->command->info("Total: " . count($sedes) . " sedes creadas");
    }
}
