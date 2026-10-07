<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\WhatsApp\WhatsAppClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class WhatsAppAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $unauthorizedUser;
    protected User $volunteerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LocalizationRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LocaleViewPath::class,
        ]);

        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $roleSuper = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole($roleSuper);

        $this->unauthorizedUser = User::factory()->create();

        $this->volunteerUser = User::factory()->create();
        $permView = Permission::firstOrCreate(['name' => 'view whatsapp chats', 'guard_name' => 'web']);
        $permReply = Permission::firstOrCreate(['name' => 'reply whatsapp messages', 'guard_name' => 'web']);
        $this->volunteerUser->givePermissionTo([$permView, $permReply]);
    }

    public function test_guest_is_redirected_when_unauthenticated(): void
    {
        $response = $this->get(route('whatsapp.inbox'));
        $response->assertRedirect('/');
    }

    public function test_unauthorized_user_is_forbidden_from_admin_inbox(): void
    {
        $response = $this->actingAs($this->unauthorizedUser)->get(route('whatsapp.inbox'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_all_whatsapp_admin_pages(): void
    {
        // Device page
        $resDevice = $this->actingAs($this->superAdmin)->get(route('whatsapp.device.status'));
        $resDevice->assertStatus(200);

        // Inbox page
        $resInbox = $this->actingAs($this->superAdmin)->get(route('whatsapp.inbox'));
        $resInbox->assertStatus(200);

        // Subscribers page
        $resSub = $this->actingAs($this->superAdmin)->get(route('whatsapp.subscribers.index'));
        $resSub->assertStatus(200);

        // Reports page
        $resReports = $this->actingAs($this->superAdmin)->get(route('whatsapp.reports.index'));
        $resReports->assertStatus(200);

        // Documentation page
        $resDocs = $this->actingAs($this->superAdmin)->get(route('whatsapp.docs'));
        $resDocs->assertStatus(200);
    }

    public function test_volunteer_can_reply_and_activate_live_agent(): void
    {
        $mockClient = Mockery::mock(WhatsAppClient::class);
        $mockClient->shouldReceive('sendTextMessage')->andReturn([
            'success' => true,
            'data' => ['results' => ['message_id' => 'AGENT_REPLY_123']],
        ]);
        $this->app->instance(WhatsAppClient::class, $mockClient);

        $conv = WhatsAppConversation::create([
            'jid' => '201099998888@s.whatsapp.net',
            'phone' => '201099998888',
            'name' => 'Inquiring Caller',
            'is_live_agent_mode' => false,
        ]);

        $response = $this->actingAs($this->volunteerUser)
            ->post(route('whatsapp.inbox.send', $conv), [
                'message' => 'Hello, I am a helpline volunteer. How can I help you today?',
            ]);

        $response->assertRedirect(route('whatsapp.inbox', ['conversation_id' => $conv->id]));

        $conv->refresh();
        $this->assertTrue($conv->isLiveAgentActive());
        $this->assertEquals($this->volunteerUser->id, $conv->last_assigned_user_id);

        $this->assertDatabaseHas('whatsapp_messages', [
            'conversation_id' => $conv->id,
            'direction' => 'outgoing',
            'sender_type' => 'agent',
            'user_id' => $this->volunteerUser->id,
        ]);
    }

    public function test_reports_csv_export(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('whatsapp.reports.export'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_super_admin_can_download_educational_sample_csv(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('whatsapp.subscribers.sample-csv'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('phone,name,message', $response->getContent());
    }

    public function test_super_admin_can_dispatch_bulk_csv_campaign(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        $csvContent = "phone,name,message\n+201011112222,Ahmed,مرحبا بك\n01122223333,Mohamed,أهلاً وسهلاً\n";
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('contacts.csv', $csvContent);

        $response = $this->actingAs($this->superAdmin)->post(route('whatsapp.subscribers.bulk-csv'), [
            'csv_file' => $file,
            'device_id' => 'default',
            'send_mode' => 'custom_per_row',
            'anti_ban_profile' => 'safe',
            'append_optout' => '1',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('whatsapp_broadcast_logs', [
            'channel' => 'bulk_csv',
            'status' => 'processing',
            'total_recipients' => 2,
        ]);

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SendWhatsAppBulkCsvBroadcast::class);
    }

    public function test_device_check_returns_json_status(): void
    {
        $mockClient = Mockery::mock(WhatsAppClient::class);
        $mockClient->shouldReceive('getDeviceStatus')->with('default')->andReturn([
            'success' => true,
            'connected' => true,
            'status' => 'CONNECTED',
            'device_id' => 'default',
            'data' => ['results' => ['jid' => '201551590069@s.whatsapp.net']],
        ]);
        $this->app->instance(WhatsAppClient::class, $mockClient);

        $response = $this->actingAs($this->superAdmin)->get(route('whatsapp.device.check', ['device_id' => 'default']));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'connected' => true,
            'status' => 'CONNECTED',
        ]);
    }

    public function test_bulk_csv_excludes_recently_sent_contacts(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        // Create an already-sent contact in the last 2 hours
        $conv = WhatsAppConversation::create([
            'jid' => '201011112222@s.whatsapp.net',
            'phone' => '201011112222',
            'name' => 'Ahmed',
        ]);

        WhatsAppMessage::create([
            'conversation_id' => $conv->id,
            'direction' => 'outgoing',
            'sender_type' => 'agent',
            'category' => 'bulk_csv',
            'message_type' => 'text',
            'body' => 'Previous message',
            'status' => 'sent',
            'created_at' => \Carbon\Carbon::now()->subHours(2),
        ]);

        $csvContent = "phone,name,message\n+201011112222,Ahmed,رسالة مكررة\n01122223333,Mohamed,أهلاً وسهلاً\n";
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('contacts.csv', $csvContent);

        $response = $this->actingAs($this->superAdmin)->post(route('whatsapp.subscribers.bulk-csv'), [
            'csv_file' => $file,
            'device_id' => 'default',
            'send_mode' => 'custom_per_row',
            'anti_ban_profile' => 'safe',
            'exclude_recent' => '1',
            'exclude_hours' => '48',
        ]);

        $response->assertSessionHas('success');
        // Only 1 recipient should be queued because Ahmed (+201011112222) was excluded!
        $this->assertDatabaseHas('whatsapp_broadcast_logs', [
            'channel' => 'bulk_csv',
            'status' => 'processing',
            'total_recipients' => 1,
        ]);
    }

    public function test_super_admin_can_resume_paused_broadcast(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        $mockClient = Mockery::mock(WhatsAppClient::class);
        $mockClient->shouldReceive('getDeviceStatus')->with('default')->andReturn([
            'success' => true,
            'connected' => true,
            'status' => 'CONNECTED',
            'device_id' => 'default',
        ]);
        $this->app->instance(WhatsAppClient::class, $mockClient);

        $broadcast = \App\Models\WhatsAppBroadcastLog::create([
            'channel' => 'bulk_csv',
            'device_id' => 'default',
            'title' => 'Paused Campaign',
            'total_recipients' => 10,
            'successful_count' => 3,
            'failed_count' => 0,
            'status' => 'paused',
            'anti_ban_profile' => 'safe',
            'metadata' => [
                'current_index' => 3,
                'recipients' => [],
                'logs' => ['Paused'],
            ],
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('whatsapp.broadcasts.resume', $broadcast));
        $response->assertSessionHas('success');

        $broadcast->refresh();
        $this->assertEquals('processing', $broadcast->status);

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SendWhatsAppBulkCsvBroadcast::class);
    }

    public function test_bulk_csv_accepts_warmup_profile(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        $csvContent = "phone,name,message\n+201011112222,Ahmed,مرحبا بك\n";
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('contacts.csv', $csvContent);

        $response = $this->actingAs($this->superAdmin)->post(route('whatsapp.subscribers.bulk-csv'), [
            'csv_file' => $file,
            'device_id' => 'll',
            'send_mode' => 'custom_per_row',
            'anti_ban_profile' => 'warmup',
            'enable_cooldown' => '1',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('whatsapp_broadcast_logs', [
            'device_id' => 'll',
            'status' => 'processing',
            'anti_ban_profile' => 'warmup',
        ]);

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SendWhatsAppBulkCsvBroadcast::class);
    }

    public function test_inbox_conversations_api_returns_json_list(): void
    {
        WhatsAppConversation::create([
            'jid' => '201011119999@s.whatsapp.net',
            'phone' => '201011119999',
            'name' => 'Searchable Contact',
        ]);

        $response = $this->actingAs($this->volunteerUser)
            ->getJson(route('whatsapp.inbox.conversations', ['search' => 'Searchable']));

        $response->assertStatus(200);
        $response->assertJsonStructure(['conversations', 'total', 'current_page']);
        $this->assertTrue(collect($response->json('conversations'))->contains('name', 'Searchable Contact'));
    }

    public function test_volunteer_can_save_internal_notes(): void
    {
        $conv = WhatsAppConversation::create([
            'jid' => '201088887777@s.whatsapp.net',
            'phone' => '201088887777',
            'name' => 'Fellowship Friend',
        ]);

        $response = $this->actingAs($this->volunteerUser)
            ->postJson(route('whatsapp.inbox.notes', $conv), [
                'notes' => 'Clean date: 10/7/2004, regular attendee at Maadi group.',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $conv->refresh();
        $this->assertEquals('Clean date: 10/7/2004, regular attendee at Maadi group.', $conv->notes);
    }

    public function test_volunteer_can_toggle_jft_subscription(): void
    {
        $conv = WhatsAppConversation::create([
            'jid' => '201077776666@s.whatsapp.net',
            'phone' => '201077776666',
            'name' => 'Subscriber Friend',
        ]);

        // 1. Opt-in toggle
        $resOptIn = $this->actingAs($this->volunteerUser)
            ->postJson(route('whatsapp.inbox.toggle-subscription', $conv));

        $resOptIn->assertStatus(200);
        $resOptIn->assertJson(['success' => true, 'is_subscribed' => true]);
        $this->assertDatabaseHas('whatsapp_subscribers', [
            'jid' => '201077776666@s.whatsapp.net',
            'is_active' => true,
        ]);

        // 2. Opt-out toggle
        $resOptOut = $this->actingAs($this->volunteerUser)
            ->postJson(route('whatsapp.inbox.toggle-subscription', $conv));

        $resOptOut->assertStatus(200);
        $resOptOut->assertJson(['success' => true, 'is_subscribed' => false]);
        $this->assertDatabaseHas('whatsapp_subscribers', [
            'jid' => '201077776666@s.whatsapp.net',
            'is_active' => false,
        ]);
    }
}

