<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a test user
        $user = User::first() ?? User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Get or create venues
        $venues = Venue::all();
        if ($venues->isEmpty()) {
            // Create sample venues if none exist
            $venues = collect([
                ['name' => 'Main Auditorium', 'address' => '123 Main St', 'capacity' => 500],
                ['name' => 'Conference Room A', 'address' => '456 Oak Ave', 'capacity' => 50],
                ['name' => 'Outdoor Pavilion', 'address' => '789 Park Rd', 'capacity' => 200],
            ])->map(function ($venueData) {
                return Venue::create($venueData);
            });
        }

        // Create sample events with different statuses
        $events = [
            // Published Events (ongoing and upcoming)
            [
                'title' => 'Annual Tech Conference 2024',
                'description' => 'A comprehensive technology conference featuring the latest innovations in AI, cloud computing, and software development.',
                'status' => 'published',
                'start_at' => Carbon::now()->addDays(5),
                'end_at' => Carbon::now()->addDays(5)->addHours(8),
                'venue_id' => $venues->first()->id,
                'number_of_participants' => 300,
            ],
            [
                'title' => 'Summer Music Festival',
                'description' => 'An outdoor music festival featuring local bands and food vendors.',
                'status' => 'published',
                'start_at' => Carbon::now()->addDays(10),
                'end_at' => Carbon::now()->addDays(10)->addHours(12),
                'venue_id' => $venues->last()->id,
                'number_of_participants' => 150,
            ],
            [
                'title' => 'Startup Pitch Night',
                'description' => 'Entrepreneurs pitch their business ideas to investors and judges.',
                'status' => 'published',
                'start_at' => Carbon::now()->addDays(3),
                'end_at' => Carbon::now()->addDays(3)->addHours(4),
                'venue_id' => $venues->get(1)->id,
                'number_of_participants' => 75,
            ],

            // Approved Events (ready to be published)
            [
                'title' => 'Leadership Workshop',
                'description' => 'A professional development workshop focused on leadership skills and team management.',
                'status' => 'approved',
                'start_at' => Carbon::now()->addWeeks(2),
                'end_at' => Carbon::now()->addWeeks(2)->addHours(6),
                'venue_id' => $venues->get(1)->id,
                'number_of_participants' => 30,
            ],
            [
                'title' => 'Product Launch Event',
                'description' => 'Official launch event for our new product line with demonstrations and networking.',
                'status' => 'approved',
                'start_at' => Carbon::now()->addWeeks(3),
                'end_at' => Carbon::now()->addWeeks(3)->addHours(3),
                'venue_id' => $venues->first()->id,
                'number_of_participants' => 200,
            ],
            [
                'title' => 'Customer Appreciation Gala',
                'description' => 'An elegant evening to thank our valued customers with dinner and entertainment.',
                'status' => 'approved',
                'start_at' => Carbon::now()->addWeeks(4),
                'end_at' => Carbon::now()->addWeeks(4)->addHours(5),
                'venue_id' => $venues->first()->id,
                'number_of_participants' => 100,
            ],

            // Pending Approval Events (awaiting review)
            [
                'title' => 'Team Building Retreat',
                'description' => 'A two-day retreat focused on team bonding and collaborative activities.',
                'status' => 'pending_approvals',
                'start_at' => Carbon::now()->addMonth(),
                'end_at' => Carbon::now()->addMonth()->addDays(2),
                'venue_id' => $venues->last()->id,
                'number_of_participants' => 50,
            ],
            [
                'title' => 'Industry Networking Mixer',
                'description' => 'Monthly networking event for professionals in the tech industry.',
                'status' => 'pending_approvals',
                'start_at' => Carbon::now()->addWeek(),
                'end_at' => Carbon::now()->addWeek()->addHours(3),
                'venue_id' => $venues->get(1)->id,
                'number_of_participants' => 40,
            ],
            [
                'title' => 'Charity Fundraiser Dinner',
                'description' => 'Annual fundraising event to support local community initiatives.',
                'status' => 'pending_approvals',
                'start_at' => Carbon::now()->addWeeks(2),
                'end_at' => Carbon::now()->addWeeks(2)->addHours(4),
                'venue_id' => $venues->first()->id,
                'number_of_participants' => 120,
            ],
        ];

        foreach ($events as $eventData) {
            Event::create(array_merge($eventData, [
                'requested_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ Created sample events with different statuses:');
        $this->command->info('   - 3 Published events');
        $this->command->info('   - 3 Approved events');
        $this->command->info('   - 3 Pending approval events');
    }
}
