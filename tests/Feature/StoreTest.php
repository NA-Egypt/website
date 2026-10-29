<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        // Seed Roles & Permissions
        $manageStore = Permission::firstOrCreate(['name' => 'manage store', 'guard_name' => 'web']);
        $viewLit = Permission::firstOrCreate(['name' => 'view lit inventory', 'guard_name' => 'web']);

        $storeRole = Role::firstOrCreate(['name' => 'Store Manager', 'guard_name' => 'web']);
        $storeRole->givePermissionTo($manageStore);

        $litRole = Role::firstOrCreate(['name' => 'Lit User', 'guard_name' => 'web']);
        $litRole->givePermissionTo($viewLit);
    }

    public function test_dashboard_redirects_store_manager_to_store_index()
    {
        $user = User::factory()->create();
        $user->assignRole('Store Manager');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('store.index'));
    }

    public function test_dashboard_redirects_lit_user_to_lit_index()
    {
        $user = User::factory()->create();
        $user->assignRole('Lit User');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('lit.index'));
    }

    public function test_store_manager_can_create_inventory_item()
    {
        $user = User::factory()->create();
        $user->assignRole('Store Manager');

        $response = $this->actingAs($user)->post(route('store.store'), [
            'name' => 'Living Clean Book',
            'description' => 'A guide to living clean.',
            'selling_price' => 120.00,
            'initial_store_quantity' => 10,
            'category' => 'Arabic Books',
        ]);

        $response->assertRedirect(route('store.index'));
        $this->assertDatabaseHas('inventory_items', [
            'name' => 'Living Clean Book',
            'store_quantity' => 10,
            'lit_quantity' => 0,
            'category' => 'Arabic Books',
        ]);
        $this->assertDatabaseHas('inventory_transactions', [
            'type' => 'receive',
            'quantity' => 10,
        ]);
    }

    public function test_store_manager_can_filter_items_by_category()
    {
        $user = User::factory()->create();
        $user->assignRole('Store Manager');

        $book = InventoryItem::create([
            'name' => 'Arabic Book',
            'selling_price' => 100.00,
            'category' => 'Arabic Books',
        ]);

        $coin = InventoryItem::create([
            'name' => 'Gold Coin',
            'selling_price' => 50.00,
            'category' => 'Coins',
        ]);

        // Filter by Arabic Books
        $response = $this->actingAs($user)->get(route('store.index', ['category' => 'Arabic Books']));
        $response->assertStatus(200);
        $response->assertSee('Arabic Book');
        $response->assertDontSee('Gold Coin');
    }

    public function test_store_manager_can_receive_stock()
    {
        $user = User::factory()->create();
        $user->assignRole('Store Manager');

        $item = InventoryItem::create([
            'name' => 'White Booklet',
            'selling_price' => 10.00,
            'store_quantity' => 5,
        ]);

        $response = $this->actingAs($user)->post(route('store.receive', $item), [
            'quantity' => 15,
            'notes' => 'Received from printer',
        ]);

        $response->assertRedirect(route('store.index'));
        $this->assertEquals(20, $item->fresh()->store_quantity);
        $this->assertDatabaseHas('inventory_transactions', [
            'inventory_item_id' => $item->id,
            'type' => 'receive',
            'quantity' => 15,
            'notes' => 'Received from printer',
        ]);
    }

    public function test_store_manager_can_transfer_to_lit()
    {
        $user = User::factory()->create();
        $user->assignRole('Store Manager');

        $item = InventoryItem::create([
            'name' => 'Keytag',
            'selling_price' => 5.00,
            'store_quantity' => 100,
            'lit_quantity' => 10,
        ]);

        $response = $this->actingAs($user)->post(route('store.transfer', $item), [
            'quantity' => 40,
            'notes' => 'Weekly transfer',
        ]);

        $response->assertRedirect(route('store.index'));
        $item->refresh();
        $this->assertEquals(60, $item->store_quantity);
        $this->assertEquals(50, $item->lit_quantity);
        $this->assertDatabaseHas('inventory_transactions', [
            'inventory_item_id' => $item->id,
            'type' => 'transfer_to_lit',
            'quantity' => 40,
        ]);
    }

    public function test_store_manager_cannot_transfer_more_than_available()
    {
        $user = User::factory()->create();
        $user->assignRole('Store Manager');

        $item = InventoryItem::create([
            'name' => 'IP Booklet',
            'selling_price' => 15.00,
            'store_quantity' => 10,
        ]);

        $response = $this->actingAs($user)->post(route('store.transfer', $item), [
            'quantity' => 15,
        ]);

        $response->assertRedirect(route('store.index'));
        $response->assertSessionHas('error');
        $this->assertEquals(10, $item->fresh()->store_quantity);
    }

    public function test_store_manager_can_return_from_lit()
    {
        $user = User::factory()->create();
        $user->assignRole('Store Manager');

        $item = InventoryItem::create([
            'name' => 'Sponsorship Book',
            'selling_price' => 80.00,
            'store_quantity' => 5,
            'lit_quantity' => 20,
        ]);

        $response = $this->actingAs($user)->post(route('store.return', $item), [
            'quantity' => 12,
            'notes' => 'Unused return',
        ]);

        $response->assertRedirect(route('store.index'));
        $item->refresh();
        $this->assertEquals(17, $item->store_quantity);
        $this->assertEquals(8, $item->lit_quantity);
        $this->assertDatabaseHas('inventory_transactions', [
            'inventory_item_id' => $item->id,
            'type' => 'return_from_lit',
            'quantity' => 12,
        ]);
    }

    public function test_lit_user_cannot_access_store_or_manipulate_stock()
    {
        $user = User::factory()->create();
        $user->assignRole('Lit User');

        $item = InventoryItem::create([
            'name' => 'Recovery Book',
            'selling_price' => 90.00,
            'store_quantity' => 10,
        ]);

        // Attempting to see store list
        $this->actingAs($user)->get(route('store.index'))->assertStatus(403);

        // Attempting to receive stock
        $this->actingAs($user)->post(route('store.receive', $item), ['quantity' => 10])->assertStatus(403);
    }

    public function test_store_manager_can_perform_inline_update_for_stock()
    {
        $user = User::factory()->create();
        $user->assignRole('Store Manager');

        $item = InventoryItem::create([
            'name' => 'Inline Test Item',
            'selling_price' => 50.00,
            'store_quantity' => 20,
            'lit_quantity' => 5,
            'category' => 'Arabic Books',
        ]);

        $response = $this->actingAs($user)->postJson(route('store.inline_update', $item), [
            'selling_price' => 50.00,
            'receive_qty' => 10,
            'transfer_qty' => 5,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $item->refresh();
        $this->assertEquals(25, $item->store_quantity); // +10 receive -5 transfer = +5 net
        $this->assertEquals(10, $item->lit_quantity);   // +5 transfer
    }

    public function test_unauthorized_user_cannot_update_selling_price_via_inline_update()
    {
        $user = User::factory()->create();
        $user->assignRole('Store Manager');

        $item = InventoryItem::create([
            'name' => 'Price Test Item',
            'selling_price' => 50.00,
            'store_quantity' => 20,
            'category' => 'Arabic Books',
        ]);

        $response = $this->actingAs($user)->postJson(route('store.inline_update', $item), [
            'selling_price' => 75.00,
        ]);

        $response->assertStatus(403);
        $this->assertEquals(50.00, $item->fresh()->selling_price);
    }

    public function test_super_admin_can_update_selling_price_via_inline_update()
    {
        Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('super admin');

        $item = InventoryItem::create([
            'name' => 'Admin Price Test Item',
            'selling_price' => 50.00,
            'store_quantity' => 20,
            'category' => 'Arabic Books',
        ]);

        $response = $this->actingAs($admin)->postJson(route('store.inline_update', $item), [
            'selling_price' => 75.00,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals(75.00, $item->fresh()->selling_price);
    }

    public function test_store_manager_can_perform_bulk_inline_update()
    {
        $user = User::factory()->create();
        $user->assignRole('Store Manager');

        $item1 = InventoryItem::create([
            'name' => 'Bulk Item 1',
            'selling_price' => 30.00,
            'store_quantity' => 15,
            'lit_quantity' => 0,
            'category' => 'Arabic Books',
        ]);

        $item2 = InventoryItem::create([
            'name' => 'Bulk Item 2',
            'selling_price' => 40.00,
            'store_quantity' => 25,
            'lit_quantity' => 5,
            'category' => 'Coins',
        ]);

        $response = $this->actingAs($user)->postJson(route('store.bulk_inline_update'), [
            'items' => [
                [
                    'id' => $item1->id,
                    'receive_qty' => 5,
                    'transfer_qty' => 2,
                ],
                [
                    'id' => $item2->id,
                    'receive_qty' => 10,
                    'transfer_qty' => 8,
                ]
            ]
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $item1->refresh();
        $this->assertEquals(18, $item1->store_quantity); // 15 + 5 - 2
        $this->assertEquals(2, $item1->lit_quantity);    // 0 + 2

        $item2->refresh();
        $this->assertEquals(27, $item2->store_quantity); // 25 + 10 - 8
        $this->assertEquals(13, $item2->lit_quantity);   // 5 + 8
    }

    public function test_store_reports_card_metrics_filter_by_item_id()
    {
        $user = User::factory()->create();
        $user->assignRole('Store Manager');

        $item1 = InventoryItem::create([
            'name' => 'Report Item 1',
            'selling_price' => 100.00,
            'store_quantity' => 10,
            'lit_quantity' => 5,
            'category' => 'Arabic Books',
        ]);

        $item2 = InventoryItem::create([
            'name' => 'Report Item 2',
            'selling_price' => 200.00,
            'store_quantity' => 50,
            'lit_quantity' => 20,
            'category' => 'Coins',
        ]);

        $response = $this->actingAs($user)->get(route('store.reports', ['item_id' => $item1->id]));

        $response->assertStatus(200);
        $response->assertViewHas('totalItems', 1);
        $response->assertViewHas('totalStoreStock', 10);
        $response->assertViewHas('totalLitStock', 5);
        $response->assertViewHas('totalValuation', (10 + 5) * 100.00);
    }

    public function test_store_manager_can_mark_item_as_damaged_with_note()
    {
        $user = User::factory()->create();
        $user->assignRole('Store Manager');

        $item = InventoryItem::create([
            'name' => 'Basic Text Arabic',
            'selling_price' => 150.00,
            'store_quantity' => 30,
            'lit_quantity' => 10,
            'damaged_quantity' => 0,
            'category' => 'Arabic Books',
        ]);

        $response = $this->actingAs($user)->postJson(route('store.damage', $item), [
            'quantity' => 3,
            'source_location' => 'store',
            'notes' => 'Water damage on cover during shipping',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $item->refresh();
        $this->assertEquals(27, $item->store_quantity);
        $this->assertEquals(10, $item->lit_quantity);
        $this->assertEquals(3, $item->damaged_quantity);

        $this->assertDatabaseHas('inventory_transactions', [
            'inventory_item_id' => $item->id,
            'type' => 'damaged',
            'source_location' => 'store',
            'quantity' => 3,
            'notes' => 'Water damage on cover during shipping',
            'status' => 'active',
        ]);
    }

    public function test_lit_user_can_mark_lit_item_as_damaged_with_note()
    {
        $user = User::factory()->create();
        $user->assignRole('Lit User');

        $item = InventoryItem::create([
            'name' => 'Step Working Guide',
            'selling_price' => 140.00,
            'store_quantity' => 20,
            'lit_quantity' => 12,
            'damaged_quantity' => 1,
            'category' => 'Arabic Books',
        ]);

        $response = $this->actingAs($user)->postJson(route('lit.damage', $item), [
            'quantity' => 2,
            'notes' => 'Torn pages found at Literature table',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $item->refresh();
        $this->assertEquals(20, $item->store_quantity);
        $this->assertEquals(10, $item->lit_quantity);
        $this->assertEquals(3, $item->damaged_quantity);

        $this->assertDatabaseHas('inventory_transactions', [
            'inventory_item_id' => $item->id,
            'type' => 'damaged',
            'source_location' => 'lit',
            'quantity' => 2,
            'notes' => 'Torn pages found at Literature table',
        ]);
    }

    public function test_store_manager_can_cancel_wrong_receive_and_start_new_receive()
    {
        $user = User::factory()->create();
        $user->assignRole('Store Manager');

        $item = InventoryItem::create([
            'name' => 'It Works How and Why',
            'selling_price' => 130.00,
            'store_quantity' => 10,
            'lit_quantity' => 5,
            'category' => 'Arabic Books',
        ]);

        // Step 1: User receives wrong quantity (e.g. 500 instead of 50)
        $this->actingAs($user)->postJson(route('store.receive', $item), [
            'quantity' => 500,
            'notes' => 'Printer batch',
        ])->assertStatus(200);

        $this->assertEquals(510, $item->fresh()->store_quantity);

        $wrongTx = InventoryTransaction::where('inventory_item_id', $item->id)
            ->where('type', 'receive')
            ->latest('id')
            ->first();
        $this->assertNotNull($wrongTx);
        $this->assertEquals('active', $wrongTx->status);

        // Step 2: User marks receive as wrong entry
        $cancelResponse = $this->actingAs($user)->postJson(route('store.cancel_wrong_receive', $item), [
            'transaction_id' => $wrongTx->id,
            'reason' => 'Entered 500 by mistake instead of 50',
        ]);

        $cancelResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'cancelled_transaction_id' => $wrongTx->id,
                'start_new_receive_item_id' => $item->id,
            ]);

        // Verify store_quantity was reversed back to 10
        $this->assertEquals(10, $item->fresh()->store_quantity);

        // Verify original receive transaction is marked cancelled
        $this->assertDatabaseHas('inventory_transactions', [
            'id' => $wrongTx->id,
            'status' => 'cancelled',
            'cancelled_by' => $user->id,
        ]);

        // Verify audit cancellation record is logged
        $this->assertDatabaseHas('inventory_transactions', [
            'inventory_item_id' => $item->id,
            'type' => 'receive_cancelled',
            'quantity' => 500,
            'reference_transaction_id' => $wrongTx->id,
            'status' => 'active',
        ]);
    }

    public function test_inventory_live_state_and_ui_help_and_shortcuts_render()
    {
        $user = User::factory()->create();
        $user->assignRole('Store Manager');

        $item = InventoryItem::create([
            'name' => 'Just For Today',
            'selling_price' => 110.00,
            'store_quantity' => 15,
            'lit_quantity' => 8,
            'damaged_quantity' => 2,
            'category' => 'Arabic Books',
        ]);

        $liveResponse = $this->actingAs($user)->getJson(route('inventory.live_state'));
        $liveResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('live_state.totals.total_store_stock', 15)
            ->assertJsonPath('live_state.totals.total_lit_stock', 8)
            ->assertJsonPath('live_state.totals.total_damaged_stock', 2);

        $indexResponse = $this->actingAs($user)->get(route('store.index'));
        $indexResponse->assertStatus(200)
            ->assertSee('keyboardShortcutsModal', false)
            ->assertSee('damageModal', false)
            ->assertSee('wrongReceiveModal', false)
            ->assertSee('inv-card-help-btn', false);
    }
}
