<?php

namespace Database\Seeders;

use Database\Factories\DevelopmentFactory;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.a
     */
    public function run(): void
    {
        DevelopmentFactory::new()->count(10)->create();
    }
}
