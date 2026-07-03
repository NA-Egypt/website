<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Day;
use App\Models\Group;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupScopingTest extends TestCase
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
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'ServiceBody', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'gsr', 'guard_name' => 'web']);
    }

    public function test_super_admin_can_see_all_groups_on_index()
    {
        $day = Day::first() ?? Day::create([
            'ar_name' => 'السبت',
            'en_name' => 'Saturday',
        ]);
        $sb1 = ServiceBody::create([
            'ar_name' => 'الهيئة الأولى',
            'en_name' => 'First Service Body',
            'description' => 'Desc',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Location',
        ]);
        $sb2 = ServiceBody::create([
            'ar_name' => 'الهيئة الثانية',
            'en_name' => 'Second Service Body',
            'description' => 'Desc',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Location',
        ]);

        $city = City::first() ?? City::create([
            'ar_name' => 'القاهرة',
            'en_name' => 'Cairo',
        ]);
        $nh = Neighborhood::first() ?? Neighborhood::create([
            'ar_name' => 'حي الاختبار',
            'en_name' => 'Test Neighborhood',
            'city_id' => $city->id,
        ]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $group1 = Group::create([
            'ar_name' => 'Group One Arabic',
            'en_name' => 'Group One',
            'ar_gsr_name' => 'GSR 1',
            'en_gsr_name' => 'GSR 1',
            'email' => 'g1@example.com',
            'phone' => '12345678',
            'user_id' => $user1->id,
            'ar_address' => 'عنوان 1',
            'en_address' => 'Address 1',
            'location' => 'https://maps.google.com/1',
            'service_body_id' => $sb1->id,
            'neighborhood_id' => $nh->id,
            'group_type' => 'open',
            'slug' => 'group-one',
        ]);

        $group2 = Group::create([
            'ar_name' => 'Group Two Arabic',
            'en_name' => 'Group Two',
            'ar_gsr_name' => 'GSR 2',
            'en_gsr_name' => 'GSR 2',
            'email' => 'g2@example.com',
            'phone' => '12345678',
            'user_id' => $user2->id,
            'ar_address' => 'عنوان 2',
            'en_address' => 'Address 2',
            'location' => 'https://maps.google.com/2',
            'service_body_id' => $sb2->id,
            'neighborhood_id' => $nh->id,
            'group_type' => 'open',
            'slug' => 'group-two',
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('super admin');

        $response = $this->actingAs($admin)->get(route('group.index'));
        $response->assertStatus(200);
        $response->assertSee('Group One');
        $response->assertSee('Group Two');
    }

    public function test_service_body_user_can_only_see_their_service_body_groups_on_index()
    {
        $day = Day::first() ?? Day::create([
            'ar_name' => 'السبت',
            'en_name' => 'Saturday',
        ]);
        $sb1 = ServiceBody::create([
            'ar_name' => 'الهيئة الأولى',
            'en_name' => 'First Service Body',
            'description' => 'Desc',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Location',
        ]);
        $sb2 = ServiceBody::create([
            'ar_name' => 'الهيئة الثانية',
            'en_name' => 'Second Service Body',
            'description' => 'Desc',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Location',
        ]);

        $city = City::first() ?? City::create([
            'ar_name' => 'القاهرة',
            'en_name' => 'Cairo',
        ]);
        $nh = Neighborhood::first() ?? Neighborhood::create([
            'ar_name' => 'حي الاختبار',
            'en_name' => 'Test Neighborhood',
            'city_id' => $city->id,
        ]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $group1 = Group::create([
            'ar_name' => 'Group One Arabic',
            'en_name' => 'Group One',
            'ar_gsr_name' => 'GSR 1',
            'en_gsr_name' => 'GSR 1',
            'email' => 'g1@example.com',
            'phone' => '12345678',
            'user_id' => $user1->id,
            'ar_address' => 'عنوان 1',
            'en_address' => 'Address 1',
            'location' => 'https://maps.google.com/1',
            'service_body_id' => $sb1->id,
            'neighborhood_id' => $nh->id,
            'group_type' => 'open',
            'slug' => 'group-one',
        ]);

        $group2 = Group::create([
            'ar_name' => 'Group Two Arabic',
            'en_name' => 'Group Two',
            'ar_gsr_name' => 'GSR 2',
            'en_gsr_name' => 'GSR 2',
            'email' => 'g2@example.com',
            'phone' => '12345678',
            'user_id' => $user2->id,
            'ar_address' => 'عنوان 2',
            'en_address' => 'Address 2',
            'location' => 'https://maps.google.com/2',
            'service_body_id' => $sb2->id,
            'neighborhood_id' => $nh->id,
            'group_type' => 'open',
            'slug' => 'group-two',
        ]);

        $user = User::factory()->create([
            'service_body_id' => $sb1->id,
        ]);
        $user->assignRole('ServiceBody');

        $response = $this->actingAs($user)->get(route('group.index'));
        $response->assertStatus(200);
        $response->assertSee('Group One');
        $response->assertDontSee('Group Two');
    }
}
