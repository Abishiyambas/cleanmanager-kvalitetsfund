<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Indgang', 'Kantine', 'Toilet', 'Mødelokale', 'Gang']).' '.fake()->unique()->numberBetween(1, 999),
            'address' => fake()->streetAddress(),
            'description' => fake()->sentence(),
        ];
    }
}
