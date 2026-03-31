<?php

namespace Database\Factories;

use App\Models\SocialAssistanceRecipient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SocialAssistanceRecipient>
 */
class SocialAssistanceRecipientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 100000, 1000000),
            'reason' => $this->faker->sentence(),
            'bank' => $this->faker->randomElement(['BRI', 'BNI', 'MANDIRI', 'BCA', 'PERMATA']),
            'account_number' => $this->faker->unique()->bankAccountNumber(),
            'proof' => $this->faker->imageUrl(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
