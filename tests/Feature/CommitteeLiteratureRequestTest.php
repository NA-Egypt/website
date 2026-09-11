<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\InventorySlip;
use App\Models\InventoryTransaction;
use App\Models\LiteratureRequest;
use App\Models\ServiceCommittee;
use App\Models\User;
use App\Services\InventoryLedgerService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommitteeLiteratureRequestTest extends TestCase
{
    use RefreshDatabase;

    protected User $committeeUser;
    protected User $litUser;
    protected User $superAdmin;
    protected ServiceCommittee $committee;
    protected InventoryItem $itemA;
    protected InventoryItem $itemB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        // Roles
        Role::firstOrCreate(['name' => 'Committees', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Lit User', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);

        // Users
        $this->committeeUser = User::factory()->create([
            'email' => 'hi.committee@naegypt.org',
            'name' => 'H&I Representative',
        ]);
        $this->committeeUser->assignRole('Committees');

        $this->litUser = User::factory()->create([
            'email' => 'lit.comm@naegypt.org',
            'name' => 'Literature Chair',
        ]);
        $this->litUser->assignRole('Lit User');

        $this->superAdmin = User::factory()->create([
            'email' => 'admin@naegypt.org',
            'name' => 'Super Admin',
        ]);
        $this->superAdmin->assignRole('super admin');

        // Service Committee
        $this->committee = ServiceCommittee::create([
            'ar_name' => 'لجنة المستشفيات والمؤسسات',
            'en_name' => 'H&I Committee',
            'chairman_name' => 'Ahmed Ali',
            'chairman_phone' => '01000000000',
            'ar_address' => 'القاهرة، مصر',
            'en_address' => 'Cairo, Egypt',
            'location' => 'Cairo',
            'user_id' => $this->committeeUser->id,
        ]);

        // Inventory Items: Note store_quantity vs lit_quantity
        $this->itemA = InventoryItem::create([
            'name' => 'الكتاب الأساسي',
            'name_en' => 'Basic Text',
            'category' => 'Books',
            'cost_price' => 50.00,
            'selling_price' => 100.00,
            'store_quantity' => 200, // Litstore stock
            'lit_quantity' => 50,    // Literature Committee stock
        ]);

        $this->itemB = InventoryItem::create([
            'name' => 'كتيب أهلاً بك',
            'name_en' => 'Welcome IP',
            'category' => 'IPs',
            'cost_price' => 5.00,
            'selling_price' => 10.00,
            'store_quantity' => 500, // Litstore stock
            'lit_quantity' => 100,   // Literature Committee stock
        ]);
    }

    public function test_committee_can_submit_on_demand_literature_request()
    {
        $response = $this->actingAs($this->committeeUser)
            ->post(route('committee-literature.store'), [
                'committee_id' => $this->committee->id,
                'quantities' => [
                    $this->itemA->id => 15,
                    $this->itemB->id => 30,
                ],
                'notes' => 'Literature for rehabilitation centers visit',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('literature_requests', [
            'service_committee_id' => $this->committee->id,
            'type' => 'committee',
            'status' => 'submitted',
            'total_items_count' => 45,
            'total_price' => 0.00, // No invoice
        ]);

        // Verify stock has not changed yet (until slip is issued)
        $this->itemA->refresh();
        $this->itemB->refresh();
        $this->assertEquals(50, $this->itemA->lit_quantity);
        $this->assertEquals(200, $this->itemA->store_quantity);
    }

    public function test_literature_committee_issues_slip_which_deducts_lit_stock_and_keeps_store_untouched()
    {
        $litRequest = LiteratureRequest::create([
            'service_committee_id' => $this->committee->id,
            'month' => Carbon::now()->startOfMonth(),
            'status' => 'submitted',
            'type' => 'committee',
            'total_items_count' => 20,
            'total_price' => 0.00,
        ]);

        $litRequest->items()->create([
            'inventory_item_id' => $this->itemA->id,
            'quantity' => 20,
            'price' => 0.00,
            'total' => 0.00,
        ]);

        // Literature committee fulfills request and issues delivery slip
        $response = $this->actingAs($this->litUser)
            ->post(route('committee-literature.issue-slip', $litRequest->id), [
                'quantities' => [
                    $this->itemA->id => 20,
                ],
                'notes' => 'Handed over 20 copies',
            ]);

        $response->assertRedirect(route('committee-literature.show', $litRequest->id));

        // Delivery slip created
        $this->assertDatabaseHas('inventory_slips', [
            'type' => 'issue_to_committee',
            'service_committee_id' => $this->committee->id,
            'literature_request_id' => $litRequest->id,
            'status' => 'transferred',
            'total_items_count' => 20,
            'total_value' => 0.00, // No invoice/pricing on slip
        ]);

        // Verify prefix is IC-
        $slip = InventorySlip::where('literature_request_id', $litRequest->id)->first();
        $this->assertStringStartsWith('IC-', $slip->slip_number);

        // Verify Lit Committee stock decreased, Store stock UNTOUCHED
        $this->itemA->refresh();
        $this->assertEquals(30, $this->itemA->lit_quantity);   // 50 - 20 = 30
        $this->assertEquals(200, $this->itemA->store_quantity); // Store remains 200

        // Verify transaction logged
        $this->assertDatabaseHas('inventory_transactions', [
            'inventory_item_id' => $this->itemA->id,
            'type' => 'issue_to_committee',
            'quantity' => 20,
        ]);
    }

    public function test_delivery_slip_pdf_has_no_financial_invoice_data()
    {
        $litRequest = LiteratureRequest::create([
            'service_committee_id' => $this->committee->id,
            'month' => Carbon::now()->startOfMonth(),
            'status' => 'submitted',
            'type' => 'committee',
            'total_items_count' => 10,
            'total_price' => 0.00,
        ]);

        $ledgerService = app(InventoryLedgerService::class);
        $slip = $ledgerService->createCommitteeIssueSlip(
            $litRequest,
            [['inventory_item_id' => $this->itemA->id, 'quantity' => 10]],
            $this->litUser->id
        );

        $response = $this->actingAs($this->committeeUser)
            ->get(route('committee-literature.slip-pdf', $slip->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_committee_acknowledges_receipt_of_dispatched_literature()
    {
        $litRequest = LiteratureRequest::create([
            'service_committee_id' => $this->committee->id,
            'month' => Carbon::now()->startOfMonth(),
            'status' => 'dispatched',
            'type' => 'committee',
            'total_items_count' => 10,
            'total_price' => 0.00,
        ]);

        $slip = InventorySlip::create([
            'slip_number' => InventorySlip::generateSlipNumber('issue_to_committee'),
            'type' => 'issue_to_committee',
            'service_committee_id' => $this->committee->id,
            'literature_request_id' => $litRequest->id,
            'status' => 'transferred',
            'issued_by' => $this->litUser->id,
            'total_items_count' => 10,
            'total_value' => 0.00,
        ]);

        $response = $this->actingAs($this->committeeUser)
            ->post(route('committee-literature.acknowledge', $slip->id));

        $response->assertRedirect();

        $slip->refresh();
        $this->assertEquals('received', $slip->status);
        $this->assertEquals($this->committeeUser->id, $slip->received_by);
        $this->assertNotNull($slip->received_at);

        $litRequest->refresh();
        $this->assertEquals('received', $litRequest->status);
    }

    public function test_returning_remains_restores_lit_stock_and_keeps_store_untouched()
    {
        $litRequest = LiteratureRequest::create([
            'service_committee_id' => $this->committee->id,
            'month' => Carbon::now()->startOfMonth(),
            'status' => 'received',
            'type' => 'committee',
            'total_items_count' => 20,
            'total_price' => 0.00,
        ]);

        // Issue slip of 20
        $ledgerService = app(InventoryLedgerService::class);
        $slip = $ledgerService->createCommitteeIssueSlip(
            $litRequest,
            [['inventory_item_id' => $this->itemA->id, 'quantity' => 20]],
            $this->litUser->id
        );

        $this->itemA->refresh();
        $this->assertEquals(30, $this->itemA->lit_quantity); // 50 - 20 = 30

        // Committee returns 8 remaining copies
        $response = $this->actingAs($this->committeeUser)
            ->post(route('committee-literature.process-return', $litRequest->id), [
                'quantities' => [
                    $this->itemA->id => 8,
                ],
                'notes' => 'Returned 8 unused copies from event',
            ]);

        $response->assertRedirect(route('committee-literature.show', $litRequest->id));

        // Return slip generated
        $this->assertDatabaseHas('inventory_slips', [
            'type' => 'return_from_committee',
            'service_committee_id' => $this->committee->id,
            'literature_request_id' => $litRequest->id,
            'total_items_count' => 8,
            'total_value' => 0.00,
        ]);

        $returnSlip = InventorySlip::where('type', 'return_from_committee')->first();
        $this->assertStringStartsWith('RC-', $returnSlip->slip_number);

        // Lit stock restored by 8: 30 + 8 = 38
        $this->itemA->refresh();
        $this->assertEquals(38, $this->itemA->lit_quantity);
        // Store stock remains completely untouched at 200
        $this->assertEquals(200, $this->itemA->store_quantity);

        // Net consumed by committee is 20 - 8 = 12 copies
    }

    public function test_cannot_return_more_than_remaining_delivered_quantity()
    {
        $litRequest = LiteratureRequest::create([
            'service_committee_id' => $this->committee->id,
            'month' => Carbon::now()->startOfMonth(),
            'status' => 'received',
            'type' => 'committee',
            'total_items_count' => 10,
            'total_price' => 0.00,
        ]);

        $ledgerService = app(InventoryLedgerService::class);
        $ledgerService->createCommitteeIssueSlip(
            $litRequest,
            [['inventory_item_id' => $this->itemA->id, 'quantity' => 10]],
            $this->litUser->id
        );

        // Attempt to return 15 when only 10 were delivered
        $response = $this->actingAs($this->committeeUser)
            ->post(route('committee-literature.process-return', $litRequest->id), [
                'quantities' => [
                    $this->itemA->id => 15,
                ],
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('inventory_slips', [
            'type' => 'return_from_committee',
        ]);
    }

    public function test_lit_ledger_accurately_reports_committee_distributions_in_dedicated_column()
    {
        $month = Carbon::now()->startOfMonth();

        $litRequest = LiteratureRequest::create([
            'service_committee_id' => $this->committee->id,
            'month' => $month,
            'status' => 'received',
            'type' => 'committee',
            'total_items_count' => 20,
            'total_price' => 0.00,
        ]);

        $ledgerService = app(InventoryLedgerService::class);

        // Issue 20, return 5 -> Net distributed = 15
        $ledgerService->createCommitteeIssueSlip(
            $litRequest,
            [['inventory_item_id' => $this->itemA->id, 'quantity' => 20]],
            $this->litUser->id
        );

        $ledgerService->createCommitteeReturnSlip(
            $litRequest,
            [['inventory_item_id' => $this->itemA->id, 'quantity' => 5]],
            $this->litUser->id
        );

        $ledgerData = $ledgerService->getMonthlyLedger($month);

        $itemRow = collect($ledgerData['items'])->firstWhere('id', $this->itemA->id);
        $this->assertNotNull($itemRow);
        $this->assertEquals(15, $itemRow['lit_committee_distributed']);
        $this->assertEquals(15, $ledgerData['lit_summary']['committee_distributed']);

        // Reconciliation data also tracks committee distributed quantity
        $reconciliationData = $ledgerService->getReconciliationData($month);
        $reconcileItem = collect($reconciliationData['items'])->firstWhere('item_id', $this->itemA->id);
        $this->assertEquals(15, $reconcileItem['committee_distributed_qty']);
        $this->assertEquals(15, $reconciliationData['total_committee_distributed']);
    }
}
