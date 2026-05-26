<?php

namespace Tests\Feature\Livewire;

use App\Livewire\YearlyCalendar;
use App\Models\CalendarEvent;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class YearlyCalendarTest extends TestCase
{
    // use RefreshDatabase; // Commented out to avoid wiping existing DB, will cleanup manually or use transaction if possible, but standard is RefreshDatabase. Given user constraints, let's try to be safe. 
    // Actually, testing usually requires a separate DB or RefreshDatabase. I'll rely on User factory and mocking or meaningful cleanup. 
    // Since I cannot easily set up a separate test DB here, I will use valid data and cleanup.
    
    // Changing strategy: I will assume standard Laravel testing environment which uses RefreshDatabase trait usually. 
    // But to be safe in this environment, I'll avoid RefreshDatabase if I'm not sure about the DB config.
    // However, without RefreshDatabase, tests might affect production data. 
    // I check .env? No. 
    // I will use `Illuminate\Foundation\Testing\DatabaseTransactions` to wrap tests in transactions.
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    public function test_component_renders_correctly()
    {
        Livewire::test(YearlyCalendar::class)
            ->assertStatus(200);
    }

    public function test_guest_cannot_save_event()
    {
        Livewire::test(YearlyCalendar::class)
            ->set('title', 'Test Event')
            ->set('start', now())
            ->set('end', now()->addHour())
            ->set('color', '#3788d8')
            ->call('saveEvent')
            ->assertForbidden(); // Should be 403
    }

    public function test_unauthorized_user_cannot_save_event()
    {
        $user = User::factory()->create();
        
        $this->actingAs($user);

        Livewire::test(YearlyCalendar::class)
            ->set('title', 'Test Event')
            ->set('start', now())
            ->set('end', now()->addHour())
            ->set('color', '#3788d8')
            ->call('saveEvent')
            ->assertForbidden();
    }

    public function test_authorized_user_can_save_event()
    {
        // Setup permission
        // Assuming Permission and Role models exist and work as standard Spatie or similar, but looking at User model code:
        // $this->roles()->whereHas('permissions', ...
        
        // I need to create a user, role, and permission.
        $permission = Permission::firstOrCreate(['name' => 'can_manage_calendar']);
        $role = Role::firstOrCreate(['name' => 'Calendar Manager']);
        
        // Manual pivot attach if relationships standard, checking User.php again...
        // User belongsToMany Role. Role belongsToMany Permission? I assume.
        
        if (!$role->permissions()->where('name', 'can_manage_calendar')->exists()) {
             $role->permissions()->attach($permission);
        }

        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user);

        Livewire::test(YearlyCalendar::class)
            ->set('title', 'New Event')
            ->set('start', '2026-01-01 10:00:00')
            ->set('end', '2026-01-01 12:00:00')
            ->set('description', 'Description')
            ->set('color', '#ff0000')
            ->call('saveEvent')
            ->assertDispatched('event-saved');

        $this->assertDatabaseHas('calendar_events', [
            'title' => 'New Event',
            'user_id' => $user->id,
        ]);
    }

    public function test_validation_rules()
    {
        $permission = Permission::firstOrCreate(['name' => 'can_manage_calendar']);
        $role = Role::firstOrCreate(['name' => 'Calendar Manager']);
        if (!$role->permissions()->where('name', 'can_manage_calendar')->exists()) {
             $role->permissions()->attach($permission);
        }
        $user = User::factory()->create();
        $user->roles()->attach($role);
        $this->actingAs($user);

        Livewire::test(YearlyCalendar::class)
            ->set('title', '') // Required
            ->set('start', '')
            ->call('saveEvent')
            ->assertHasErrors(['title', 'start']);
    }

    public function test_super_admin_can_save_event()
    {
        $role = Role::firstOrCreate(['name' => 'super admin']);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user);

        Livewire::test(YearlyCalendar::class)
            ->set('title', 'Super Admin Event')
            ->set('start', '2026-06-01 10:00:00')
            ->set('end', '2026-06-01 12:00:00')
            ->set('color', '#00ff00')
            ->call('saveEvent')
            ->assertDispatched('event-saved');

        $this->assertDatabaseHas('calendar_events', [
            'title' => 'Super Admin Event',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_edit_another_users_event()
    {
        $role = Role::firstOrCreate(['name' => 'Committees']);
        $user = User::factory()->create();
        $user->roles()->attach($role);
        
        $otherUser = User::factory()->create();

        $event = CalendarEvent::create([
            'title' => 'Other User Event',
            'start' => '2026-06-01 10:00:00',
            'end' => '2026-06-01 12:00:00',
            'color' => '#3788d8',
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user);

        Livewire::test(YearlyCalendar::class)
            ->call('editEvent', $event->id)
            ->assertForbidden();
    }

    public function test_user_can_edit_own_event()
    {
        $role = Role::firstOrCreate(['name' => 'Committees']);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $event = CalendarEvent::create([
            'title' => 'My Event',
            'start' => '2026-06-01 10:00:00',
            'end' => '2026-06-01 12:00:00',
            'color' => '#3788d8',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        Livewire::test(YearlyCalendar::class)
            ->call('editEvent', $event->id)
            ->assertSet('title', 'My Event')
            ->set('title', 'My Updated Event')
            ->call('saveEvent')
            ->assertDispatched('event-saved');

        $this->assertDatabaseHas('calendar_events', [
            'id' => $event->id,
            'title' => 'My Updated Event',
        ]);
        
        // Assert no duplicate event created
        $this->assertEquals(1, CalendarEvent::where('user_id', $user->id)->count());
    }

    public function test_rsc_can_edit_any_event()
    {
        $rscUser = User::where('email', 'rsc@naegypt.org')->first() ?: User::factory()->create(['email' => 'rsc@naegypt.org']);
        $otherUser = User::factory()->create();

        $event = CalendarEvent::create([
            'title' => 'Other User Event',
            'start' => '2026-06-01 10:00:00',
            'end' => '2026-06-01 12:00:00',
            'color' => '#3788d8',
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($rscUser);

        Livewire::test(YearlyCalendar::class)
            ->call('editEvent', $event->id)
            ->assertSet('title', 'Other User Event')
            ->set('title', 'RSC Updated Event')
            ->call('saveEvent')
            ->assertDispatched('event-saved');

        $this->assertDatabaseHas('calendar_events', [
            'id' => $event->id,
            'title' => 'RSC Updated Event',
        ]);
    }

    public function test_service_body_role_can_manage_calendar_and_save_event()
    {
        $role = Role::firstOrCreate(['name' => 'ServiceBody']);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user);

        Livewire::test(YearlyCalendar::class)
            ->set('title', 'ServiceBody Event')
            ->set('start', '2026-06-01 10:00:00')
            ->set('end', '2026-06-01 12:00:00')
            ->set('color', '#3788d8')
            ->call('saveEvent')
            ->assertDispatched('event-saved');

        $this->assertDatabaseHas('calendar_events', [
            'title' => 'ServiceBody Event',
            'user_id' => $user->id,
        ]);
    }

    public function test_reset_modal_resets_fields()
    {
        $role = Role::firstOrCreate(['name' => 'Committees']);
        $user = User::factory()->create();
        $user->roles()->attach($role);
        $this->actingAs($user);

        Livewire::test(YearlyCalendar::class)
            ->set('eventId', 1)
            ->set('title', 'Test Title')
            ->call('resetModal')
            ->assertSet('eventId', null)
            ->assertSet('title', null);
    }

    public function test_select_date_range_sets_fields()
    {
        $role = Role::firstOrCreate(['name' => 'Committees']);
        $user = User::factory()->create();
        $user->roles()->attach($role);
        $this->actingAs($user);

        Livewire::test(YearlyCalendar::class)
            ->set('eventId', 5)
            ->call('selectDateRange', '2026-05-26', '2026-05-27') // Single day selection
            ->assertSet('eventId', null)
            ->assertSet('start', '2026-05-26T09:00')
            ->assertSet('end', '2026-05-26T10:00') // Same-day single-day selection adjustment
            ->assertDispatched('open-modal');

        Livewire::test(YearlyCalendar::class)
            ->call('selectDateRange', '2026-05-26', '2026-05-29') // Multi-day selection
            ->assertSet('eventId', null)
            ->assertSet('start', '2026-05-26T09:00')
            ->assertSet('end', '2026-05-29T10:00')
            ->assertDispatched('open-modal');
    }

    public function test_super_admin_can_edit_any_event()
    {
        $role = Role::firstOrCreate(['name' => 'super admin']);
        $user = User::factory()->create();
        $user->roles()->attach($role);
        
        $otherUser = User::factory()->create();

        $event = CalendarEvent::create([
            'title' => 'Other User Event',
            'start' => '2026-06-01 10:00:00',
            'end' => '2026-06-01 12:00:00',
            'color' => '#3788d8',
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user);

        Livewire::test(YearlyCalendar::class)
            ->call('editEvent', $event->id)
            ->assertSet('title', 'Other User Event')
            ->set('title', 'Super Admin Updated Event')
            ->call('saveEvent')
            ->assertDispatched('event-saved');

        $this->assertDatabaseHas('calendar_events', [
            'id' => $event->id,
            'title' => 'Super Admin Updated Event',
        ]);
    }
}
