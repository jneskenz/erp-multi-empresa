<?php

namespace Database\Factories\Workspace;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workspace\Sede>
 */
class SedeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo' => strtoupper($this->faker->bothify('??###')), // Genera un código único
            'nombre' => \Illuminate\Support\Str::limit($this->faker->company(), 50), // maximo 50 caracteres
            'descripcion' => $this->faker->optional()->sentence(),
            'estado' => $this->faker->boolean(90), // 90% de probabilidad de ser true
            'empresa_id' => \App\Models\Workspace\Empresa::factory(), // Asumiendo que tienes un modelo Empresa
        ];
    }
}
