<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Driver>
 */
class DriverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'license_number' => fake()->numberBetween(100000000, 190000000),
            'user_id' => fake()->numberBetween(1, 50),
            'phone'  => fake()->numberBetween(3010000000, 3219999999),
            'status' => fake()->randomElement(['activo', 'suspendido']),
        ];
    }
}
