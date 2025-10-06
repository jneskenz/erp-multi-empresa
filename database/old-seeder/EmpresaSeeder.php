<?php

namespace Database\Seeders;

use App\Models\Workspace\Empresa;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresas = [
            [
                'ruc' => '20123451789',
                'nombre_comercial' => 'Empresa Ejemplo S.A.C.',
                'razon_social' => 'Empresa Ejemplo Sociedad Anónima Cerrada',
                'direccion' => 'Av. Principal 123, Lima, Lima',
                'telefono' => '01-2345678',
                'correo' => 'contacto@ejemplo.com',
                'avatar' => 'https://www.ejemplo.com',
                'estado' => '1',
            ],
            [
                'ruc' => '20981654321',
                'nombre_comercial' => 'Comercial Lima E.I.R.L.',
                'razon_social' => 'Comercial Lima Empresa Individual de Responsabilidad Limitada',
                'direccion' => 'Jr. Comercio 456, Lima, Lima',
                'telefono' => '01-9876543',
                'correo' => 'ventas@comerciallima.com',
                'avatar' => 'https://www.comerciallima.com',
                'estado' => '1',
            ],
            [
                'ruc' => '20256789123',
                'nombre_comercial' => 'Servicios Generales S.R.L.',
                'razon_social' => 'Servicios Generales Sociedad de Responsabilidad Limitada',
                'direccion' => 'Calle Los Servicios 789, San Isidro, Lima',
                'telefono' => '01-4567891',
                'correo' => 'info@servicios.com',
                'avatar' => null,
                'estado' => '0',
            ],
        ];

        foreach ($empresas as $empresa) {
            Empresa::create($empresa);
        }

        Empresa::factory(15)->create();

    }
}
