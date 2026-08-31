<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\InventorySlip;
use App\Models\InventoryTransaction;
use App\Models\LiteratureRequest;
use App\Models\LiteratureRequestItem;
use App\Models\ServiceBody;
use App\Models\User;
use App\Services\InventoryLedgerService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LitSlipsAndLedgerTest extends TestCase
{
    use RefreshDatabase;

    protected $storeManager;
    protected $litUser;
    protected $rscUser;
    protected $item1;
    protected $item2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        $manageStore = Permission::firstOrCreate(['name' => 'manage store', 'guard_name' => 'web']);
        $viewLit = Permission::firstOrCreate(['name' => 'view lit inventory', 'guard_name' => 'web']);

        $storeRole = Role::firstOrCreate(['name' => 'Store Manager', 'guard_name' => 'web']);
        $storeRole->givePermissionTo($manageStore);

        $litRole = Role::firstOrCreate(['name' => 'Lit User', 'guard_name' => 'web']);
        $litRole->givePermissionTo($viewLit);

        $rscRole = Role::firstOrCreate(['name' => 'rsc', 'guard_name' => 'web']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);

        $this->storeManager = User::factory()->create([
            'name' => 'Store Keeper',
            'email' => 'storekeeper@example.com',
        ]);
        $this->storeManager->assignRole($storeRole);

        $this->litUser = User::factory()->create([
            'name' => 'Lit Keeper',
            'email' => 'litkeeper@example.com',
        ]);
        $this->litUser->assignRole($litRole);

        $this->rscUser = User::factory()->create([
            'name' => 'RSC Member',
            'email' => 'rsc@example.com',
        ]);
        $this->rscUser->assignRole($rscRole);

        $this->item1 = InventoryItem::create([
            'name' => 'كتاب أساسي',
            'name_en' => 'Basic Text',
            'selling_price' => 150.00,
            'store_quantity' => 50,
            'lit_quantity' => 10,
            'category' => 'Arabic Books',
        ]);

        $this->item2 = InventoryItem::create([
            'name' => 'كتيب إرشادي',
            'name_en' => 'Guide IP',
            'selling_price' => 25.00,
            'store_quantity' => 100,
            'lit_quantity' => 20,
            'category' => 'Arabic IP',
        ]);
    }

    public function test_store_transfer_automatically_creates_transfer_slip()
    {
        $response = $this->actingAs($this->storeManager)->post(route('store.transfer', $this->item1->id), [
            'quantity' => 5,
            'notes' => 'Transfer for weekly orders',
        ]);

        $response->assertRedirect(route('store.index'));

        $this->item1->refresh();
        $this->assertEquals(45, $this->item1->store_quantity);
        $this->assertEquals(15, $this->item1->lit_quantity);

        $slip = InventorySlip::where('type', 'transfer_to_lit')->first();
        $this->assertNotNull($slip);
        $this->assertStringStartsWith('TR-', $slip->slip_number);
        $this->assertEquals('transferred', $slip->status);
        $this->assertEquals(5, $slip->total_items_count);
        $this->assertEquals(750.00, (float) $slip->total_value);
        $this->assertEquals($this->storeManager->id, $slip->issued_by);
        $this->assertCount(1, $slip->items);
    }

    public function test_bulk_transfer_creates_transfer_slip_with_multiple_items()
    {
        $response = $this->actingAs($this->storeManager)->post(route('store.bulk_transfer'), [
            'quantities' => [
                $this->item1->id => 10,
                $this->item2->id => 15,
            ],
            'notes' => 'Monthly replenishment',
        ]);

        $response->assertRedirect(route('store.index'));

        $this->item1->refresh();
        $this->item2->refresh();
        $this->assertEquals(40, $this->item1->store_quantity);
        $this->assertEquals(20, $this->item1->lit_quantity);
        $this->assertEquals(85, $this->item2->store_quantity);
        $this->assertEquals(35, $this->item2->lit_quantity);

        $slip = InventorySlip::where('type', 'transfer_to_lit')->latest('id')->first();
        $this->assertNotNull($slip);
        $this->assertEquals(25, $slip->total_items_count);
        $this->assertEquals((10 * 150) + (15 * 25), (float) $slip->total_value);
        $this->assertCount(2, $slip->items);
    }

    public function test_literature_committee_can_acknowledge_receipt_of_transfer_slip()
    {
        $ledgerService = app(InventoryLedgerService::class);
        $slip = $ledgerService->createTransferSlip([
            [
                'inventory_item_id' => $this->item1->id,
                'quantity' => 4,
                'unit_price' => 150.00,
            ]
        ], $this->storeManager->id, 'Dispatched items');

        $this->assertEquals('transferred', $slip->status);

        $response = $this->actingAs($this->litUser)->post(route('slips.acknowledge', $slip->id));
        $response->assertRedirect();

        $slip->refresh();
        $this->assertEquals('received', $slip->status);
        $this->assertEquals($this->litUser->id, $slip->received_by);
        $this->assertNotNull($slip->received_at);
    }

    public function test_slips_archive_and_pdf_export()
    {
        $ledgerService = app(InventoryLedgerService::class);
        $slip = $ledgerService->createTransferSlip([
            [
                'inventory_item_id' => $this->item1->id,
                'quantity' => 2,
                'unit_price' => 150.00,
            ]
        ], $this->storeManager->id);

        $response = $this->actingAs($this->litUser)->get(route('slips.index'));
        $response->assertStatus(200);
        $response->assertSee($slip->slip_number);

        $pdfResponse = $this->actingAs($this->litUser)->get(route('slips.pdf', $slip->id));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_lit_reconciliation_calculates_data_and_returns_surplus_to_store()
    {
        $ledgerService = app(InventoryLedgerService::class);

        // Record a transfer this month
        $ledgerService->createTransferSlip([
            ['inventory_item_id' => $this->item1->id, 'quantity' => 10, 'unit_price' => 150.00]
        ], $this->storeManager->id);

        InventoryTransaction::create([
            'inventory_item_id' => $this->item1->id,
            'user_id' => $this->storeManager->id,
            'type' => 'transfer_to_lit',
            'quantity' => 10,
        ]);

        $reconData = $ledgerService->getReconciliationData(Carbon::now());
        $this->assertNotEmpty($reconData['items']);

        // Process a return from Lit to Store
        $returnResponse = $this->actingAs($this->litUser)->post(route('lit.reconciliation.return'), [
            'returns' => [
                $this->item1->id => 3,
            ],
            'notes' => 'Surplus stock return',
        ]);

        $returnResponse->assertRedirect(route('slips.index'));

        $this->item1->refresh();
        $this->assertEquals(53, $this->item1->store_quantity);
        $this->assertEquals(7, $this->item1->lit_quantity);

        $returnSlip = InventorySlip::where('type', 'return_to_store')->latest('id')->first();
        $this->assertNotNull($returnSlip);
        $this->assertStringStartsWith('RT-', $returnSlip->slip_number);
        $this->assertEquals(3, $returnSlip->total_items_count);
        $this->assertEquals(450.00, (float) $returnSlip->total_value);
    }

    public function test_monthly_ledger_view_and_exports_for_rsc_and_lit()
    {
        $viewResponse = $this->actingAs($this->rscUser)->get(route('lit.ledger'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee(__('messages.monthly_ledger_title'));

        $pdfResponse = $this->actingAs($this->rscUser)->get(route('lit.ledger.pdf'));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('Content-Type', 'application/pdf');

        $csvResponse = $this->actingAs($this->litUser)->get(route('lit.ledger.csv'));
        $csvResponse->assertStatus(200);
        $csvResponse->assertHeader('Content-Disposition', 'attachment; filename=inventory_ledger_' . Carbon::now()->format('Y-m') . '.csv');
    }
}
