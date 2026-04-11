<?php

namespace Database\Seeders;

use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserFactory::new()->create([
            'name' => 'Admin',
            'email' => 'admin@app.com',
            'password' => bcrypt('password'),
        ])->assignRole('admin');

        UserFactory::new()->headOfFamily()->create([
            'name' => 'Devit NA',
            'email' => 'devit@app.com',
            'password' => bcrypt('password'),
        ]);

        UserFactory::new()->count(30)->create();
    }
}
