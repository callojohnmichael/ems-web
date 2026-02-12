<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Resource;
use Illuminate\Database\Seeder;

class SchoolSystemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Employees (Committee Members)
        $employees = [
            [
                'employee_id_number' => 'EMP-2024-001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@school.edu',
                'department' => 'ICT Department',
            ],
            [
                'employee_id_number' => 'EMP-2024-002',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@school.edu',
                'department' => 'Finance',
            ],
            [
                'employee_id_number' => 'EMP-2024-003',
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'email' => 'm.brown@school.edu',
                'department' => 'Student Affairs',
            ],
            [
                'employee_id_number' => 'EMP-2024-004',
                'first_name' => 'Sarah',
                'last_name' => 'Wilson',
                'email' => 's.wilson@school.edu',
                'department' => 'Maintenance',
            ],
        ];

        foreach ($employees as $employee) {
            Employee::updateOrCreate(['employee_id_number' => $employee['employee_id_number']], $employee);
        }

        // 2. Seed Resources (Logistics/Equipment)
        $resources = [
            ['name' => 'Projector', 'type' => 'Electronic', 'quantity' => 10],
            ['name' => 'Sound System (Portable)', 'type' => 'Audio', 'quantity' => 5],
            ['name' => 'Plastic Chairs', 'type' => 'Furniture', 'quantity' => 200],
            ['name' => 'Monoblock Tables', 'type' => 'Furniture', 'quantity' => 50],
            ['name' => 'Microphone (Wireless)', 'type' => 'Audio', 'quantity' => 8],
            ['name' => 'Laptop', 'type' => 'Electronic', 'quantity' => 15],
        ];

        foreach ($resources as $resource) {
            Resource::updateOrCreate(['name' => $resource['name']], $resource);
        }
    }
}