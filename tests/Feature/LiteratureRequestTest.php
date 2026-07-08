<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use App\Models\ServiceBody;
use App\Models\InventoryItem;
use App\Models\LiteratureRequest;
use App\Models\LiteratureRequestItem;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;

class LiteratureRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $gsrUser;
    protected $treasurerUser;
    protected $litUser;
    protected $group;
    protected $serviceBody;
    protected $item1;
    protected $item2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        // Create Spatie Roles
        $gsrRole = Role::firstOrCreate(['name' => 'gsr', 'guard_name' => 'web']);
        $treasurerRole = Role::firstOrCreate(['name' => 'Treasurer', 'guard_name' => 'web']);
        $litRole = Role::firstOrCreate(['name' => 'Lit User', 'guard_name' => 'web']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);

        // Create service body and group
        $day = new \App\Models\Day();
        $day->name = 'Saturday';
        $day->save();
        $this->serviceBody = ServiceBody::create([
            'ar_name' => 'الهيئة الخدمية',
            'en_name' => 'Service Body One',
            'day_id' => $day->id,
            'description' => 'Desc',
            'type' => 'rsc',
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Cairo',
        ]);

        // Create city & neighborhood
        $city = \App\Models\City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $neighborhood = \App\Models\Neighborhood::create([
            'ar_name' => 'حي الاختبار',
            'en_name' => 'Test Neighborhood',
            'city_id' => $city->id,
        ]);

        // Create Users first so we can link them
        $this->gsrUser = User::factory()->create([
            'name' => 'GSR User',
            'email' => 'gsr@test.com',
            'service_body_id' => $this->serviceBody->id,
        ]);
        $this->gsrUser->assignRole($gsrRole);

        $this->group = Group::create([
            'ar_name' => 'مجموعة الأمل',
            'en_name' => 'Hope Group',
            'ar_gsr_name' => 'GSR',
            'en_gsr_name' => 'GSR',
            'email' => 'group@naegypt.org',
            'phone' => '12345678',
            'user_id' => $this->gsrUser->id,
            'ar_address' => 'العنوان',
            'en_address' => 'Address',
            'location' => 'https://maps.google.com',
            'group_type' => 'open',
            'service_body_id' => $this->serviceBody->id,
            'neighborhood_id' => $neighborhood->id,
        ]);

        $this->treasurerUser = User::factory()->create([
            'name' => 'Treasurer User',
            'email' => 'treasurer@test.com',
            'service_body_id' => $this->serviceBody->id,
        ]);
        $this->treasurerUser->assignRole($treasurerRole);

        $this->litUser = User::factory()->create([
            'name' => 'Lit User',
            'email' => 'lit@test.com',
        ]);
        $this->litUser->assignRole($litRole);

        // Create inventory items
        $this->item1 = InventoryItem::create([
            'name' => 'Item 1',
            'selling_price' => 10.00,
            'store_quantity' => 100,
            'category' => 'Arabic Books',
        ]);

        $this->item2 = InventoryItem::create([
            'name' => 'Item 2',
            'selling_price' => 20.00,
            'store_quantity' => 50,
            'category' => 'Arabic IP',
        ]);
    }

    public function test_gsr_can_add_items_to_cart_before_19th()
    {
        Carbon::setTestNow('2026-07-10'); // Before 19th

        $response = $this->actingAs($this->gsrUser)->post(route('literature-requests.cart.update'), [
            'quantities' => [
                $this->item1->id => 5,
                $this->item2->id => 2,
            ]
        ]);

        $response->assertRedirect(route('literature-requests.cart'));

        $this->assertDatabaseHas('literature_requests', [
            'group_id' => $this->group->id,
            'service_body_id' => $this->serviceBody->id,
            'status' => 'draft',
            'total_items_count' => 7,
            'total_price' => 90.00,
        ]);
    }

    public function test_gsr_cannot_add_items_to_cart_after_19th()
    {
        Carbon::setTestNow('2026-07-20'); // After 19th

        $response = $this->actingAs($this->gsrUser)->post(route('literature-requests.cart.update'), [
            'quantities' => [
                $this->item1->id => 5,
            ]
        ]);

        $response->assertStatus(403);
    }

    public function test_gsr_can_submit_request_and_override_before_19th()
    {
        Carbon::setTestNow('2026-07-10'); // Before 19th

        // 1. Add to cart
        $this->actingAs($this->gsrUser)->post(route('literature-requests.cart.update'), [
            'quantities' => [
                $this->item1->id => 5,
            ]
        ]);

        // 2. Submit
        $response = $this->actingAs($this->gsrUser)->post(route('literature-requests.submit'));
        $response->assertRedirect(route('literature-requests.cart'));

        $this->assertDatabaseHas('literature_requests', [
            'group_id' => $this->group->id,
            'status' => 'submitted',
            'total_items_count' => 5,
        ]);

        // 3. Override (add new quantities and submit again)
        $this->actingAs($this->gsrUser)->post(route('literature-requests.cart.update'), [
            'quantities' => [
                $this->item1->id => 8,
            ]
        ]);

        $this->assertDatabaseHas('literature_requests', [
            'group_id' => $this->group->id,
            'status' => 'draft',
            'total_items_count' => 8,
        ]);

        $this->actingAs($this->gsrUser)->post(route('literature-requests.submit'));

        $this->assertDatabaseHas('literature_requests', [
            'group_id' => $this->group->id,
            'status' => 'submitted',
            'total_items_count' => 8,
        ]);
    }

    public function test_accumulation_happens_on_or_after_19th()
    {
        Carbon::setTestNow('2026-07-10'); // Before 19th

        // Submit group request
        $this->actingAs($this->gsrUser)->post(route('literature-requests.cart.update'), [
            'quantities' => [
                $this->item1->id => 10,
            ]
        ]);
        $this->actingAs($this->gsrUser)->post(route('literature-requests.submit'));

        // Visit treasurer dashboard on 20th
        Carbon::setTestNow('2026-07-20');

        $response = $this->actingAs($this->treasurerUser)->get(route('literature-requests.treasurer'));
        $response->assertStatus(200);

        // Accumulated request should be created
        $this->assertDatabaseHas('literature_requests', [
            'service_body_id' => $this->serviceBody->id,
            'type' => 'servicebody',
            'status' => 'draft',
            'total_items_count' => 10,
            'total_price' => 100.00,
        ]);
    }

    public function test_treasurer_can_approve_and_send_to_literature_committee()
    {
        Carbon::setTestNow('2026-07-20');

        // Create accumulated request
        $accumulated = LiteratureRequest::create([
            'service_body_id' => $this->serviceBody->id,
            'month' => Carbon::now()->startOfMonth(),
            'type' => 'servicebody',
            'status' => 'draft',
            'total_items_count' => 10,
            'total_price' => 100.00,
        ]);

        $response = $this->actingAs($this->treasurerUser)->post(route('literature-requests.approve-send', $accumulated->id));
        $response->assertRedirect();

        $this->assertDatabaseHas('literature_requests', [
            'id' => $accumulated->id,
            'status' => 'sent_to_committee',
        ]);
    }

    public function test_lit_committee_can_edit_and_return_to_service_body()
    {
        Carbon::setTestNow('2026-07-20');

        // Create accumulated request sent to committee
        $accumulated = LiteratureRequest::create([
            'service_body_id' => $this->serviceBody->id,
            'month' => Carbon::now()->startOfMonth(),
            'type' => 'servicebody',
            'status' => 'sent_to_committee',
            'total_items_count' => 10,
            'total_price' => 100.00,
        ]);

        LiteratureRequestItem::create([
            'literature_request_id' => $accumulated->id,
            'inventory_item_id' => $this->item1->id,
            'quantity' => 10,
            'price' => $this->item1->selling_price,
            'total' => 100.00,
        ]);

        // Edit request
        $response = $this->actingAs($this->litUser)->post(route('literature-requests.committee.update', $accumulated->id), [
            'quantities' => [
                $this->item1->id => 6, // Reduced quantity
            ]
        ]);

        $response->assertRedirect(route('literature-requests.committee'));

        $this->assertDatabaseHas('literature_requests', [
            'id' => $accumulated->id,
            'status' => 'returned_by_committee',
            'total_items_count' => 6,
            'total_price' => 60.00,
        ]);
    }
}
