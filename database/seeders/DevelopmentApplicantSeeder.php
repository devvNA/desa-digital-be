<?php

namespace Database\Seeders;

use App\Models\Development;
use App\Models\User;
use Database\Factories\DevelopmentApplicantFactory;
use Illuminate\Database\Seeder;

class DevelopmentApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $development = Development::all();
        $users = User::all();

        foreach ($development as $development) {
            foreach ($users as $user) {
                DevelopmentApplicantFactory::new()->create([
                    'development_id' => $development->id,
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
