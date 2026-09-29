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
}
