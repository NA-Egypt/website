<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceBody;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Committees', 'guard_name' => 'web']);
    }

    public function test_super_admin_can_view_edit_user_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super admin');

        $user = User::factory()->create([
            'display_name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('users.edit', $user));

        $response->assertStatus(200);
        $response->assertSee('Test User');
        $response->assertSee('test@example.com');
    }

    public function test_super_admin_can_update_user_display_name_email_and_roles()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super admin');

        $user = User::factory()->create([
            'name' => 'olduser',
            'display_name' => 'Old Display Name',
            'email' => 'old@example.com',
        ]);

        $role = Role::firstOrCreate(['name' => 'Committees', 'guard_name' => 'web']);
        $day = \App\Models\Day::first() ?? \App\Models\Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        $serviceBody = ServiceBody::create([
            'en_name' => 'English SB',
            'ar_name' => 'Arabic SB',
            'description' => 'Desc',
            'type' => 'rsc',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Test Location',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('users.update', $user), [
                'display_name' => 'New Display Name',
                'email' => 'newemail@example.com',
                'roles' => [$role->id],
                'service_body_id' => $serviceBody->id,
            ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertEquals('newemail', $user->name);
        $this->assertEquals('New Display Name', $user->display_name);
        $this->assertEquals('newemail@example.com', $user->email);
        $this->assertEquals($serviceBody->id, $user->service_body_id);
        $this->assertTrue($user->hasRole('Committees'));
    }

    public function test_user_with_no_roles_is_redirected_from_dashboard_to_homepage()
    {
        $user = User::factory()->create();
        // Ensure user has no roles

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertRedirect(route('frontend.home'));
    }

    public function test_users_index_returns_last_login_formatted_and_kpi_stats()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super admin');

        $activeUser = User::factory()->create([
            'email' => 'active@example.com',
            'last_login_at' => now()->subDays(2),
        ]);

        $inactiveUser = User::factory()->create([
            'email' => 'inactive@example.com',
            'last_login_at' => null,
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('users.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'last_login_at',
                    'last_login_at_formatted',
                    'last_login_human',
                ]
            ]
        ]);

        $data = $response->json('data');
        $activeItem = collect($data)->firstWhere('id', $activeUser->id);
        $this->assertNotNull($activeItem['last_login_at_formatted']);
        $this->assertNotNull($activeItem['last_login_human']);

        $inactiveItem = collect($data)->firstWhere('id', $inactiveUser->id);
        $this->assertNull($inactiveItem['last_login_at_formatted']);
    }

    public function test_users_index_supports_sorting_by_last_login_at()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super admin');

        $olderLoginUser = User::factory()->create([
            'email' => 'older@example.com',
            'last_login_at' => now()->subDays(10),
        ]);

        $recentLoginUser = User::factory()->create([
            'email' => 'recent@example.com',
            'last_login_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('users.index', ['sort_by' => 'last_login_at', 'sort_dir' => 'desc']));

        $response->assertStatus(200);
        $data = $response->json('data');
        $ids = collect($data)->pluck('id')->all();

        // Recent login user must appear before older login user
        $recentIndex = array_search($recentLoginUser->id, $ids);
        $olderIndex = array_search($olderLoginUser->id, $ids);
        $this->assertNotFalse($recentIndex);
        $this->assertNotFalse($olderIndex);
        $this->assertLessThan($olderIndex, $recentIndex);
    }
}

