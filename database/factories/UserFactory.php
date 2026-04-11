<?php

namespace Database\Factories;

use App\Models\HeadOfFamily;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'profile_picture' => $this->faker->imageUrl(300, 300, 'people'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function headOfFamily(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('head-of-family');

            HeadOfFamily::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'profile_picture' => $user->profile_picture,
                    'identity_number' => fake()->unique()->numerify('################'),
                    'gender' => fake()->randomElement(['male', 'female']),
                    'date_of_birth' => fake()->dateTimeBetween('-60 years', 'now'),
                    'phone_number' => fake()->unique()->phoneNumber(),
                    'occupation' => fake()->jobTitle(),
                    'marital_status' => fake()->randomElement(['single', 'married']),
                ]
            );
        });
    }
}
