<?php

namespace Tests\Feature;

use App\Jobs\SendWhatsAppBroadcast;
use App\Models\WhatsAppSubscriber;
use App\Services\WhatsApp\WhatsAppClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class WhatsAppBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_artisan_command_dispatches_broadcast_job(): void
    {
        Queue::fake();

        $this->artisan('whatsapp:broadcast-jft')
            ->assertExitCode(0);

        Queue::assertPushed(SendWhatsAppBroadcast::class, function ($job) {
            return $job->channel === 'jft';
        });
    }

    public function test_broadcast_job_iterates_active_subscribers(): void
    {
        $mockClient = Mockery::mock(WhatsAppClient::class);
        $mockClient->shouldReceive('getDeviceStatus')->once()->andReturn([
            'success' => true,
            'connected' => true,
        ]);
        $mockClient->shouldReceive('sendTextMessage')->andReturn([
            'success' => true,
            'data' => ['results' => ['message_id' => 'BROADCAST_MSG_1']],
        ]);
        $this->app->instance(WhatsAppClient::class, $mockClient);

        // Active subscriber
        WhatsAppSubscriber::create([
            'jid' => '201011111111@s.whatsapp.net',
            'phone' => '201011111111',
            'channel' => 'jft',
            'is_active' => true,
        ]);

        // Inactive subscriber (should not receive)
        WhatsAppSubscriber::create([
            'jid' => '201022222222@s.whatsapp.net',
            'phone' => '201022222222',
            'channel' => 'jft',
            'is_active' => false,
        ]);

        $job = new SendWhatsAppBroadcast('jft', 'Test Broadcast', 'Daily reading message');
        $job->handle($mockClient);

        $this->assertDatabaseHas('whatsapp_broadcast_logs', [
            'channel' => 'jft',
            'total_recipients' => 1,
            'successful_count' => 1,
        ]);

        $this->assertDatabaseHas('whatsapp_messages', [
            'direction' => 'outgoing',
            'category' => 'broadcast',
            'body' => 'Daily reading message',
        ]);
    }

    public function test_broadcast_job_pauses_when_device_disconnected(): void
    {
        $mockClient = Mockery::mock(WhatsAppClient::class);
        $mockClient->shouldReceive('getDeviceStatus')->once()->andReturn([
            'success' => true,
            'connected' => false,
        ]);
        $mockClient->shouldNotReceive('sendTextMessage');
        $this->app->instance(WhatsAppClient::class, $mockClient);

        WhatsAppSubscriber::create([
            'jid' => '201011111111@s.whatsapp.net',
            'phone' => '201011111111',
            'channel' => 'jft',
            'is_active' => true,
        ]);

        $job = new SendWhatsAppBroadcast('jft', 'Disconnected Broadcast', 'Daily reading');
        $job->handle($mockClient);

        $this->assertDatabaseHas('whatsapp_broadcast_logs', [
            'channel' => 'jft',
            'title' => 'Disconnected Broadcast',
            'status' => 'paused',
            'successful_count' => 0,
            'failed_count' => 0,
        ]);
    }

    public function test_broadcast_job_triggers_circuit_breaker_on_invalid_wa_cli(): void
    {
        $mockClient = Mockery::mock(WhatsAppClient::class);
        $mockClient->shouldReceive('getDeviceStatus')->once()->andReturn([
            'success' => true,
            'connected' => true,
        ]);
        $mockClient->shouldReceive('sendTextMessage')->once()->andReturn([
            'success' => false,
            'status' => 500,
            'error' => '{"code":"INVALID_WA_CLI","message":"your WhatsApp CLI is invalid or empty"}',
        ]);
        $this->app->instance(WhatsAppClient::class, $mockClient);

        WhatsAppSubscriber::create([
            'jid' => '201011111111@s.whatsapp.net',
            'phone' => '201011111111',
            'channel' => 'jft',
            'is_active' => true,
        ]);
        WhatsAppSubscriber::create([
            'jid' => '201033333333@s.whatsapp.net',
            'phone' => '201033333333',
            'channel' => 'jft',
            'is_active' => true,
        ]);

        $job = new SendWhatsAppBroadcast('jft', 'Circuit Breaker Broadcast', 'Daily reading');
        $job->handle($mockClient);

        $this->assertDatabaseHas('whatsapp_broadcast_logs', [
            'channel' => 'jft',
            'title' => 'Circuit Breaker Broadcast',
            'status' => 'paused',
            'successful_count' => 0,
            'failed_count' => 1,
        ]);
    }
}
