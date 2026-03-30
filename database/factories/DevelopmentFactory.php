<?php

namespace Database\Factories;

use App\Models\Development;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Development>
 */
class DevelopmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'thumbnail' => $this->faker->imageUrl(),
            'name' => $this->faker->randomElement(['Pembangunan Jalan', 'Pembangunan Jembatan', 'Pembangunan Gedung Olahraga', 'Pembangunan Taman', 'Pembangunan Toilet']).' '.$this->faker->city(),
            'description' => $this->faker->paragraph(),
            'person_in_charge' => $this->faker->name(),
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'amount' => $this->faker->randomFloat(2, 1000000, 100000000),
            'status' => $this->faker->randomElement(['ongoing', 'completed']),
        ];
    }
}
