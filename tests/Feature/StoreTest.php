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
}
