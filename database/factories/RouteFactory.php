<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Route>
 */
class RouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $days = fake()->numberBetween(1, 7);
        $hours = fake()->numberBetween(1, 24);
        $totalHours = ($days * 24) + $hours;

        $cities = ['Medellin', 'Chocó', 'Cartago', 'Pereira', 'Cali', 'La tebaida', 'Bogota'];

        return [
            'origin' => fake()->randomElement($cities),
            'destination' => fake()->randomElement($cities),
            'distance_km' => fake()->numberBetween(80, 1000),
            'estimated_duration' => $totalHours,
        ];
    }
}
