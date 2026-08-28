<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceBody;
use App\Models\Group;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\Day;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceBodyMapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
    }

    public function test_public_service_body_map_page_is_accessible()
    {
        $response = $this->get('/service-bodies/map');
        $response->assertStatus(200);
        $response->assertSee('ServiceBodyMap');
    }

    public function test_api_service_body_map_data_returns_valid_structure_and_excludes_al_ahram()
    {
        $day = Day::create(['ar_name' => 'الجمعة', 'en_name' => 'Friday']);
        $city = City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $neighborhood = Neighborhood::create(['ar_name' => 'المعادي', 'en_name' => 'Maadi', 'city_id' => $city->id, 'latitude' => 29.9602, 'longitude' => 31.2569]);
        
        $sb1 = ServiceBody::create(['id' => 1, 'ar_name' => 'منتدى شمال شرق', 'en_name' => 'North East Cairo GSF', 'day_id' => $day->id, 'start_time' => '17:00:00', 'end_time' => '19:00:00', 'location' => 'مقر']);
        $sb2 = ServiceBody::create(['id' => 2, 'ar_name' => 'منتدى الأهرام', 'en_name' => 'Al Ahram GSF', 'day_id' => $day->id, 'start_time' => '17:00:00', 'end_time' => '19:00:00', 'location' => 'أونلاين']);
        $sb3 = ServiceBody::create(['id' => 3, 'ar_name' => 'منطقة وجه بحري', 'en_name' => 'Lower Egypt ASC', 'day_id' => $day->id, 'start_time' => '17:00:00', 'end_time' => '19:00:00', 'location' => 'مقر']);

        $user = User::factory()->create();

        Group::create([
            'ar_name' => 'مجموعة الأمل',
            'en_name' => 'Hope Group',
            'ar_gsr_name' => 'ممثل المجموعة',
            'en_gsr_name' => 'GSR Name',
            'ar_address' => 'عنوان عربي',
            'en_address' => 'English Address',
            'user_id' => $user->id,
            'service_body_id' => $sb1->id,
            'neighborhood_id' => $neighborhood->id,
            'group_type' => 'in_person',
            'phone' => '01000000000',
            'location' => 'https://maps.app.goo.gl/example'
        ]);

        Group::create([
            'ar_name' => 'مجموعة اونلاين',
            'en_name' => 'Online Group',
            'ar_gsr_name' => 'ممثل المجموعة',
            'en_gsr_name' => 'GSR Name',
            'ar_address' => 'أونلاين',
            'en_address' => 'Online',
            'user_id' => $user->id,
            'service_body_id' => $sb2->id,
            'neighborhood_id' => $neighborhood->id,
            'group_type' => 'online',
            'phone' => '01000000000',
            'location' => 'https://zoom.us/j/123456'
        ]);

        $response = $this->get('/api/service-bodies/map-data');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'service_bodies' => [
                '*' => [
                    'id',
                    'name',
                    'color',
                    'groups_count',
                    'meetings_count',
                    'groups'
                ]
            ],
            'total_service_bodies',
            'total_groups',
            'total_meetings'
        ]);

        $data = $response->json();
        $ids = array_column($data['service_bodies'], 'id');
        $this->assertNotContains(2, $ids, 'Al Ahram GSF (id: 2) must be excluded from map data');
        $this->assertContains(1, $ids);
        $this->assertContains(3, $ids);
    }

    public function test_authenticated_dashboard_service_body_map_is_accessible()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super admin');

        $response = $this->actingAs($admin)->get(route('serviceBody.map'));
        $response->assertStatus(200);
        $response->assertSee('ServiceBodyMap');
    }
}
