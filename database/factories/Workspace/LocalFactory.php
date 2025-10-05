<?php

namespace Database\Factories\Workspace;

use App\Models\Workspace\Local;
use App\Models\Workspace\Sede;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workspace\Local>
 */
class LocalFactory extends Factory
{
    protected $model = Local::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'descripcion' => $this->faker->company . ' - ' . $this->faker->city,
            'codigo' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'direccion' => $this->faker->address,
            'correo' => $this->faker->unique()->safeEmail,
            'telefono' => $this->faker->phoneNumber,
            'whatsapp' => $this->faker->phoneNumber,
            'estado' => $this->faker->boolean(80), // 80% de probabilidad de estar activo
            'sede_id' => Sede::factory(),
        ];
    }

    /**
     * Indicate that the local is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => true,
        ]);
    }

    /**
     * Indicate that the local is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => false,
        ]);
    }
}
