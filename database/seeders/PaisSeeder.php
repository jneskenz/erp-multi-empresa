<?php

namespace Database\Seeders;

use App\Models\Erp\Pais;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pais::factory()->createMany([
            [
                'descripcion' => 'Perú',
                'codigo' => 'PE',
                'moneda' => 'Sol',
                'codigo_moneda' => 'PEN',
                'simbolo_moneda' => 'S/',
                'estado' => 1,
            ],
            [
                'descripcion' => 'Estados Unidos',
                'codigo' => 'US',
                'moneda' => 'Dólar',
                'codigo_moneda' => 'USD',
                'simbolo_moneda' => '$',
                'estado' => 1,
            ],
            [
                'descripcion' => 'España',
                'codigo' => 'ES',
                'moneda' => 'Euro',
                'codigo_moneda' => 'EUR',
                'simbolo_moneda' => '€',
                'estado' => 1,
            ],
            [
                'descripcion' => 'México',
                'codigo' => 'MX',
                'moneda' => 'Peso Mexicano',
                'codigo_moneda' => 'MXN',
                'simbolo_moneda' => '$',
                'estado' => 1,
            ],
            [
                'descripcion' => 'Argentina',
                'codigo' => 'AR',
                'moneda' => 'Peso Argentino',
                'codigo_moneda' => 'ARS',
                'simbolo_moneda' => '$',
                'estado' => 1,
            ],
            [
                'descripcion' => 'Chile',
                'codigo' => 'CL',
                'moneda' => 'Peso Chileno',
                'codigo_moneda' => 'CLP',
                'simbolo_moneda' => '$',
                'estado' => 1,
            ],
            [
                'descripcion' => 'Colombia',
                'codigo' => 'CO',
                'moneda' => 'Peso Colombiano',
                'codigo_moneda' => 'COP',
                'simbolo_moneda' => '$',
                'estado' => 1,
            ],
            [
                'descripcion' => 'Brasil',
                'codigo' => 'BR',
                'moneda' => 'Real Brasileño',
                'codigo_moneda' => 'BRL',
                'simbolo_moneda' => 'R$',
                'estado' => 1,
            ],
            [
                'descripcion' => 'Reino Unido',
                'codigo' => 'GB',
                'moneda' => 'Libra Esterlina',
                'codigo_moneda' => 'GBP',
                'simbolo_moneda' => '£',
                'estado' => 1,
            ],
            [
                'descripcion' => 'Canadá',
                'codigo' => 'CA',
                'moneda' => 'Dólar Canadiense',
                'codigo_moneda' => 'CAD',
                'simbolo_moneda' => '$',
                'estado' => 1,
            ],
            [
                'descripcion' => 'Australia',
                'codigo' => 'AU',
                'moneda' => 'Dólar Australiano',
                'codigo_moneda' => 'AUD',
                'simbolo_moneda' => '$',
                'estado' => 1,
            ],
            [
                'descripcion' => 'Japón',
                'codigo' => 'JP',
                'moneda' => 'Yen Japonés',
                'codigo_moneda' => 'JPY',
                'simbolo_moneda' => '¥',
                'estado' => 1,
            ],
           
        ]);

        // Pais::factory()->count(50)->create();
    }
}
