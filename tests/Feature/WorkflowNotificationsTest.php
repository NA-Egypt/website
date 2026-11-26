<?php

namespace Tests\Feature;

use App\Models\ChangeRequest;
use App\Models\CommitteeReport;
use App\Models\Day;
use App\Models\InventoryItem;
use App\Models\LiteratureRequest;
use App\Models\ServiceBody;
use App\Models\ServiceCommittee;
use App\Models\User;
use App\Notifications\ChangeRequestStatusNotification;
use App\Notifications\CommitteeReportStatusNotification;
use App\Notifications\InventorySlipPendingNotification;
use App\Notifications\LiteratureRequestStageNotification;
use App\Services\InventoryLedgerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkflowNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_are_queued_and_use_database_and_mail_channels(): void
    {
        $user = User::factory()->create();

        $cr = ChangeRequest::create([
            'user_id' => $user->id,
            'request_type' => 'general',
            'subject' => 'Update phone',
            'description' => 'Details',
            'status' => 'pending',
        ]);

        $notification = new ChangeRequestStatusNotification($cr, 'completed');
        $this->assertInstanceOf(ShouldQueue::class, $notification);
        $this->assertEquals(['database', 'mail'], $notification->via($user));
    }

    public function test_committee_report_workflow_dispatches_notifications(): void
    {
        Notification::fake();

        $rscRole = Role::firstOrCreate(['name' => 'rsc', 'guard_name' => 'web']);
        $rscUser = User::factory()->create();
        $rscUser->assignRole($rscRole);

        $committeeUser = User::factory()->create();
        $committee = ServiceCommittee::create([
            'ar_name' => 'لجنة الإعلام',
            'en_name' => 'PI Committee',
            'user_id' => $committeeUser->id,
        ]);

        $report = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-08-01',
            'status' => 'draft',
            'user_id' => $committeeUser->id,
        ]);

        // Submit (send)
        $this->actingAs($committeeUser)->post(route('committee-reports.send', $report->id))->assertRedirect();
        Notification::assertSentTo($rscUser, CommitteeReportStatusNotification::class, fn ($n) => $n->event === 'submitted');

        // Return to draft
        $this->actingAs($rscUser)->post(route('committee-reports.returnToDraft', $report->id), [
            'review_notes' => 'Please clarify section 2',
        ])->assertRedirect();
        Notification::assertSentTo($committeeUser, CommitteeReportStatusNotification::class, fn ($n) => $n->event === 'returned');

        // Re-submit then approve
        $report->update(['status' => 'submitted']);
        $this->actingAs($rscUser)->post(route('committee-reports.approveAndSend', $report->id))->assertRedirect();
        Notification::assertSentTo($committeeUser, CommitteeReportStatusNotification::class, fn ($n) => $n->event === 'approved');
    }

    public function test_literature_request_and_inventory_slip_and_change_request_dispatch_notifications(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));

        $litRole = Role::firstOrCreate(['name' => 'Lit User', 'guard_name' => 'web']);
        $litUser = User::factory()->create();
        $litUser->assignRole($litRole);

        $day = Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        $sb = ServiceBody::create([
            'ar_name' => 'لجنة القاهرة',
            'en_name' => 'Cairo Area',
            'day_id' => $day->id,
            'start_time' => '18:00:00',
            'end_time' => '20:00:00',
            'location' => 'Cairo',
        ]);

        $litRequest = LiteratureRequest::create([
            'service_body_id' => $sb->id,
            'request_type' => 'service_body',
            'month' => '2026-08-01',
            'status' => 'draft',
        ]);

        // Literature request approved & sent to Lit Committee
        $this->actingAs($admin)->post(route('literature-requests.approve-send', $litRequest->id))->assertRedirect();
        Notification::assertSentTo($litUser, LiteratureRequestStageNotification::class, fn ($n) => $n->stage === 'approved');

        // Inventory slip created in pending status
        $item = InventoryItem::create([
            'name' => 'Basic Text',
            'name_en' => 'Basic Text',
            'store_quantity' => 10,
            'lit_quantity' => 0,
            'selling_price' => 100,
        ]);
        app(InventoryLedgerService::class)->createTransferSlip([
            ['inventory_item_id' => $item->id, 'quantity' => 2, 'unit_price' => 100],
        ], $admin->id, 'Transfer');
        Notification::assertSentTo($litUser, InventorySlipPendingNotification::class);

        // Change request status update
        $requester = User::factory()->create();
        $cr = ChangeRequest::create([
            'user_id' => $requester->id,
            'request_type' => 'general',
            'subject' => 'Fix map pin',
            'description' => 'Details',
            'status' => 'pending',
        ]);

        Sanctum::actingAs($admin);
        $this->patchJson("/api/v1/change-requests/{$cr->id}/status", ['status' => 'completed'])->assertStatus(200);
        Notification::assertSentTo($requester, ChangeRequestStatusNotification::class, fn ($n) => $n->newStatus === 'completed');
    }

    public function test_notification_endpoints_for_web_and_api_list_and_mark_read(): void
    {
        Mail::fake();
        config(['queue.default' => 'sync']);

        $user = User::factory()->create();
        $cr1 = ChangeRequest::create([
            'user_id' => $user->id,
            'request_type' => 'general',
            'subject' => 'First',
            'description' => 'Desc',
            'status' => 'pending',
        ]);
        $cr2 = ChangeRequest::create([
            'user_id' => $user->id,
            'request_type' => 'general',
            'subject' => 'Second',
            'description' => 'Desc',
            'status' => 'pending',
        ]);

        $user->notify(new ChangeRequestStatusNotification($cr1, 'in_progress'));
        $user->notify(new ChangeRequestStatusNotification($cr2, 'completed'));

        // API v1 list
        Sanctum::actingAs($user);
        $listRes = $this->getJson('/api/v1/notifications');
        $listRes->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.unread_count', 2);

        $firstId = $listRes->json('data.0.id');

        // API v1 mark one as read
        $this->postJson("/api/v1/notifications/{$firstId}/read")
            ->assertStatus(200)
            ->assertJsonPath('unread_count', 1);

        // Web list & mark all as read
        $webRes = $this->actingAs($user, 'web')->get(route('notifications.index'));
        if ($webRes->status() === 302) {
            $webRes = $this->actingAs($user, 'web')->get($webRes->headers->get('Location'));
        }
        $webRes->assertStatus(200)
            ->assertJsonPath('meta.unread_count', 1);

        $readAllRes = $this->actingAs($user, 'web')->postJson(route('notifications.read_all'));
        if ($readAllRes->status() === 302) {
            $readAllRes = $this->actingAs($user, 'web')->postJson($readAllRes->headers->get('Location'));
        }
        $readAllRes->assertStatus(200)
            ->assertJsonPath('unread_count', 0);

        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }
}
