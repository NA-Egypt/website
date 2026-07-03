<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceBody;
use App\Models\ServiceBodyAgenda;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceBodyAgendaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('storagebox');
        
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'ServiceBody', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'rsc', 'guard_name' => 'web']);
    }

    protected function createServiceBody($attributes = [])
    {
        $day = \App\Models\Day::first() ?? \App\Models\Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        return ServiceBody::create(array_merge([
            'ar_name' => 'منطقة تجريبية',
            'en_name' => 'Test Service Body',
            'description' => 'Desc',
            'type' => 'rsc',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Cairo',
        ], $attributes));
    }

    public function test_servicebody_user_can_see_own_drafts_and_others_released_approved_agendas_in_archive()
    {
        $sb1 = $this->createServiceBody(['ar_name' => 'المنطقة الأولى', 'en_name' => 'Service Body 1']);
        $sb2 = $this->createServiceBody(['ar_name' => 'المنطقة الثانية', 'en_name' => 'Service Body 2']);

        $user1 = User::factory()->create([
            'email' => 'rcm1@naegypt.org',
            'service_body_id' => $sb1->id,
        ]);
        $user1->assignRole('ServiceBody');

        // 1. Own draft agenda (should be visible to user1)
        $ownDraft = ServiceBodyAgenda::create([
            'service_body_id' => $sb1->id,
            'agenda_date' => now()->toDateString(),
            'meeting_date' => now()->format('Y-m-d'),
            'status' => 'draft',
            'body' => [['headline' => 'Own Draft', 'content' => 'Content']],
        ]);

        // 2. Other service body approved & released agenda (meeting_date is previous month 5th, released on 10th of previous month)
        // Today is e.g. 21st, so it's released and should be visible to user1
        $otherReleasedDate = now()->subMonth()->day(5)->format('Y-m-d');
        $otherReleased = ServiceBodyAgenda::create([
            'service_body_id' => $sb2->id,
            'agenda_date' => now()->subMonth()->toDateString(),
            'meeting_date' => $otherReleasedDate,
            'status' => 'approved',
            'body' => [['headline' => 'Other Released', 'content' => 'Content']],
        ]);

        // 3. Other service body draft agenda (should NOT be visible to user1)
        $otherDraft = ServiceBodyAgenda::create([
            'service_body_id' => $sb2->id,
            'agenda_date' => now()->toDateString(),
            'meeting_date' => now()->format('Y-m-d'),
            'status' => 'draft',
            'body' => [['headline' => 'Other Draft', 'content' => 'Content']],
        ]);

        // 4. Other service body approved but unreleased agenda (meeting on 15th of current month, released on 10th of next month)
        // Since today is current month, it is not released yet and should NOT be visible to user1
        $otherUnreleasedDate = now()->day(15)->format('Y-m-d');
        $otherUnreleased = ServiceBodyAgenda::create([
            'service_body_id' => $sb2->id,
            'agenda_date' => now()->toDateString(),
            'meeting_date' => $otherUnreleasedDate,
            'status' => 'approved',
            'body' => [['headline' => 'Other Unreleased', 'content' => 'Content']],
        ]);

        $this->actingAs($user1);

        $response = $this->get(route('service-body-agendas.archive'));
        $response->assertStatus(200);

        // Own draft must be visible in the archive tree JSON or HTML
        $response->assertSee($sb1->ar_name);
        $response->assertSee($ownDraft->meeting_date->format('Y-m-d'));

        // Other released approved agenda must be visible
        $response->assertSee($sb2->ar_name);
        $response->assertSee($otherReleased->meeting_date->format('Y-m-d'));

        // Other draft must NOT be visible (so its date should not be visible under SB2 name)
        $response->assertDontSee($sb2->ar_name . ' - ' . $otherDraft->meeting_date->format('Y-m-d'));

        // Other unreleased approved agenda must NOT be visible
        $response->assertDontSee($sb2->ar_name . ' - ' . $otherUnreleased->meeting_date->format('Y-m-d'));
    }

    public function test_user_with_create_permission_can_create_agenda()
    {
        $sb = $this->createServiceBody();
        $user = User::factory()->create(['service_body_id' => $sb->id]);
        $user->assignRole('ServiceBody');
        $createPerm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'create sb agenda', 'guard_name' => 'web']);
        $user->givePermissionTo($createPerm);

        $this->actingAs($user);

        $response = $this->get(route('service-body-agendas.create'));
        $response->assertStatus(200);

        $response = $this->post(route('service-body-agendas.store'), [
            'meeting_date' => '2026-07-15',
            'sections' => [
                ['headline' => 'Section 1', 'content' => 'Content 1']
            ],
            'status' => 'draft'
        ]);
        $response->assertRedirect(route('service-body-agendas.index'));
        $this->assertDatabaseHas('service_body_agendas', [
            'service_body_id' => $sb->id,
            'meeting_date' => '2026-07-15 00:00:00'
        ]);
    }

    public function test_user_without_create_permission_cannot_create_agenda()
    {
        $sb = $this->createServiceBody();
        $user = User::factory()->create(['service_body_id' => $sb->id]);
        $user->assignRole('ServiceBody');
        // No permissions given

        $this->actingAs($user);

        $response = $this->get(route('service-body-agendas.create'));
        $response->assertStatus(403);

        $response = $this->post(route('service-body-agendas.store'), [
            'meeting_date' => '2026-07-15',
            'sections' => [
                ['headline' => 'Section 1', 'content' => 'Content 1']
            ],
            'status' => 'draft'
        ]);
        $response->assertStatus(403);
    }

    public function test_user_with_edit_permission_can_edit_draft_agenda()
    {
        $sb = $this->createServiceBody();
        $user = User::factory()->create(['service_body_id' => $sb->id]);
        $user->assignRole('ServiceBody');
        $editPerm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'edit sb agenda', 'guard_name' => 'web']);
        $user->givePermissionTo($editPerm);

        $agenda = ServiceBodyAgenda::create([
            'service_body_id' => $sb->id,
            'agenda_date' => now()->toDateString(),
            'meeting_date' => '2026-07-15',
            'status' => 'draft',
            'body' => [['headline' => 'Section 1', 'content' => 'Content 1']],
        ]);

        $this->actingAs($user);

        $response = $this->get(route('service-body-agendas.edit', $agenda->id));
        $response->assertStatus(200);

        $response = $this->put(route('service-body-agendas.update', $agenda->id), [
            'meeting_date' => '2026-07-20',
            'sections' => [
                ['headline' => 'Updated Section', 'content' => 'Updated Content']
            ],
            'status' => 'draft'
        ]);
        $response->assertRedirect(route('service-body-agendas.index'));
        $this->assertDatabaseHas('service_body_agendas', [
            'id' => $agenda->id,
            'meeting_date' => '2026-07-20 00:00:00'
        ]);
    }

    public function test_user_with_approve_permission_can_approve_submitted_agenda()
    {
        $sb = $this->createServiceBody();
        $user = User::factory()->create(['service_body_id' => $sb->id]);
        $user->assignRole('ServiceBody');
        $approvePerm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'approve sb agenda', 'guard_name' => 'web']);
        $user->givePermissionTo($approvePerm);

        $agenda = ServiceBodyAgenda::create([
            'service_body_id' => $sb->id,
            'agenda_date' => now()->toDateString(),
            'meeting_date' => '2026-07-15',
            'status' => 'submitted',
            'body' => [['headline' => 'Section 1', 'content' => 'Content 1']],
        ]);

        // Mock the archiver service
        $archiverMock = $this->mock(\App\Services\ServiceBodyAgendaArchiver::class);
        $archiverMock->shouldReceive('archive')->once();

        $this->actingAs($user);

        $response = $this->post(route('service-body-agendas.approve', $agenda->id));
        $response->assertRedirect(route('service-body-agendas.index'));
        $this->assertEquals('approved', $agenda->fresh()->status);
    }

    public function test_user_with_delete_permission_can_delete_draft_agenda()
    {
        $sb = $this->createServiceBody();
        $user = User::factory()->create(['service_body_id' => $sb->id]);
        $user->assignRole('ServiceBody');
        $deletePerm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'delete sb agenda', 'guard_name' => 'web']);
        $user->givePermissionTo($deletePerm);

        $agenda = ServiceBodyAgenda::create([
            'service_body_id' => $sb->id,
            'agenda_date' => now()->toDateString(),
            'meeting_date' => '2026-07-15',
            'status' => 'draft',
            'body' => [['headline' => 'Section 1', 'content' => 'Content 1']],
        ]);

        $this->actingAs($user);

        $response = $this->delete(route('service-body-agendas.destroy', $agenda->id));
        $response->assertRedirect(route('service-body-agendas.index'));
        $this->assertDatabaseMissing('service_body_agendas', ['id' => $agenda->id]);
    }
}
