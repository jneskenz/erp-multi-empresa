<?php

namespace Database\Seeders;

use App\Models\Erp\Empresa;
use App\Models\Erp\Local;
use App\Models\Erp\Sede;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si existen sedes para crear locales
        if (Sede::count() === 0) {
            // Si no hay sedes, crear algunas para testing
            if (Empresa::count() === 0) {
                $empresa = Empresa::factory()->create([
                    'nombre' => 'Empresa Demo',
                    'codigo' => 'EMP001',
                ]);
            } else {
                $empresa = Empresa::first();
            }

            $sedes = Sede::factory(3)->create([
                'empresa_id' => $empresa->id,
            ]);
        } else {
            $sedes = Sede::all();
        }

        // Crear locales para cada sede
        foreach ($sedes as $sede) {
            Local::factory(rand(2, 5))->create([
                'sede_id' => $sede->id,
            ]);
        }

        // Crear algunos locales específicos para testing
        $sedeDemo = $sedes->first();
        
        Local::factory()->create([
            'descripcion' => 'Local Principal - Centro',
            'codigo' => 'LOC001',
            'direccion' => 'Av. Principal 123, Centro',
            'correo' => 'centro@empresa.com',
            'telefono' => '+51 987654321',
            'whatsapp' => '+51 987654321',
            'estado' => true,
            'sede_id' => $sedeDemo->id,
        ]);

        Local::factory()->create([
            'descripcion' => 'Local Secundario - Norte',
            'codigo' => 'LOC002',
            'direccion' => 'Av. Norte 456, Zona Norte',
            'correo' => 'norte@empresa.com',
            'telefono' => '+51 987654322',
            'whatsapp' => '+51 987654322',
            'estado' => true,
            'sede_id' => $sedeDemo->id,
        ]);
        
    }
}
