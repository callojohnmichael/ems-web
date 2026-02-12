<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\CustodianMaterial;
use App\Models\Event;
use App\Models\EventCustodianRequest;
use App\Models\Venue;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Venues (School Specific)
        // -----------------------------------------------------------
        $venues = [
            [
                'name' => 'Main Auditorium',
                'address' => 'Academic Building, 2nd Floor',
                'capacity' => 500,
                'facilities' => 'Stage, Sound System, Air Conditioning, Projector',
            ],
            [
                'name' => 'School Gymnasium',
                'address' => 'Sports Complex, East Campus',
                'capacity' => 1200,
                'facilities' => 'Basketball Court, Bleachers, Sound System, Scoreboard',
            ],
            [
                'name' => 'Conference Room A',
                'address' => 'Administration Wing, Ground Floor',
                'capacity' => 30,
                'facilities' => 'Conference Table, Wi-Fi, LED Display',
            ],
            [
                'name' => 'Open Field / Quadrangle',
                'address' => 'Central Campus Grounds',
                'capacity' => 2000,
                'facilities' => 'Outdoor Lighting, Moveable Stage Access',
            ],
            [
                'name' => 'Multimedia Room',
                'address' => 'Library Building, 3rd Floor',
                'capacity' => 60,
                'facilities' => 'Computers, Sound Proofing, Projector, High-speed Internet',
            ],
        ];

        foreach ($venues as $venue) {
            Venue::create($venue);
        }

        // 2. Seed Employees
        // -----------------------------------------------------------
        $employees = [
            ['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john@example.com', 'department' => 'IT', 'employee_id_number' => 'EMP-001'],
            ['first_name' => 'Jane', 'last_name' => 'Smith', 'email' => 'jane@example.com', 'department' => 'HR', 'employee_id_number' => 'EMP-002'],
            ['first_name' => 'Michael', 'last_name' => 'Brown', 'email' => 'mike@example.com', 'department' => 'Maintenance', 'employee_id_number' => 'EMP-003'],
            ['first_name' => 'Sarah', 'last_name' => 'Wilson', 'email' => 'sarah@example.com', 'department' => 'Marketing', 'employee_id_number' => 'EMP-004'],
        ];

        foreach ($employees as $emp) {
            Employee::create($emp);
        }

        // 3. Seed Custodian Materials
        // -----------------------------------------------------------
        $materials = [
            ['name' => 'Folding Chairs', 'category' => 'Furniture', 'stock' => 150],
            ['name' => 'Sound System', 'category' => 'Electronics', 'stock' => 5],
            ['name' => 'Projector Screen', 'category' => 'Electronics', 'stock' => 3],
            ['name' => 'Whiteboard Markers', 'category' => 'Office Supplies', 'stock' => 50],
            ['name' => 'Extension Cords', 'category' => 'Hardware', 'stock' => 20],
            ['name' => 'Monoblock Tables', 'category' => 'Furniture', 'stock' => 40],
        ];

        foreach ($materials as $mat) {
            CustodianMaterial::create($mat);
        }

        // 4. Seed Sample Requests (Only if an event exists)
        // -----------------------------------------------------------
        $event = Event::first();

        if ($event) {
            $material = CustodianMaterial::where('name', 'Folding Chairs')->first();
            
            if ($material) {
                EventCustodianRequest::create([
                    'event_id' => $event->id,
                    'custodian_material_id' => $material->id,
                    'quantity' => 20,
                    'status' => 'pending',
                ]);
            }

            $soundSystem = CustodianMaterial::where('name', 'Sound System')->first();
            if ($soundSystem) {
                EventCustodianRequest::create([
                    'event_id' => $event->id,
                    'custodian_material_id' => $soundSystem->id,
                    'quantity' => 1,
                    'status' => 'approved',
                ]);
            }
        }
    }
}