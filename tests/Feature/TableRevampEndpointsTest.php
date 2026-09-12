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

    public function test_cities_index_renders_and_returns_json()
    {
        $city = City::create(['ar_name' => 'الإسكندرية', 'en_name' => 'Alexandria']);

        $viewResponse = $this->actingAs($this->admin)->get(route('city.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('kpiStats');
        $viewResponse->assertSee('CitiesDataTable');

        $jsonResponse = $this->actingAs($this->admin)->getJson(route('city.index', [
            'search' => 'Alexandria'
        ]));
        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJsonFragment(['en_name' => 'Alexandria']);
    }

    public function test_neighborhoods_index_renders_and_returns_json()
    {
        $city = City::first() ?? City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $nh = Neighborhood::create(['ar_name' => 'المعادي', 'en_name' => 'Maadi', 'city_id' => $city->id]);

        $viewResponse = $this->actingAs($this->admin)->get(route('neighborhood.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('kpiStats');
        $viewResponse->assertViewHas('cities');
        $viewResponse->assertSee('NeighborhoodsDataTable');

        $jsonResponse = $this->actingAs($this->admin)->getJson(route('neighborhood.index', [
            'city_id' => $city->id
        ]));
        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJsonFragment(['en_name' => 'Maadi']);
    }

    public function test_topics_index_renders_and_returns_json()
    {
        $topic = Topic::create(['ar_name' => 'الخطوة الأولى', 'en_name' => 'Step 1']);

        $viewResponse = $this->actingAs($this->admin)->get(route('topic.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('kpiStats');
        $viewResponse->assertSee('TopicsDataTable');

        $jsonResponse = $this->actingAs($this->admin)->getJson(route('topic.index', [
            'search' => 'Step 1'
        ]));
        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJsonFragment(['en_name' => 'Step 1']);
    }

    public function test_workgroups_index_renders_and_returns_json()
    {
        $parent = \App\Models\ServiceCommittee::create([
            'ar_name' => 'لجنة الخدمة الإقليمية',
            'en_name' => 'Regional Committee',
            'email'   => 'rsc@example.com',
            'user_id' => $this->admin->id,
        ]);

        $wg = \App\Models\ServiceCommittee::create([
            'parent_id'      => $parent->id,
            'ar_name'        => 'مجموعة عمل تكنولوجيا المعلومات',
            'en_name'        => 'IT Workgroup',
            'workgroup_type' => 'permanent',
            'status'         => 'active',
            'email'          => 'it@example.com',
            'user_id'        => $this->admin->id,
        ]);

        $viewResponse = $this->actingAs($this->admin)->get(route('workgroup.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('kpiStats');
        $viewResponse->assertSee('WorkgroupsDataTable');

        $jsonResponse = $this->actingAs($this->admin)->getJson(route('workgroup.index', [
            'parent_id' => $parent->id
        ]));
        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJsonFragment(['en_name' => 'IT Workgroup']);
    }

    public function test_service_committees_index_renders_and_returns_json()
    {
        $committee = \App\Models\ServiceCommittee::create([
            'ar_name' => 'لجنة الأدب',
            'en_name' => 'Literature Committee',
            'email'   => 'lit@example.com',
            'user_id' => $this->admin->id,
        ]);

        $viewResponse = $this->actingAs($this->admin)->get(route('serviceCommittee.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('kpiStats');
        $viewResponse->assertSee('ServiceCommitteesDataTable');

        $jsonResponse = $this->actingAs($this->admin)->getJson(route('serviceCommittee.index', [
            'search' => 'Literature'
        ]));
        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJsonFragment(['en_name' => 'Literature Committee']);
    }

    public function test_service_bodies_index_renders_and_returns_json()
    {
        $day = Day::first() ?? Day::create(['ar_name' => 'الأحد', 'en_name' => 'Sunday']);
        $sb = ServiceBody::create([
            'ar_name'     => 'هيئة القاهرة',
            'en_name'     => 'Cairo ASC',
            'description' => 'Desc',
            'day_id'      => $day->id,
            'start_time'  => '10:00',
            'end_time'    => '12:00',
            'location'    => 'Cairo',
        ]);

        $viewResponse = $this->actingAs($this->admin)->get(route('serviceBody.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('kpiStats');
        $viewResponse->assertViewHas('days');
        $viewResponse->assertSee('ServiceBodiesDataTable');

        $jsonResponse = $this->actingAs($this->admin)->getJson(route('serviceBody.index', [
            'day_id' => $day->id
        ]));
        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJsonFragment(['en_name' => 'Cairo ASC']);
    }

    public function test_users_index_renders_and_returns_json()
    {
        $viewResponse = $this->actingAs($this->admin)->get(route('users.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('kpiStats');
        $viewResponse->assertViewHas('roles');
        $viewResponse->assertSee('UsersDataTable');

        $jsonResponse = $this->actingAs($this->admin)->getJson(route('users.index', [
            'search' => $this->admin->email
        ]));
        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJsonFragment(['email' => $this->admin->email]);
    }

    public function test_subscribers_index_renders_and_returns_json()
    {
        $sub = \Mydnic\Subscribers\Subscriber::create([
            'email' => 'subscriber@example.com'
        ]);

        $viewResponse = $this->actingAs($this->admin)->get(route('subscribers.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('kpiStats');
        $viewResponse->assertSee('SubscribersDataTable');

        $jsonResponse = $this->actingAs($this->admin)->getJson(route('subscribers.index', [
            'search' => 'subscriber@example.com'
        ]));
        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJsonFragment(['email' => 'subscriber@example.com']);
    }

    public function test_permissions_index_renders_and_returns_json()
    {
        $perm = \App\Models\Permission::firstOrCreate(['name' => 'view_reports', 'guard_name' => 'web']);

        $viewResponse = $this->actingAs($this->admin)->get(route('permissions.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('kpiStats');
        $viewResponse->assertViewHas('categories');
        $viewResponse->assertSee('PermissionsDataTable');

        $jsonResponse = $this->actingAs($this->admin)->getJson(route('permissions.index', [
            'search' => 'view_reports'
        ]));
        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJsonFragment(['name' => 'view_reports']);
    }

    public function test_all_revamped_table_views_have_no_untranslated_messages_keys()
    {
        $routes = [
            'meeting.index',
            'group.index',
            'direct-online-group.index',
            'city.index',
            'neighborhood.index',
            'topic.index',
            'workgroup.index',
            'serviceCommittee.index',
            'serviceBody.index',
            'users.index',
            'subscribers.index',
            'permissions.index',
        ];

        foreach (['en', 'ar'] as $locale) {
            app()->setLocale($locale);

            foreach ($routes as $route) {
                $response = $this->actingAs($this->admin)->get(route($route));
                $response->assertStatus(200);
                $content = $response->getContent();

                $forbiddenKeys = [
                    'messages.actions',
                    'messages.edit',
                    'messages.delete',
                    'messages.showing',
                    'messages.to',
                    'messages.of',
                    'messages.entries',
                    'messages.first',
                    'messages.prev',
                    'messages.next',
                    'messages.last',
                ];

                foreach ($forbiddenKeys as $forbiddenKey) {
                    $this->assertStringNotContainsString(
                        $forbiddenKey,
                        $content,
                        "Route [{$route}] in [{$locale}] locale unexpectedly contains untranslated string [{$forbiddenKey}]"
                    );
                }
            }
        }
    }
}

