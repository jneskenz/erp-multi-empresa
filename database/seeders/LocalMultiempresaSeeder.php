<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GrupoEmpresa;
use App\Models\Workspace\Sede;
use App\Models\Workspace\Local;
use App\Models\Workspace\Empresa;

class LocalMultiempresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Crea locales y sus relaciones con empresas a través de la tabla pivot empresa_local.
     * Nota: Los locales NO tienen campo empresa_id directo, usan relación many-to-many.
     */
    public function run(): void
    {
        // Obtener sedes
        $sedes = [
            'sanisidro' => Sede::where('slug', 'sanisidro')->first(),
            'miraflores' => Sede::where('slug', 'miraflores')->first(),
            'callao' => Sede::where('slug', 'callao')->first(),
            'surco' => Sede::where('slug', 'surco')->first(),
            'sanisidro-xyz' => Sede::where('slug', 'sanisidro-xyz')->first(),
            'ate' => Sede::where('slug', 'ate')->first(),
        ];
        
        if (in_array(null, $sedes, true)) {
            $this->command->error('❌ No se encontraron todas las sedes. Ejecuta SedeMultiempresaSeeder primero.');
            return;
        }
        
        // Obtener empresas
        $empresas = [
            'abcRetail' => Empresa::where('ruc', '20111222333')->first(),
            'abcServicios' => Empresa::where('ruc', '20111222444')->first(),
            'abcDist' => Empresa::where('ruc', '20111222555')->first(),
            'xyzTech' => Empresa::where('ruc', '20222333444')->first(),
            'xyzConsulting' => Empresa::where('ruc', '20222333555')->first(),
            'defManufactura' => Empresa::where('ruc', '20333444555')->first(),
            'defLogistica' => Empresa::where('ruc', '20333444666')->first(),
        ];

        if (in_array(null, $empresas, true)) {
            $this->command->error('❌ No se encontraron todas las empresas. Ejecuta EmpresaMultiempresaSeeder primero.');
            return;
        }

        // Array de locales con sus empresas asociadas (para la tabla pivot)
        $localesData = [
            // ═══════════════════════════════════════════════════════════
            // GRUPO ABC - Locales de Retail, Servicios y Distribución
            // ═══════════════════════════════════════════════════════════
            
            // Sede San Isidro
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['abcRetail']->grupo_empresa_id,
                    'sede_id' => $sedes['sanisidro']->id,
                    'nombre' => 'Tienda San Isidro Centro',
                    'slug' => 'tienda-si-centro',
                    'codigo' => 'L-SI-001',
                    'tipo' => 'tienda',
                    'direccion' => 'Av. Javier Prado 1234, San Isidro',
                    'telefono' => '01-2345678',
                    'email' => 'tienda.si@abcretail.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['abcRetail']->id],
            ],
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['abcRetail']->grupo_empresa_id,
                    'sede_id' => $sedes['sanisidro']->id,
                    'nombre' => 'Almacén Central San Isidro',
                    'slug' => 'almacen-central-si',
                    'codigo' => 'A-SI-001',
                    'tipo' => 'almacen',
                    'descripcion' => 'Almacén compartido para Retail y Servicios',
                    'direccion' => 'Av. Javier Prado 1234, San Isidro (2do piso)',
                    'telefono' => '01-2345682',
                    'email' => 'almacen.si@abcretail.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['abcRetail']->id, $empresas['abcServicios']->id], // Almacén compartido
            ],
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['abcServicios']->grupo_empresa_id,
                    'sede_id' => $sedes['sanisidro']->id,
                    'nombre' => 'Oficina Administrativa Servicios',
                    'slug' => 'oficina-admin-servicios',
                    'codigo' => 'O-SI-002',
                    'tipo' => 'oficina',
                    'direccion' => 'Av. Javier Prado 1236, San Isidro',
                    'telefono' => '01-2345679',
                    'email' => 'oficina@abcservicios.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['abcServicios']->id],
            ],
            
            // Sede Miraflores
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['abcRetail']->grupo_empresa_id,
                    'sede_id' => $sedes['miraflores']->id,
                    'nombre' => 'Tienda Larco',
                    'slug' => 'tienda-larco',
                    'codigo' => 'L-MF-001',
                    'tipo' => 'tienda',
                    'direccion' => 'Av. Larco 567, Miraflores',
                    'telefono' => '01-2345681',
                    'email' => 'tienda.mf@abcretail.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['abcRetail']->id],
            ],
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['abcRetail']->grupo_empresa_id,
                    'sede_id' => $sedes['miraflores']->id,
                    'nombre' => 'Almacén Miraflores',
                    'slug' => 'almacen-miraflores',
                    'codigo' => 'A-MF-001',
                    'tipo' => 'almacen',
                    'direccion' => 'Av. Larco 567, Miraflores (Sótano)',
                    'telefono' => '01-2345683',
                    'email' => 'almacen.mf@abcretail.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['abcRetail']->id],
            ],
            
            // Sede Callao
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['abcDist']->grupo_empresa_id,
                    'sede_id' => $sedes['callao']->id,
                    'nombre' => 'Almacén Principal Callao',
                    'slug' => 'almacen-principal-callao',
                    'codigo' => 'A-CL-001',
                    'tipo' => 'almacen',
                    'direccion' => 'Av. Argentina 5678, Callao',
                    'telefono' => '01-2345680',
                    'email' => 'almacen@abcdist.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['abcDist']->id],
            ],
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['abcDist']->grupo_empresa_id,
                    'sede_id' => $sedes['callao']->id,
                    'nombre' => 'Centro de Distribución',
                    'slug' => 'centro-dist-callao',
                    'codigo' => 'CD-CL-001',
                    'tipo' => 'almacen',
                    'descripcion' => 'Centro compartido para Distribuciones y Retail',
                    'direccion' => 'Av. Argentina 5680, Callao',
                    'telefono' => '01-2345684',
                    'email' => 'centro@abcdist.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['abcDist']->id, $empresas['abcRetail']->id], // Centro compartido
            ],
            
            // ═══════════════════════════════════════════════════════════
            // GRUPO XYZ - Locales de Tecnología y Consultoría
            // ═══════════════════════════════════════════════════════════
            
            // Sede Surco
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['xyzTech']->grupo_empresa_id,
                    'sede_id' => $sedes['surco']->id,
                    'nombre' => 'Oficina Principal Surco',
                    'slug' => 'oficina-principal-surco',
                    'codigo' => 'O-SU-001',
                    'tipo' => 'oficina',
                    'descripcion' => 'Oficina compartida para Tech y Consulting',
                    'direccion' => 'Calle Los Negocios 456, Surco',
                    'telefono' => '01-3456789',
                    'email' => 'oficina@xyztech.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['xyzTech']->id, $empresas['xyzConsulting']->id], // Oficina compartida
            ],
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['xyzTech']->grupo_empresa_id,
                    'sede_id' => $sedes['surco']->id,
                    'nombre' => 'Lab de Desarrollo',
                    'slug' => 'lab-desarrollo',
                    'codigo' => 'L-SU-001',
                    'tipo' => 'oficina',
                    'direccion' => 'Calle Los Negocios 456, Surco (3er piso)',
                    'telefono' => '01-3456793',
                    'email' => 'lab@xyztech.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['xyzTech']->id],
            ],
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['xyzConsulting']->grupo_empresa_id,
                    'sede_id' => $sedes['surco']->id,
                    'nombre' => 'Sala de Reuniones Consulting',
                    'slug' => 'sala-consulting',
                    'codigo' => 'O-SU-002',
                    'tipo' => 'oficina',
                    'direccion' => 'Calle Los Negocios 458, Surco',
                    'telefono' => '01-3456790',
                    'email' => 'contacto@xyzconsulting.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['xyzConsulting']->id],
            ],
            
            // Sede San Isidro XYZ
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['xyzTech']->grupo_empresa_id,
                    'sede_id' => $sedes['sanisidro-xyz']->id,
                    'nombre' => 'Oficina Comercial San Isidro',
                    'slug' => 'oficina-comercial-si',
                    'codigo' => 'O-SI-003',
                    'tipo' => 'oficina',
                    'direccion' => 'Av. República de Panamá 3456, San Isidro',
                    'telefono' => '01-3456792',
                    'email' => 'comercial@xyztech.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['xyzTech']->id],
            ],
            
            // ═══════════════════════════════════════════════════════════
            // GRUPO DEF - Locales de Manufactura y Logística
            // ═══════════════════════════════════════════════════════════
            
            // Sede Ate
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['defManufactura']->grupo_empresa_id,
                    'sede_id' => $sedes['ate']->id,
                    'nombre' => 'Línea Producción A',
                    'slug' => 'produccion-a',
                    'codigo' => 'P-AT-001',
                    'tipo' => 'fabrica',
                    'direccion' => 'Av. Industrial 789, Ate',
                    'telefono' => '01-4567890',
                    'email' => 'produccion.a@defmanufactura.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['defManufactura']->id],
            ],
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['defManufactura']->grupo_empresa_id,
                    'sede_id' => $sedes['ate']->id,
                    'nombre' => 'Línea Producción B',
                    'slug' => 'produccion-b',
                    'codigo' => 'P-AT-002',
                    'tipo' => 'fabrica',
                    'direccion' => 'Av. Industrial 789, Ate',
                    'telefono' => '01-4567892',
                    'email' => 'produccion.b@defmanufactura.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['defManufactura']->id],
            ],
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['defManufactura']->grupo_empresa_id,
                    'sede_id' => $sedes['ate']->id,
                    'nombre' => 'Almacén Materia Prima',
                    'slug' => 'almacen-mp',
                    'codigo' => 'A-AT-002',
                    'tipo' => 'almacen',
                    'direccion' => 'Av. Industrial 789, Ate',
                    'telefono' => '01-4567893',
                    'email' => 'almacen@defmanufactura.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['defManufactura']->id],
            ],
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['defLogistica']->grupo_empresa_id,
                    'sede_id' => $sedes['ate']->id,
                    'nombre' => 'Centro Distribución Norte',
                    'slug' => 'centro-dist-norte',
                    'codigo' => 'CD-AT-002',
                    'tipo' => 'almacen',
                    'direccion' => 'Av. Industrial 791, Ate',
                    'telefono' => '01-4567891',
                    'email' => 'centro@deflogistica.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['defLogistica']->id],
            ],
            [
                'local' => [
                    'grupo_empresa_id' => $empresas['defLogistica']->grupo_empresa_id,
                    'sede_id' => $sedes['ate']->id,
                    'nombre' => 'Oficina Administrativa Logística',
                    'slug' => 'oficina-admin-logistica',
                    'codigo' => 'O-AT-003',
                    'tipo' => 'oficina',
                    'direccion' => 'Av. Industrial 791, Ate (Oficina)',
                    'telefono' => '01-4567894',
                    'email' => 'oficina@deflogistica.com',
                    'estado' => 1,
                    'activo' => true,
                ],
                'empresas' => [$empresas['defLogistica']->id],
            ],
        ];

        $totalLocales = 0;
        $totalRelaciones = 0;

        foreach ($localesData as $data) {
            // Crear el local
            $local = Local::create($data['local']);
            $totalLocales++;
            
            // Crear relaciones con empresas en la tabla pivot
            foreach ($data['empresas'] as $empresaId) {
                $local->empresas()->attach($empresaId, [
                    'activo' => true,
                    'fecha_inicio' => now(),
                    'es_principal' => false,
                ]);
                $totalRelaciones++;
            }
            
            $sede = Sede::find($data['local']['sede_id']);
            $empresasNombres = Empresa::whereIn('id', $data['empresas'])->pluck('nombre_comercial')->implode(', ');
            
            $this->command->info("✓ Local: {$local->nombre} [{$local->codigo}]");
            $this->command->line("  └─ Sede: {$sede->nombre} | Empresas: {$empresasNombres}");
        }

        $this->command->info('');
        $this->command->info("═══════════════════════════════════════════════════════");
        $this->command->info("✅ Total de locales creados: {$totalLocales}");
        $this->command->info("✅ Total de relaciones empresa-local: {$totalRelaciones}");
        $this->command->info("═══════════════════════════════════════════════════════");
    }
}
