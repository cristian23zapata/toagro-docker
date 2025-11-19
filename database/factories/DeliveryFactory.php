<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Delivery>
 */
class DeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id' => fake()->numberBetween(1, 50),
            'customer_name' => fake()->name(),
            'delivery_address' => fake()->address(),
            'delivery_time' => fake()->dateTimeBetween('-1 year', 'now'),
            'status' => fake()->randomElement(['pendiente', 'entregado', 'fallido'])
        ];
    }
}
