<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Day;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use App\Models\Topic;
use App\Models\CalendarEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class HomeApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_home_endpoint_returns_consolidated_frontpage_data(): void
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
                    'helplines',
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

        $helplines = $response->json('data.helplines');
        $this->assertCount(2, $helplines);
        $this->assertNotEmpty($response->json('data.social_links.whatsapp'));

        // Ensure Al-Ahram area phone is not present
        $allPhones = collect($helplines)->pluck('phones')->flatten()->toArray();
        $this->assertNotContains('+201003694690', $allPhones);
    }

    public function test_frontpage_alias_endpoint_works(): void
    {
        $response = $this->getJson('/api/v1/frontpage');
        $response->assertStatus(200);
        $this->assertArrayHasKey('stats', $response->json('data'));
    }

    public function test_jft_endpoint_returns_structured_daily_reading(): void
    {
        $response = $this->getJson('/api/v1/jft?date=2026-01-10');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'date' => '2026-01-10',
                    'title' => 'الامتنان',
                ]
            ]);

        $data = $response->json('data');
        $this->assertStringContainsString('منشور رقم 21', $data['quote_source']);
        $this->assertNotEmpty($data['thought_for_the_day']);
        $this->assertNotEmpty($data['content']);
        $this->assertNotEmpty($data['content_html']);
    }

    public function test_stats_endpoint_returns_correct_metrics(): void
    {
        $city = City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $neighborhood = Neighborhood::create([
            'city_id' => $city->id,
            'ar_name' => 'المعادي',
            'en_name' => 'Maadi',
        ]);
        $day = Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        $serviceBody = ServiceBody::create([
            'ar_name' => 'لجنة مصر',
            'en_name' => 'NA Egypt RSC',
            'type' => 'rsc',
            'location' => 'Cairo',
            'day_id' => $day->id,
            'date' => '2026-01-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
        ]);

        $user = \App\Models\User::factory()->create();

        $group = Group::create([
            'user_id' => $user->id,
            'service_body_id' => $serviceBody->id,
            'neighborhood_id' => $neighborhood->id,
            'ar_name' => 'مجموعة الأمل',
            'en_name' => 'Hope Group',
            'ar_gsr_name' => 'خادم',
            'en_gsr_name' => 'GSR',
            'email' => 'group@example.com',
            'phone' => '01012345678',
            'ar_address' => 'العنوان',
            'en_address' => 'Address',
            'location' => 'https://maps.google.com/1',
            'group_type' => 'in-person',
            'slug' => 'hope-group',
            'status' => 'active',
        ]);

        $topic = Topic::create(['ar_name' => 'موضوع', 'en_name' => 'Topic']);

        Meeting::create([
            'group_id' => $group->id,
            'day_id' => $day->id,
            'topic_id' => $topic->id,
            'start_time' => '19:00:00',
            'end_time' => '20:30:00',
            'type' => 'in-person',
            'lang' => 'arabic',
            'status' => 'available',
        ]);

        // Refresh stats cache
        Cache::flush();

        $response = $this->getJson('/api/v1/stats');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'weekly_meetings' => 1,
                    'total_meetings' => 1,
                    'groups' => 1,
                    'governorates' => 1,
                ]
            ]);
    }
}
