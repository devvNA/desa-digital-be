<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
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
            'name' => $this->faker->randomElement(['Belajar Bahasa Inggris', 'Jalan Sehat', 'Kerja Bakti', 'Pengajian']) . ' ' . $this->faker->city(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(100000, 1000000),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'time' => $this->faker->time('H:i'),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
