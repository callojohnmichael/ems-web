<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission; // Added this import
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MultimediaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view multimedia',
            'create multimedia post',
            'edit multimedia post',
            'delete multimedia post',
            'publish multimedia post',
            'react multimedia post',
            'comment multimedia post',
            'manage multimedia',
        ];

        foreach ($permissions as $p) {
            // This now correctly references Spatie's Permission model
            Permission::firstOrCreate([
                'name' => $p,
                'guard_name' => 'web'
            ]);
        }
    }
}