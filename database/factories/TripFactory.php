<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => fake()->numberBetween(1, 50),
            'driver_id' => fake()->numberBetween(1, 50),
            'route_id'  => fake()->numberBetween(1, 50),
            'start_time' => fake()->dateTimeBetween('-1 year', 'now'),
            'end_time' => fake()->dateTimeBetween('-1 year', 'now'),
            'status' => fake()->randomElement(['pendiente', 'en_progreso', 'completado', 'cancelado'])
        ];
    }
}
