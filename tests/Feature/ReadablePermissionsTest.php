<?php

namespace Tests\Feature;

use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReadablePermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        // Seed basic permissions
        $permissions = [
            'create sb agenda',
            'edit sb agenda',
            'approve sb agenda',
            'delete sb agenda',
            'manage store',
            'view lit inventory',
            'view inventory slips',
            'acknowledge inventory slips',
            'view lit ledger',
            'manage literature requests',
            'approve literature requests',
            'edit literature requests',
            'can_manage_calendar',
            'create calendar events',
            'manage calendar events',
            'manage own forms',
            'super admin',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
    }

    public function test_permission_model_provides_localized_attributes_in_english()
    {
        app()->setLocale('en');

        $permission = Permission::where('name', 'create sb agenda')->first();

        $this->assertEquals('Create Service Body Agendas', $permission->display_name);
        $this->assertStringContainsString('Draft and submit', $permission->description);
        $this->assertEquals('agenda', $permission->category);
    }

    public function test_permission_model_provides_localized_attributes_in_arabic()
    {
        app()->setLocale('ar');

        $permission = Permission::where('name', 'create sb agenda')->first();

        $this->assertEquals('إنشاء جداول أعمال هيئة الخدمة', $permission->display_name);
        $this->assertStringContainsString('إنشاء وصياغة', $permission->description);
        $this->assertEquals('agenda', $permission->category);
    }

    public function test_unmapped_permission_falls_back_gracefully()
    {
        $unmapped = Permission::create(
            ['name' => 'custom_unmapped_test_action', 'guard_name' => 'web']
        );

        $this->assertEquals('Custom Unmapped Test Action', $unmapped->display_name);
        $this->assertEquals('', $unmapped->description);
        $this->assertEquals('general', $unmapped->category);
    }

    public function test_get_grouped_categorizes_permissions_correctly()
    {
        app()->setLocale('en');

        $grouped = Permission::getGrouped();

        $this->assertArrayHasKey('agenda', $grouped);
        $this->assertArrayHasKey('store', $grouped);
        $this->assertArrayHasKey('calendar', $grouped);
        $this->assertArrayHasKey('forms', $grouped);
        $this->assertArrayHasKey('system', $grouped);

        $this->assertEquals('Service Body Agendas', $grouped['agenda']['title']);
        $this->assertEquals('Store & Literature', $grouped['store']['title']);

        $agendaPermNames = $grouped['agenda']['permissions']->pluck('name')->toArray();
        $this->assertContains('create sb agenda', $agendaPermNames);

        $systemPermNames = $grouped['system']['permissions']->pluck('name')->toArray();
        $this->assertContains('super admin', $systemPermNames);
    }

    public function test_permission_resource_includes_readable_fields()
    {
        app()->setLocale('en');

        $permission = Permission::where('name', 'manage store')->first();

        $resource = new PermissionResource($permission);
        $data = $resource->toArray(request());

        $this->assertEquals('manage store', $data['name']);
        $this->assertEquals('Manage Literature Store', $data['display_name']);
        $this->assertEquals('store', $data['category']);
        $this->assertNotEmpty($data['description']);
    }

    public function test_user_views_render_with_grouped_readable_permissions()
    {
        $role = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole($role);
        $this->actingAs($admin);

        $response = $this->get(route('users.create'));
        $response->assertStatus(200);
        $response->assertSee('userPermissionSearch');
        $response->assertSee('user-perm-item');

        $responseEdit = $this->get(route('users.edit', $admin));
        $responseEdit->assertStatus(200);
        $responseEdit->assertSee('userPermissionSearch');
        $responseEdit->assertSee('user-perm-item');
    }

    public function test_role_assign_permissions_view_renders_readable_permissions()
    {
        $roleAdmin = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole($roleAdmin);
        $this->actingAs($admin);

        $role = Role::create(['name' => 'Test Role', 'guard_name' => 'web']);

        $response = $this->get(route('roles.assign-permissions', $role));
        $response->assertStatus(200);
        $response->assertSee('permissionSearch');
        $response->assertSee('permission-card');
        $response->assertSee('data-search', false);
    }

    public function test_permissions_controller_json_search_matches_readable_name()
    {
        $roleAdmin = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole($roleAdmin);
        $this->actingAs($admin);

        app()->setLocale('en');

        $response = $this->getJson(route('permissions.index', ['search' => 'Agendas']));
        $response->assertStatus(200);

        $names = collect($response->json('data'))->pluck('name');
        $this->assertTrue($names->contains('create sb agenda'));
    }
}
