<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Workspace\Empresa;
use App\Models\Workspace\Sede;

class SedesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener empresas existentes
        $empresas = Empresa::all();

        if ($empresas->count() === 0) {
            $this->command->warn('No hay empresas creadas. Ejecuta el seeder de empresas primero.');
            return;
        }

        $sedesData = [
            // Para la primera empresa
            [
                'codigo' => 'SC001',
                'nombre' => 'Sede Central',
                'descripcion' => 'Oficina principal y centro de operaciones administrativas',
                'estado' => 1,
            ],
            [
                'codigo' => 'SN002',
                'nombre' => 'Sucursal Norte',
                'descripcion' => 'Sucursal comercial ubicada en la zona norte de la ciudad',
                'estado' => 1,
            ],
            [
                'codigo' => 'SA003',
                'nombre' => 'Almacén Principal',
                'descripcion' => 'Centro de distribución y almacenamiento de productos',
                'estado' => 1,
            ],
            // Para la segunda empresa (si existe)
            [
                'codigo' => 'OA004',
                'nombre' => 'Oficina Administrativa',
                'descripcion' => 'Sede administrativa para gestión contable y financiera',
                'estado' => 1,
            ],
            [
                'codigo' => 'PV005',
                'nombre' => 'Punto de Venta Centro',
                'descripcion' => 'Local comercial en el centro de la ciudad',
                'estado' => 1,
            ],
            [
                'codigo' => 'DS006',
                'nombre' => 'Depósito Sur',
                'descripcion' => 'Almacén secundario para productos de temporada',
                'estado' => 0, // Inactivo como ejemplo
            ],
        ];

        $empresaIndex = 0;
        foreach ($sedesData as $index => $sedeData) {
            // Alternar entre empresas disponibles
            if ($index > 0 && $index % 3 === 0) {
                $empresaIndex++;
            }
            
            // Si no hay más empresas, usar la primera
            if ($empresaIndex >= $empresas->count()) {
                $empresaIndex = 0;
            }

            $empresa = $empresas[$empresaIndex];

            // Verificar que no exista una sede con el mismo nombre para esta empresa
            $existingSede = Sede::where('empresa_id', $empresa->id)
                                ->where('nombre', $sedeData['nombre'])
                                ->first();

            if (!$existingSede) {
                Sede::create([
                    'empresa_id' => $empresa->id,
                    'nombre' => $sedeData['nombre'],
                    'descripcion' => $sedeData['descripcion'],
                    'estado' => $sedeData['estado'],
                    'codigo' => $sedeData['codigo'],
                ]);

                $this->command->info("Sede '{$sedeData['nombre']}' creada para {$empresa->razon_social}");
            } else {
                $this->command->warn("Sede '{$sedeData['nombre']}' ya existe para {$empresa->razon_social}");
            }
        }

        $this->command->info('Seeder de sedes ejecutado exitosamente.');

        Sede::factory(50)->create();

    }
}
