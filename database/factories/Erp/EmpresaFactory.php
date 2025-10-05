<?php

namespace Database\Factories\Erp;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Erp\Empresa>
 */
class EmpresaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_comercial' => $this->faker->company(),
            'numerodocumento' => '20' . $this->faker->unique()->numerify('#########'),
            'razon_social' => $this->faker->company() . ' ' . $this->faker->randomElement(['S.A.C.', 'S.R.L.', 'E.I.R.L.']),
            'direccion' => $this->faker->address(),
            'telefono' => $this->faker->optional()->phoneNumber(),
            'correo' => $this->faker->optional()->companyEmail(),
            'avatar' => $this->faker->optional()->url(),
            'estado' => $this->faker->randomElement(['1', '0']),
            'pais_id' => \App\Models\Erp\Pais::factory(),
            'representante_legal' => $this->faker->name(),
        ];
    }
}
