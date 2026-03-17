<?php

namespace Database\Factories;

use App\Models\FamilyMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FamilyMember>
 */
class FamilyMemberFactory extends Factory
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
            'identity_number' => $this->faker->unique()->numerify('################'),
            'gender' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'date_of_birth' => $this->faker->date(),
            'phone_number' => $this->faker->phoneNumber,
            'occupation' => $this->faker->jobTitle,
            'marital_status' => $this->faker->randomElement(['Belum Menikah', 'Menikah', 'Cerai', 'Duda', 'Janda']),
            'relation' => $this->faker->randomElement(['Suami', 'Istri', 'Anak', 'Orang Tua', 'Saudara', 'Lainnya']),
        ];
    }
}
