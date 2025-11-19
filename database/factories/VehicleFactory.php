<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plate_number' => fake()->bothify('???-####'),
            'brand' => fake()->randomElement(['Toyota', 'Honda', 'Ford', 'BMW', 'Chevrolet']),
            'model'  => fake()->numberBetween(2000, 2025),
            'capacity' => fake()->numberBetween(100, 1000),
            'status' => fake()->randomElement(['activo', 'mantenimiento', 'inactivo']),
        ];
    }
}
