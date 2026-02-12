<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campus;
use App\Models\Facility;
use App\Models\Amenity;

class CampusFacilitySeeder extends Seeder
{
    public function run(): void
    {
        // Create Amenities
        $amenities = [
            ['name' => 'WiFi', 'description' => 'High-speed internet connectivity'],
            ['name' => 'Projector', 'description' => 'Multimedia projection system'],
            ['name' => 'Sound System', 'description' => 'Professional audio equipment'],
            ['name' => 'Air Conditioning', 'description' => 'Climate control system'],
            ['name' => 'Parking', 'description' => 'Parking facilities'],
            ['name' => 'Restrooms', 'description' => 'Public restroom facilities'],
            ['name' => 'Catering', 'description' => 'Food and beverage service'],
            ['name' => 'Wheelchair Access', 'description' => 'Accessible for persons with disabilities'],
            ['name' => 'Stage', 'description' => 'Elevated performance platform'],
            ['name' => 'Bleachers', 'description' => 'Tiered seating for spectators'],
            ['name' => 'LED Display', 'description' => 'Large screens for presentations'],
            ['name' => 'Conference Table', 'description' => 'Professional meeting table'],
            ['name' => 'Computers', 'description' => 'Computer workstations'],
            ['name' => 'Sound Proofing', 'description' => 'Acoustic isolation'],
            ['name' => 'Outdoor Lighting', 'description' => 'External illumination'],
            ['name' => 'Moveable Stage Access', 'description' => 'Flexible staging area'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::firstOrCreate(['name' => $amenity['name']], $amenity);
        }

        // Campus 1: Main Campus
        $mainCampus = Campus::firstOrCreate(
            ['name' => 'Main Campus'],
            [
                'description' => 'Primary academic and administrative center',
                'location' => 'Central Location',
            ]
        );

        $facilitiesMain = [
            [
                'name' => 'Main Auditorium',
                'description' => 'Large auditorium for presentations and events',
                'capacity' => 500,
                'amenities' => ['Projector', 'Sound System', 'WiFi', 'Air Conditioning', 'Wheelchair Access', 'Restrooms']
            ],
            [
                'name' => 'Conference Room A',
                'description' => 'Medium-sized conference room',
                'capacity' => 30,
                'amenities' => ['Conference Table', 'WiFi', 'Projector', 'Air Conditioning', 'Catering']
            ],
            [
                'name' => 'Multimedia Room',
                'description' => 'Equipped for digital presentations',
                'capacity' => 60,
                'amenities' => ['Computers', 'Sound Proofing', 'Projector', 'WiFi', 'LED Display']
            ],
        ];

        foreach ($facilitiesMain as $facilityData) {
            $amenityNames = $facilityData['amenities'];
            unset($facilityData['amenities']);
            
            $facility = Facility::firstOrCreate(
                ['campus_id' => $mainCampus->id, 'name' => $facilityData['name']],
                $facilityData
            );

            $amenityIds = Amenity::whereIn('name', $amenityNames)->pluck('id');
            $facility->amenities()->sync($amenityIds);
        }

        // Campus 2: Sports Complex
        $sportsCampus = Campus::firstOrCreate(
            ['name' => 'Sports Complex'],
            [
                'description' => 'Athletic and recreational facilities',
                'location' => 'East Campus',
            ]
        );

        $facilitiesSports = [
            [
                'name' => 'School Gymnasium',
                'description' => 'Sports venue with court facilities',
                'capacity' => 1200,
                'amenities' => ['Sound System', 'Bleachers', 'LED Display', 'Parking', 'Wheelchair Access', 'Catering']
            ],
            [
                'name' => 'Open Field / Quadrangle',
                'description' => 'Outdoor events space',
                'capacity' => 2000,
                'amenities' => ['Outdoor Lighting', 'Moveable Stage Access', 'Parking', 'Catering']
            ],
        ];

        foreach ($facilitiesSports as $facilityData) {
            $amenityNames = $facilityData['amenities'];
            unset($facilityData['amenities']);
            
            $facility = Facility::firstOrCreate(
                ['campus_id' => $sportsCampus->id, 'name' => $facilityData['name']],
                $facilityData
            );

            $amenityIds = Amenity::whereIn('name', $amenityNames)->pluck('id');
            $facility->amenities()->sync($amenityIds);
        }
    }
}
