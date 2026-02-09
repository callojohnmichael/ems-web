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
            ['email' => 'armojallasmichaeljhan0314@gmail.com'],
            array_merge($defaults, ['name' => 'Admin User'])
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            array_merge($defaults, ['name' => 'Test User'])
        );

        User::updateOrCreate(
            ['email' => 'media@example.com'],
            array_merge($defaults, ['name' => 'Multimedia Staff'])
        );

        $this->call(RoleAndPermissionSeeder::class);
    }
}
