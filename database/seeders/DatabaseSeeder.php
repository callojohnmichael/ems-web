<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaults = [
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ];

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            array_merge($defaults, ['name' => 'Admin User', 'role' => User::ROLE_ADMIN])
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            array_merge($defaults, ['name' => 'Test User', 'role' => User::ROLE_USER])
        );

        User::updateOrCreate(
            ['email' => 'media@example.com'],
            array_merge($defaults, ['name' => 'Multimedia Staff', 'role' => User::ROLE_MULTIMEDIA_STAFF])
        );
    }
}
