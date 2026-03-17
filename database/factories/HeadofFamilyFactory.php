<?php

namespace Database\Factories;

use App\Models\HeadofFamily;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HeadofFamily>
 */
class HeadofFamilyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'profile_picture' => $this->faker->imageUrl(),
            // 'profile_picture' => $this->faker->image('public/storage/head_of_families', 640, 480, 'people', false),
            'identity_number' => $this->faker->unique()->numerify('################'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'date_of_birth' => $this->faker->dateTimeBetween('-60 years', 'now'),
            'phone_number' => $this->faker->unique()->phoneNumber(),
            'occupation' => $this->faker->jobTitle(),
            'marital_status' => $this->faker->randomElement(['single', 'married']),
        ];
    }
}
