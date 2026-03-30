<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\HeadOfFamily;
use Database\Factories\EventParticipantFactory;
use Illuminate\Database\Seeder;

class EventParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();
        $headofFamilies = HeadOfFamily::all();

        foreach ($events as $event) {
            foreach ($headofFamilies as $headofFamily) {
                EventParticipantFactory::new()->create([
                    'event_id' => $event->id,
                    'head_of_family_id' => $headofFamily->id,
                ]);
            }
        }
    }
}
