<?php

namespace Tests\Feature;

use App\Models\CalendarEvent;
use App\Models\City;
use App\Models\CommitteeReport;
use App\Models\CustomForm;
use App\Models\Day;
use App\Models\DirectOnlineGroup;
use App\Models\Group;
use App\Models\InventoryItem;
use App\Models\Meeting;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use App\Models\ServiceBodyAgenda;
use App\Models\ServiceCommittee;
use App\Models\Topic;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SoftDeletesAndUndoTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Day $day;

    private ServiceBody $sb;

    private Group $group;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));

        $this->day = Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        $city = City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo', 'user_id' => $this->admin->id]);
        $neighborhood = Neighborhood::create(['city_id' => $city->id, 'ar_name' => 'المعادي', 'en_name' => 'Maadi', 'user_id' => $this->admin->id]);
        $this->sb = ServiceBody::create([
            'ar_name' => 'لجنة القاهرة',
            'en_name' => 'Cairo Area',
            'day_id' => $this->day->id,
            'start_time' => '18:00:00',
            'end_time' => '20:00:00',
            'location' => 'Cairo',
        ]);
        $this->group = Group::create([
            'ar_name' => 'مجموعة الأمل',
            'en_name' => 'Hope Group',
            'ar_gsr_name' => 'أحمد',
            'en_gsr_name' => 'Ahmed',
            'phone' => '+201000000000',
            'location' => 'https://maps.google.com/?q=30,31',
            'ar_address' => 'المعادي',
            'en_address' => 'Maadi',
            'service_body_id' => $this->sb->id,
            'neighborhood_id' => $neighborhood->id,
            'user_id' => $this->admin->id,
            'group_type' => 'in_person',
        ]);
    }

    public function test_delete_flashes_undo_url_and_restore_brings_record_back_and_logs_both(): void
    {
        $topic = Topic::create(['ar_name' => 'مفتوح', 'en_name' => 'Open']);
        $meeting = Meeting::create([
            'group_id' => $this->group->id,
            'day_id' => $this->day->id,
            'topic_id' => $topic->id,
            'start_time' => '18:00',
            'end_time' => '19:00',
            'type' => 'in_person',
            'lang' => 'arabic',
            'status' => 'available',
        ]);

        // Delete meeting as super admin
        $deleteRes = $this->actingAs($this->admin)->delete(route('meeting.destroy', $meeting->id));
        $deleteRes->assertRedirect()
            ->assertSessionHas('undo_url', route('meeting.restore', $meeting->id));

        $this->assertSoftDeleted('meetings', ['id' => $meeting->id]);
        $this->assertEquals(0, Meeting::count());

        // Unauthorized user cannot restore
        $plainUser = User::factory()->create();
        $this->actingAs($plainUser)->post(route('meeting.restore', $meeting->id))->assertStatus(403);

        // Authorized user restores
        $this->actingAs($this->admin)->post(route('meeting.restore', $meeting->id))
            ->assertRedirect()
            ->assertSessionHas('success', __('messages.flash.undo.restored_success'));

        $this->assertNotSoftDeleted('meetings', ['id' => $meeting->id]);
        $this->assertEquals(1, Meeting::count());

        // GenericObserver logged both delete and restore
        $this->assertDatabaseHas('transactions', ['model' => 'Meeting', 'operation' => 'delete']);
        $this->assertDatabaseHas('transactions', ['model' => 'Meeting', 'operation' => 'restore']);
    }

    public function test_custom_form_soft_delete_frees_slug_for_reuse(): void
    {
        Permission::firstOrCreate(['name' => 'manage own forms', 'guard_name' => 'web']);

        $form = CustomForm::create([
            'title' => 'Survey 1',
            'status' => 'published',
            'slug' => 'annual-survey',
            'user_id' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)->delete(route('forms.destroy', $form->id))
            ->assertRedirect()
            ->assertSessionHas('undo_url', route('forms.restore', $form->id));

        $this->assertSoftDeleted('custom_forms', ['id' => $form->id]);

        // Creating a new form with the same slug succeeds without collision
        $newForm = CustomForm::create([
            'title' => 'Survey 2',
            'status' => 'published',
            'slug' => 'annual-survey',
            'user_id' => $this->admin->id,
        ]);

        $this->assertEquals('annual-survey', $newForm->slug);
        $this->assertEquals(1, CustomForm::count());
    }

    public function test_remaining_models_soft_delete_and_restore_correctly(): void
    {
        $online = DirectOnlineGroup::create([
            'ar_name' => 'أونلاين',
            'en_name' => 'Online',
            'location' => 'https://zoom.us/j/111',
            'user_id' => $this->admin->id,
        ]);
        $committee = ServiceCommittee::create(['ar_name' => 'لجنة', 'en_name' => 'Committee']);
        $report = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-08-01',
            'status' => 'draft',
            'user_id' => $this->admin->id,
        ]);
        $item = InventoryItem::create([
            'name' => 'Book',
            'name_en' => 'Book',
            'store_quantity' => 5,
            'lit_quantity' => 0,
            'selling_price' => 50,
        ]);
        $cal = CalendarEvent::create([
            'title' => 'Event',
            'start' => '2026-08-01 10:00:00',
            'end' => '2026-08-01 12:00:00',
            'color' => '#3788d8',
            'user_id' => $this->admin->id,
        ]);
        $sbAgenda = ServiceBodyAgenda::create([
            'service_body_id' => $this->sb->id,
            'agenda_date' => '2026-08-01',
            'meeting_date' => '2026-08-15',
            'body' => [['headline' => 'H', 'content' => 'C']],
            'status' => 'draft',
        ]);

        $this->actingAs($this->admin);

        // Group
        $this->delete(route('group.destroy', $this->group->id))->assertSessionHas('undo_url', route('group.restore', $this->group->id));
        $this->assertSoftDeleted('groups', ['id' => $this->group->id]);
        $this->post(route('group.restore', $this->group->id))->assertRedirect();
        $this->assertNotSoftDeleted('groups', ['id' => $this->group->id]);

        // DirectOnlineGroup
        $this->delete(route('direct-online-group.destroy', $online->id))->assertSessionHas('undo_url', route('direct-online-group.restore', $online->id));
        $this->assertSoftDeleted('direct_online_groups', ['id' => $online->id]);
        $this->post(route('direct-online-group.restore', $online->id))->assertRedirect();
        $this->assertNotSoftDeleted('direct_online_groups', ['id' => $online->id]);

        // CommitteeReport
        $this->delete(route('committee-reports.destroy', $report->id))->assertSessionHas('undo_url', route('committee-reports.restore', $report->id));
        $this->assertSoftDeleted('committee_reports', ['id' => $report->id]);
        $this->post(route('committee-reports.restore', $report->id))->assertRedirect();
        $this->assertNotSoftDeleted('committee_reports', ['id' => $report->id]);

        // InventoryItem
        $this->delete(route('store.destroy', $item->id))->assertSessionHas('undo_url', route('store.restore', $item->id));
        $this->assertSoftDeleted('inventory_items', ['id' => $item->id]);
        $this->post(route('store.restore', $item->id))->assertRedirect();
        $this->assertNotSoftDeleted('inventory_items', ['id' => $item->id]);

        // CalendarEvent
        $this->delete(route('web-calendar-events.destroy', $cal->id))->assertSessionHas('undo_url', route('web-calendar-events.restore', $cal->id));
        $this->assertSoftDeleted('calendar_events', ['id' => $cal->id]);
        $this->post(route('web-calendar-events.restore', $cal->id))->assertRedirect();
        $this->assertNotSoftDeleted('calendar_events', ['id' => $cal->id]);

        // ServiceBodyAgenda
        $this->delete(route('service-body-agendas.destroy', $sbAgenda->id))->assertSessionHas('undo_url', route('service-body-agendas.restore', $sbAgenda->id));
        $this->assertSoftDeleted('service_body_agendas', ['id' => $sbAgenda->id]);
        $this->post(route('service-body-agendas.restore', $sbAgenda->id))->assertRedirect();
        $this->assertNotSoftDeleted('service_body_agendas', ['id' => $sbAgenda->id]);

        $this->assertGreaterThanOrEqual(6, Transaction::where('operation', 'restore')->count());
    }
}
