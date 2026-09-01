<?php

namespace Tests\Feature\Api;

use App\Models\CalendarEvent;
use App\Models\City;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CompositeApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_home_endpoint_returns_aggregated_data(): void
    {
        $response = $this->getJson('/api/v1/home');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'stats' => [
                        'weekly_meetings',
                        'total_meetings',
                        'in_person_groups',
                        'online_groups',
                        'groups',
                        'total_groups',
                        'governorates',
                        'cities',
                        'upcoming_events',
                    ],
                    'jft' => [
                        'date',
                        'page_date',
                        'title',
                        'quote',
                        'quote_source',
                        'content',
                        'thought_for_the_day',
                        'content_html',
                    ],
                    'helplines' => [
                        '*' => [
                            'region',
                            'region_ar',
                            'phones',
                            'whatsapp',
                            'hours',
                            'hours_ar',
                        ]
                    ],
                    'social_links' => [
                        'facebook',
                        'instagram',
                        'tiktok',
                        'whatsapp',
                        'email',
                    ],
                    'upcoming_events',
                ]
            ]);
    }

    public function test_frontpage_alias_matches_home_endpoint(): void
    {
        $response = $this->getJson('/api/v1/frontpage');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'stats',
                    'jft',
                    'helplines',
                    'social_links',
                    'upcoming_events',
                ]
            ]);
    }

    public function test_home_endpoint_accepts_specific_date_parameter(): void
    {
        $response = $this->getJson('/api/v1/home?date=2026-08-17');
        $response->assertStatus(200)
            ->assertJsonPath('data.jft.date', '2026-08-17');
    }

    public function test_jft_endpoint_returns_structured_daily_reading(): void
    {
        $response = $this->getJson('/api/v1/jft?date=2026-01-10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'date',
                    'page_date',
                    'title',
                    'quote',
                    'quote_source',
                    'content',
                    'thought_for_the_day',
                    'content_html',
                ]
            ])
            ->assertJsonPath('data.date', '2026-01-10');
    }

    public function test_stats_endpoint_returns_live_computed_counters(): void
    {
        $user = User::factory()->create();
        $day = \App\Models\Day::create([
            'ar_name' => 'السبت',
            'en_name' => 'Saturday',
        ]);

        $serviceBody = ServiceBody::create([
            'ar_name' => 'لجنة القاهرة',
            'en_name' => 'Cairo Area',
            'day_id' => $day->id,
            'start_time' => '18:00:00',
            'end_time' => '20:00:00',
            'location' => 'Cairo',
        ]);

        $city = City::create([
            'ar_name' => 'القاهرة',
            'en_name' => 'Cairo',
            'user_id' => $user->id,
        ]);

        $neighborhood = Neighborhood::create([
            'city_id' => $city->id,
            'ar_name' => 'المعادي',
            'en_name' => 'Maadi',
            'user_id' => $user->id,
        ]);

        $group = Group::create([
            'ar_name' => 'مجموعة الأمل',
            'en_name' => 'Hope Group',
            'ar_gsr_name' => 'أحمد',
            'en_gsr_name' => 'Ahmed',
            'phone' => '+201000000000',
            'location' => 'https://maps.google.com/?q=30.0444,31.2357',
            'ar_address' => 'وسط البلد، القاهرة',
            'en_address' => 'Downtown, Cairo',
            'service_body_id' => $serviceBody->id,
            'neighborhood_id' => $neighborhood->id,
            'user_id' => $user->id,
            'group_type' => 'in_person',
        ]);

        CalendarEvent::create([
            'user_id' => $user->id,
            'title' => 'Upcoming Assembly',
            'start' => Carbon::now()->addDays(3),
            'end' => Carbon::now()->addDays(3)->addHours(2),
            'color' => '#00698f',
        ]);

        $response = $this->getJson('/api/v1/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'weekly_meetings',
                    'total_meetings',
                    'in_person_groups',
                    'online_groups',
                    'groups',
                    'total_groups',
                    'governorates',
                    'cities',
                    'upcoming_events',
                ]
            ])
            ->assertJsonPath('data.cities', 1)
            ->assertJsonPath('data.in_person_groups', 1)
            ->assertJsonPath('data.upcoming_events', 1);
    }
}
