<?php

namespace Database\Seeders;

use App\Models\Event;
use Database\Factories\EventFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventFactory::new()->count(5)->create();
    }
}
