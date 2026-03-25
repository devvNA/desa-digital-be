<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@app.com',
            'password' => bcrypt('password'),
        ])->assignRole('admin');

        User::create([
            'name' => 'Kepala Keluarga',
            'email' => 'headoffamily@app.com',
            'password' => bcrypt('password'),
        ])->assignRole('head-of-family');

        UserFactory::new()->count(10)->create();
    }
}
