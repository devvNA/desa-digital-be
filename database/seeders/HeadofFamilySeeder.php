<?php

namespace Database\Seeders;

use Database\Factories\FamilyMemberFactory;
use Database\Factories\HeadofFamilyFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class HeadofFamilySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserFactory::new()->count(20)->headOfFamily()->create()->each(function ($user) {
            $headOfFamily = $user->headOfFamily;

            FamilyMemberFactory::new()->count(4)->create([
                'head_of_family_id' => $headOfFamily->id,
                'user_id' => UserFactory::new()->create()->id,
            ]);
        });
    }
}
