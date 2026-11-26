<?php

namespace Tests\Feature\Api;

use App\Models\CalendarEvent;
use App\Models\City;
use App\Models\CommitteeReport;
use App\Models\Day;
use App\Models\Event;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Neighborhood;
use App\Models\Option;
use App\Models\ScMeeting;
use App\Models\ServiceBody;
use App\Models\ServiceBodyAgenda;
use App\Models\ServiceCommittee;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApiPaginationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));

        $day = Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        $city = City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo', 'user_id' => $this->admin->id]);
        $neighborhood = Neighborhood::create(['city_id' => $city->id, 'ar_name' => 'المعادي', 'en_name' => 'Maadi', 'user_id' => $this->admin->id]);
        $topic = Topic::create(['ar_name' => 'مفتوح', 'en_name' => 'Open']);
        Option::create(['ar_name' => 'خيار', 'en_name' => 'Option']);

        $sb = ServiceBody::create([
            'ar_name' => 'لجنة القاهرة',
            'en_name' => 'Cairo Area',
            'day_id' => $day->id,
            'start_time' => '18:00:00',
            'end_time' => '20:00:00',
            'location' => 'Cairo',
        ]);

        $group = Group::create([
            'ar_name' => 'مجموعة الأمل',
            'en_name' => 'Hope Group',
            'ar_gsr_name' => 'أحمد',
            'en_gsr_name' => 'Ahmed',
            'phone' => '+201000000000',
            'location' => 'https://maps.google.com/?q=30,31',
            'ar_address' => 'المعادي',
            'en_address' => 'Maadi',
            'service_body_id' => $sb->id,
            'neighborhood_id' => $neighborhood->id,
            'user_id' => $this->admin->id,
            'group_type' => 'in_person',
        ]);

        Meeting::create([
            'group_id' => $group->id,
            'day_id' => $day->id,
            'topic_id' => $topic->id,
            'start_time' => '18:00',
            'end_time' => '19:00',
            'type' => 'in_person',
            'lang' => 'arabic',
            'status' => 'available',
        ]);

        $committee = ServiceCommittee::create(['ar_name' => 'لجنة', 'en_name' => 'Committee']);
        ScMeeting::create(['service_committee_id' => $committee->id, 'day_id' => $day->id, 'week_number' => 1, 'time' => '19:00']);
        Event::create(['name' => 'Event', 'description' => 'Desc', 'date' => '2026-08-01', 'service_body_id' => $sb->id, 'day_id' => $day->id]);
        CalendarEvent::create(['title' => 'Cal', 'start' => '2026-08-01 10:00:00', 'end' => '2026-08-01 12:00:00', 'color' => '#3788d8', 'user_id' => $this->admin->id]);
        CommitteeReport::create(['service_committee_id' => $committee->id, 'meeting_date' => '2026-08-01', 'status' => 'approved', 'user_id' => $this->admin->id]);
        ServiceBodyAgenda::create(['service_body_id' => $sb->id, 'agenda_date' => '2026-08-01', 'meeting_date' => '2026-08-15', 'body' => [['headline' => 'H', 'content' => 'C']], 'status' => 'approved', 'is_exceptional' => true]);
    }

    public function test_api_index_endpoints_paginate_by_default_with_50_per_page(): void
    {
        $publicEndpoints = [
            '/api/v1/service-bodies',
            '/api/v1/sc-meetings',
            '/api/v1/events',
            '/api/v1/calendar-events',
            '/api/v1/service-body-agendas',
            '/api/v1/meetings',
            '/api/v1/cities',
            '/api/v1/days',
            '/api/v1/options',
            '/api/v1/topics',
        ];

        foreach ($publicEndpoints as $endpoint) {
            $this->getJson($endpoint)
                ->assertStatus(200)
                ->assertJsonStructure(['data', 'meta', 'links'])
                ->assertJsonPath('meta.per_page', 50);
        }

        Sanctum::actingAs($this->admin);
        $this->getJson('/api/v1/committee-reports')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonPath('meta.per_page', 50);
    }

    public function test_per_page_is_validated_and_capped_at_200(): void
    {
        $this->getJson('/api/v1/meetings?per_page=10')
            ->assertStatus(200)
            ->assertJsonPath('meta.per_page', 10);

        $this->getJson('/api/v1/meetings?per_page=201')
            ->assertStatus(422)
            ->assertJsonPath('code', 'validation_failed')
            ->assertJsonValidationErrors(['per_page']);

        $this->getJson('/api/v1/events?per_page=0')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);
    }

    public function test_small_lookup_lists_accept_all_1_to_return_unpaginated_data(): void
    {
        foreach (['/api/v1/cities', '/api/v1/days', '/api/v1/options', '/api/v1/topics', '/api/v1/service-bodies'] as $endpoint) {
            $response = $this->getJson($endpoint.'?all=1');
            $response->assertStatus(200)
                ->assertJsonStructure(['data'])
                ->assertJsonMissingPath('meta');
        }
    }
}
