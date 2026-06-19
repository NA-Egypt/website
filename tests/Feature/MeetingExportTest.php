<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Day;
use App\Models\ServiceBody;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\MeetingExportWizard;

class MeetingExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
    }

    public function test_download_export_by_service_body()
    {
        $day = Day::first(); // automatically seeded by migration

        $sb = ServiceBody::create([
            'ar_name' => 'الهيئة الخدمية',
            'en_name' => 'Service Body',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Location',
        ]);

        $city = City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $neighborhood = Neighborhood::create([
            'ar_name' => 'حي',
            'en_name' => 'Neighborhood',
            'city_id' => $city->id,
        ]);

        $user = User::factory()->create();

        $group = Group::create([
            'ar_name' => 'المجموعة',
            'en_name' => 'Group',
            'ar_gsr_name' => 'GSR',
            'en_gsr_name' => 'GSR',
            'email' => 'g@example.com',
            'phone' => '12345',
            'location' => 'https://maps.google.com',
            'ar_address' => 'العنوان',
            'en_address' => 'Address',
            'group_type' => 'open',
            'service_body_id' => $sb->id,
            'neighborhood_id' => $neighborhood->id,
            'user_id' => $user->id,
        ]);

        $topic = Topic::create([
            'ar_name' => 'الموضوع',
            'en_name' => 'Topic',
        ]);

        $meeting = Meeting::create([
            'group_id' => $group->id,
            'day_id' => $day->id,
            'topic_id' => $topic->id,
            'start_time' => '18:00:00',
            'end_time' => '19:30:00',
            'type' => 'open',
            'status' => 'available',
        ]);

        $response = $this->get(route('meetings.export.download', [
            'export_type' => 'service_bodies',
            'service_bodies' => [$sb->id],
            'fields' => ['topic', 'time'],
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_download_export_by_cities()
    {
        $day = Day::first(); // automatically seeded by migration

        $sb = ServiceBody::create([
            'ar_name' => 'الهيئة الخدمية',
            'en_name' => 'Service Body',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Location',
        ]);

        $city = City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $neighborhood = Neighborhood::create([
            'ar_name' => 'حي',
            'en_name' => 'Neighborhood',
            'city_id' => $city->id,
        ]);

        $user = User::factory()->create();

        $group = Group::create([
            'ar_name' => 'المجموعة',
            'en_name' => 'Group',
            'ar_gsr_name' => 'GSR',
            'en_gsr_name' => 'GSR',
            'email' => 'g@example.com',
            'phone' => '12345',
            'location' => 'https://maps.google.com',
            'ar_address' => 'العنوان',
            'en_address' => 'Address',
            'group_type' => 'open',
            'service_body_id' => $sb->id,
            'neighborhood_id' => $neighborhood->id,
            'user_id' => $user->id,
        ]);

        $topic = Topic::create([
            'ar_name' => 'الموضوع',
            'en_name' => 'Topic',
        ]);

        $meeting = Meeting::create([
            'group_id' => $group->id,
            'day_id' => $day->id,
            'topic_id' => $topic->id,
            'start_time' => '18:00:00',
            'end_time' => '19:30:00',
            'type' => 'open',
            'status' => 'available',
        ]);

        $response = $this->get(route('meetings.export.download', [
            'export_type' => 'cities',
            'cities' => [$city->id],
            'fields' => ['topic', 'time'],
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_wizard_filters_out_cities_with_no_meetings_or_virtual_meetings()
    {
        $day = Day::first();

        // City A: Has active non-virtual meetings
        $cityA = City::create(['ar_name' => 'مدينة أ', 'en_name' => 'City A']);
        $neighA = Neighborhood::create(['ar_name' => 'حي أ', 'en_name' => 'Neigh A', 'city_id' => $cityA->id]);
        $sb = ServiceBody::create([
            'ar_name' => 'كيان', 'en_name' => 'SB', 'day_id' => $day->id, 'date' => '2026-06-01',
            'start_time' => '10:00:00', 'end_time' => '12:00:00', 'location' => 'Loc'
        ]);
        $groupA = Group::create([
            'ar_name' => 'م أ', 'en_name' => 'G A', 'ar_gsr_name' => 'GSR', 'en_gsr_name' => 'GSR',
            'email' => 'a@example.com', 'phone' => '123', 'location' => 'Loc', 'ar_address' => 'Addr', 'en_address' => 'Addr',
            'group_type' => 'in-person', 'service_body_id' => $sb->id, 'neighborhood_id' => $neighA->id, 'user_id' => User::factory()->create()->id
        ]);
        $topic = Topic::create(['ar_name' => 'موضوع', 'en_name' => 'Topic']);
        Meeting::create([
            'group_id' => $groupA->id, 'day_id' => $day->id, 'topic_id' => $topic->id,
            'start_time' => '10:00:00', 'end_time' => '11:00:00', 'type' => 'open', 'status' => 'available'
        ]);

        // City B: Has only virtual/online meetings
        $cityB = City::create(['ar_name' => 'مدينة ب', 'en_name' => 'City B']);
        $neighB = Neighborhood::create(['ar_name' => 'حي ب', 'en_name' => 'Neigh B', 'city_id' => $cityB->id]);
        $groupB = Group::create([
            'ar_name' => 'م ب', 'en_name' => 'G B', 'ar_gsr_name' => 'GSR', 'en_gsr_name' => 'GSR',
            'email' => 'b@example.com', 'phone' => '123', 'location' => 'Loc', 'ar_address' => 'Addr', 'en_address' => 'Addr',
            'group_type' => 'online', 'service_body_id' => $sb->id, 'neighborhood_id' => $neighB->id, 'user_id' => User::factory()->create()->id
        ]);
        Meeting::create([
            'group_id' => $groupB->id, 'day_id' => $day->id, 'topic_id' => $topic->id,
            'start_time' => '10:00:00', 'end_time' => '11:00:00', 'type' => 'open', 'status' => 'available'
        ]);

        // City C: Has no meetings at all
        $cityC = City::create(['ar_name' => 'مدينة ج', 'en_name' => 'City C']);

        // Test Livewire component properties
        Livewire::test(MeetingExportWizard::class)
            ->assertSet('cities', function($cities) use ($cityA, $cityB, $cityC) {
                $ids = collect($cities)->pluck('id')->all();
                return in_array($cityA->id, $ids) && !in_array($cityB->id, $ids) && !in_array($cityC->id, $ids);
            });
    }
}
