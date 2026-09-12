<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Day;
use App\Models\DirectOnlineGroup;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TableRevampEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        $role = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole($role);
    }

    private function createDummyGroup(ServiceBody $sb = null): Group
    {
        $day = Day::first() ?? Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        if (!$sb) {
            $sb = ServiceBody::create([
                'ar_name'     => 'الهيئة الأولى',
                'en_name'     => 'First Service Body',
                'description' => 'Desc',
                'day_id'      => $day->id,
                'date'        => '2026-06-01',
                'start_time'  => '10:00:00',
                'end_time'    => '12:00:00',
                'location'    => 'Location',
            ]);
        }

        $city = City::first() ?? City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $nh = Neighborhood::first() ?? Neighborhood::create(['ar_name' => 'حي', 'en_name' => 'NH', 'city_id' => $city->id]);

        return Group::create([
            'ar_name'         => 'مجموعة',
            'en_name'         => 'Group',
            'ar_gsr_name'     => 'ممثل',
            'en_gsr_name'     => 'GSR',
            'email'           => 'group@example.com',
            'phone'           => '0100000000',
            'user_id'         => $this->admin->id,
            'ar_address'      => 'عنوان',
            'en_address'      => 'Address',
            'location'        => 'https://maps.google.com',
            'service_body_id' => $sb->id,
            'neighborhood_id' => $nh->id,
            'group_type'      => 'open',
            'slug'            => 'group-' . uniqid(),
        ]);
    }

    public function test_meetings_index_renders_html_with_kpis()
    {
        $response = $this->actingAs($this->admin)->get(route('meeting.index'));
        $response->assertStatus(200);
        $response->assertViewHas('kpiStats');
        $response->assertViewHas('days');
        $response->assertSee('MeetingsDataTable');
    }

    public function test_meetings_index_json_filtering()
    {
        $day = Day::first() ?? Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        $topic = Topic::create(['ar_name' => 'موضوع', 'en_name' => 'Topic']);
        $group = $this->createDummyGroup();

        Meeting::create([
            'group_id'   => $group->id,
            'topic_id'   => $topic->id,
            'day_id'     => $day->id,
            'start_time' => '18:00',
            'end_time'   => '19:30',
            'type'       => 'open',
            'lang'       => 'ar',
            'status'     => 'available'
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('meeting.index', [
            'day_id' => $day->id,
            'type'   => 'in_person',
            'status' => 'available'
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id',
                    'group_name',
                    'topic_name',
                    'day_name',
                    'from_time',
                    'to_time',
                    'status_label',
                    'group_type'
                ]
            ],
            'total'
        ]);
    }

    public function test_groups_index_renders_html_with_kpis()
    {
        $response = $this->actingAs($this->admin)->get(route('group.index'));
        $response->assertStatus(200);
        $response->assertViewHas('kpiStats');
        $response->assertViewHas('serviceBodies');
        $response->assertSee('GroupsDataTable');
    }

    public function test_groups_index_json_filtering()
    {
        $group = $this->createDummyGroup();

        $response = $this->actingAs($this->admin)->getJson(route('group.index', [
            'service_body_id' => $group->service_body_id
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id',
                    'primary_name',
                    'service_body_name',
                    'meetings_count'
                ]
            ],
            'total'
        ]);
    }

    public function test_direct_online_groups_index_renders_html_with_kpis()
    {
        $response = $this->actingAs($this->admin)->get(route('direct-online-group.index'));
        $response->assertStatus(200);
        $response->assertViewHas('kpiStats');
        $response->assertSee('DirectOnlineGroupsDataTable');
    }

    public function test_direct_online_groups_index_json_filtering()
    {
        DirectOnlineGroup::create([
            'ar_name'  => 'مجموعة أونلاين',
            'en_name'  => 'Online Group',
            'location' => 'https://zoom.us/j/123456789',
            'user_id'  => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('direct-online-group.index', [
            'has_link' => 'yes'
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id',
                    'primary_name',
                    'location_link'
                ]
            ],
            'total'
        ]);
    }
}
