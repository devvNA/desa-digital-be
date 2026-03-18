<?php

namespace Database\Seeders;

use App\Models\HeadOfFamily;
use App\Models\SocialAssistance;
use App\Models\SocialAssistanceRecipient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialAssistanceRecipientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $socialAssistance = SocialAssistance::all();
        $headofFamilies = HeadOfFamily::all();

        foreach ($socialAssistance as $socialAssistance) {
            foreach ($headofFamilies as $headofFamily) {
                SocialAssistanceRecipient::factory()->create([
                    'head_of_family_id' => $headofFamily->id,
                    'social_assistance_id' => $socialAssistance->id,
                ]);
            }
        }
    }
}
