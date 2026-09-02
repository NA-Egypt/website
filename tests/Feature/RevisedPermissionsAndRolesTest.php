<?php

namespace Tests\Feature;

use App\Models\CalendarEvent;
use App\Models\InventoryItem;
use App\Models\InventorySlip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RevisedPermissionsAndRolesTest extends TestCase
{
    use RefreshDatabase;

    protected $litUser;
    protected $storeManager;
    protected $rscUser;
    protected $committeesUser;
    protected $serviceBodyUser;
    protected $gsrUser;
    protected $superAdmin;
    protected $slip;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        // Run the seed migration logic
        $migration = require database_path('migrations/2026_09_02_194000_seed_revised_slips_ledger_and_event_permissions.php');
        $migration->up();

        // Create Users and assign roles
        $this->litUser = User::factory()->create(['name' => 'Lit Comm Member']);
        $this->litUser->assignRole('Lit User');

        $this->storeManager = User::factory()->create(['name' => 'Store Manager Member']);
        $this->storeManager->assignRole('Store Manager');

        $this->rscUser = User::factory()->create(['name' => 'RSC Member']);
        $this->rscUser->assignRole('rsc');

        $this->committeesUser = User::factory()->create(['name' => 'Generic Committee']);
        $this->committeesUser->assignRole('Committees');

        $this->serviceBodyUser = User::factory()->create(['name' => 'Service Body Rep']);
        $this->serviceBodyUser->assignRole('ServiceBody');

        $this->gsrUser = User::factory()->create(['name' => 'GSR Member']);
        Role::firstOrCreate(['name' => 'gsr', 'guard_name' => 'web']);
        $this->gsrUser->assignRole('gsr');

        $this->superAdmin = User::factory()->create(['name' => 'Super Administrator']);
        $this->superAdmin->assignRole('super admin');

        // Create sample inventory item and slip
        $item = InventoryItem::create([
            'name' => 'Sample Book',
            'selling_price' => 100,
            'store_quantity' => 10,
            'lit_quantity' => 5,
        ]);

        $this->slip = InventorySlip::create([
            'slip_number' => 'SLIP-TEST-001',
            'type' => 'transfer_to_lit',
            'status' => 'transferred',
            'issued_by' => $this->storeManager->id,
            'total_items_count' => 1,
            'total_value' => 100,
        ]);
    }

    /**
     * Test 1: Slips access restrictions
     */
    public function test_slips_view_access_allowed_for_lit_store_rsc_and_superadmin_only()
    {
        // Allowed roles
        $this->actingAs($this->litUser)->get(route('slips.index'))->assertStatus(200);
        $this->actingAs($this->storeManager)->get(route('slips.index'))->assertStatus(200);
        $this->actingAs($this->rscUser)->get(route('slips.index'))->assertStatus(200);
        $this->actingAs($this->superAdmin)->get(route('slips.index'))->assertStatus(200);

        // Denied roles (Committees, ServiceBody, GSR)
        $this->actingAs($this->committeesUser)->get(route('slips.index'))->assertStatus(403);
        $this->actingAs($this->serviceBodyUser)->get(route('slips.index'))->assertStatus(403);
        $this->actingAs($this->gsrUser)->get(route('slips.index'))->assertStatus(403);
    }

    /**
     * Test 2: Slip receipt acknowledgment permissions
     */
    public function test_slip_acknowledgment_allowed_for_lit_and_superadmin_only()
    {
        // Store Manager cannot acknowledge
        $this->actingAs($this->storeManager)
            ->post(route('slips.acknowledge', $this->slip->id))
            ->assertStatus(403);

        // RSC cannot acknowledge
        $this->actingAs($this->rscUser)
            ->post(route('slips.acknowledge', $this->slip->id))
            ->assertStatus(403);

        // Lit User CAN acknowledge
        $this->actingAs($this->litUser)
            ->post(route('slips.acknowledge', $this->slip->id))
            ->assertRedirect();

        $this->slip->refresh();
        $this->assertEquals('received', $this->slip->status);
        $this->assertEquals($this->litUser->id, $this->slip->received_by);
    }

    /**
     * Test 3: Ledger access restrictions
     */
    public function test_ledger_access_allowed_for_lit_store_rsc_and_superadmin_only()
    {
        // Allowed roles
        $this->actingAs($this->litUser)->get(route('lit.ledger'))->assertStatus(200);
        $this->actingAs($this->storeManager)->get(route('lit.ledger'))->assertStatus(200);
        $this->actingAs($this->rscUser)->get(route('lit.ledger'))->assertStatus(200);
        $this->actingAs($this->superAdmin)->get(route('lit.ledger'))->assertStatus(200);

        // Denied roles
        $this->actingAs($this->committeesUser)->get(route('lit.ledger'))->assertStatus(403);
        $this->actingAs($this->serviceBodyUser)->get(route('lit.ledger'))->assertStatus(403);
        $this->actingAs($this->gsrUser)->get(route('lit.ledger'))->assertStatus(403);
    }

    /**
     * Test 4: Calendar Event creation permissions
     */
    public function test_event_creation_allowed_for_committees_servicebodies_rsc_and_superadmin_only()
    {
        $payload = [
            'title' => 'Regional Convention',
            'start' => Carbon::now()->addDays(10)->toIso8601String(),
            'end' => Carbon::now()->addDays(12)->toIso8601String(),
            'description' => 'A convention for recovery',
            'color' => '#00698f',
        ];

        // GSR is not allowed
        $this->actingAs($this->gsrUser)
            ->postJson(route('web-calendar-events.store'), $payload)
            ->assertStatus(403);

        // Store Manager (alone) is not allowed
        $this->actingAs($this->storeManager)
            ->postJson(route('web-calendar-events.store'), $payload)
            ->assertStatus(403);

        // Committees CAN create
        $this->actingAs($this->committeesUser)
            ->postJson(route('web-calendar-events.store'), array_merge($payload, ['title' => 'Committee Event']))
            ->assertStatus(201);

        // ServiceBody CAN create
        $this->actingAs($this->serviceBodyUser)
            ->postJson(route('web-calendar-events.store'), array_merge($payload, ['title' => 'Service Body Event']))
            ->assertStatus(201);

        // RSC CAN create
        $this->actingAs($this->rscUser)
            ->postJson(route('web-calendar-events.store'), array_merge($payload, ['title' => 'RSC Event']))
            ->assertStatus(201);

        // Lit User (which has create calendar events permission) CAN create
        $this->actingAs($this->litUser)
            ->postJson(route('web-calendar-events.store'), array_merge($payload, ['title' => 'Lit Event']))
            ->assertStatus(201);

        // Super Admin CAN create
        $this->actingAs($this->superAdmin)
            ->postJson(route('web-calendar-events.store'), array_merge($payload, ['title' => 'Super Admin Event']))
            ->assertStatus(201);
    }

    /**
     * Test 5: Calendar Event editing and deleting permissions
     */
    public function test_event_edit_and_delete_restricted_to_creator_rsc_and_superadmin()
    {
        $event = CalendarEvent::create([
            'title' => 'Author Event',
            'start' => Carbon::now()->addDays(5),
            'end' => Carbon::now()->addDays(6),
            'user_id' => $this->committeesUser->id,
            'color' => '#28a745',
        ]);

        $updatePayload = ['title' => 'Updated Event Title'];

        // Another committee user cannot edit
        $otherCommittee = User::factory()->create();
        $otherCommittee->assignRole('Committees');

        $this->actingAs($otherCommittee)
            ->putJson(route('web-calendar-events.update', $event->id), $updatePayload)
            ->assertStatus(403);

        // The creator CAN edit
        $this->actingAs($this->committeesUser)
            ->putJson(route('web-calendar-events.update', $event->id), $updatePayload)
            ->assertStatus(200);

        // RSC CAN edit any event
        $this->actingAs($this->rscUser)
            ->putJson(route('web-calendar-events.update', $event->id), ['title' => 'RSC Edited Title'])
            ->assertStatus(200);

        // Super Admin CAN delete any event
        $this->actingAs($this->superAdmin)
            ->deleteJson(route('web-calendar-events.destroy', $event->id))
            ->assertStatus(200);

        $this->assertDatabaseMissing('calendar_events', ['id' => $event->id]);
    }

    /**
     * Test 6: GSR cannot manage calendar in YearlyCalendar Livewire view and gets 403
     */
    public function test_gsr_cannot_manage_calendar_in_yearly_calendar_view()
    {
        // GSR view /calendar must NOT have data-can-manage or data-can-create
        $response = $this->actingAs($this->gsrUser)->get(route('calendar.index'));
        $response->assertStatus(200);
        $response->assertDontSee('data-can-manage');
        $response->assertDontSee('data-can-create');

        // RSC view /calendar MUST have data-can-manage
        $rscResponse = $this->actingAs($this->rscUser)->get(route('calendar.index'));
        $rscResponse->assertStatus(200);
        $rscResponse->assertSee('data-can-manage');

        // Committees view /calendar MUST have data-can-create
        $commResponse = $this->actingAs($this->committeesUser)->get(route('calendar.index'));
        $commResponse->assertStatus(200);
        $commResponse->assertSee('data-can-create');

        // Livewire saveEvent aborts 403 for GSR
        \Livewire\Livewire::actingAs($this->gsrUser)
            ->test(\App\Livewire\YearlyCalendar::class)
            ->set('title', 'GSR Event Attempt')
            ->set('start', '2026-09-10 10:00:00')
            ->set('end', '2026-09-10 12:00:00')
            ->set('color', '#00698f')
            ->call('saveEvent')
            ->assertStatus(403);
    }
}
