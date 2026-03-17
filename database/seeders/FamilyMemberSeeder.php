<?php

namespace Database\Seeders;

use App\Models\FamilyMember;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FamilyMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FamilyMember::factory()->count(10)->create();
    }
}
