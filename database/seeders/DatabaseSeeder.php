<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => User::ROLE_ADMIN,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'role' => User::ROLE_USER,
        ]);

        User::factory()->create([
            'name' => 'Multimedia Staff',
            'email' => 'media@example.com',
            'role' => User::ROLE_MULTIMEDIA_STAFF,
        ]);
    }
}
