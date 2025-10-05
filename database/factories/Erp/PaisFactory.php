<?php

namespace Database\Factories\Erp;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Erp\Pais>
 */
class PaisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'descripcion' => $this->faker->country(),
            'codigo' => $this->faker->countryCode(),
            'moneda' => $this->faker->currencyCode(),
            'codigo_moneda' => $this->faker->currencyCode(),
            'simbolo_moneda' => $this->faker->randomElement(['$', '€', '£', '¥', '₹', '₩', '₽', '₺', '₪', '₫']),
            'estado' => $this->faker->boolean(90), // 90% de probabilidad de ser true
        ];
    }
}
