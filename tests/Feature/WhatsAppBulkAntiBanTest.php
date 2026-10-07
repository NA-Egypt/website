<?php

namespace Tests\Feature;

use App\Jobs\SendWhatsAppBulkCsvBroadcast;
use App\Models\WhatsAppBroadcastLog;
use App\Services\WhatsApp\WhatsAppClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class WhatsAppBulkAntiBanTest extends TestCase
{
    use RefreshDatabase;

    public function test_spintax_renders_random_variations(): void
    {
        $client = app(WhatsAppClient::class);

        $template = '{أهلاً|مرحباً|السلام عليكم} يا {name}';
        $results = [];

        for ($i = 0; $i < 20; $i++) {
            $rendered = $client->renderSpintax(str_replace('{name}', 'أحمد', $template));
            $this->assertMatchesRegularExpression('/^(أهلاً|مرحباً|السلام عليكم) يا أحمد$/u', $rendered);
            $results[$rendered] = true;
        }

        // With 20 iterations across 3 choices, multiple unique variations should be generated
        $this->assertGreaterThan(1, count($results));
    }

    public function test_nested_spintax_renders_correctly(): void
    {
        $client = app(WhatsAppClient::class);

        $template = '{صباح الخير|{أهلاً|مرحباً}} صديقنا';
        $rendered = $client->renderSpintax($template);

        $this->assertContains($rendered, ['صباح الخير صديقنا', 'أهلاً صديقنا', 'مرحباً صديقنا']);
    }

    public function test_bulk_broadcast_skips_unregistered_whatsapp_numbers(): void
    {
        Queue::fake();

        $log = WhatsAppBroadcastLog::create([
            'channel' => 'bulk_csv',
            'device_id' => 'default',
            'title' => 'Test Campaign',
            'total_recipients' => 2,
            'successful_count' => 0,
            'failed_count' => 0,
            'status' => 'processing',
            'anti_ban_profile' => 'ultra_safe',
            'metadata' => [
                'recipients' => [
                    ['phone' => '201040454994', 'name' => 'Unregistered Contact', 'message' => 'Hello'],
                    ['phone' => '201011112222', 'name' => 'Registered Contact', 'message' => 'Hello'],
                ],
                'current_index' => 0,
                'cooldown_counter' => 0,
                'session_cap' => 35,
                'session_sent_count' => 0,
                'unregistered_count' => 0,
                'send_mode' => 'template_to_all',
                'default_message' => 'Hello {name}',
                'anti_ban_profile' => 'ultra_safe',
                'device_id' => 'default',
            ],
        ]);

        $mockClient = Mockery::mock(WhatsAppClient::class);
        $mockClient->shouldReceive('getDeviceStatus')->andReturn(['connected' => true]);
        $mockClient->shouldReceive('formatJid')->andReturn('201040454994@s.whatsapp.net');
        // Simulate phone not on WhatsApp
        $mockClient->shouldReceive('checkUser')
            ->with('201040454994', 'default')
            ->andReturn(['success' => true, 'is_on_whatsapp' => false]);

        // Job handle
        $job = new SendWhatsAppBulkCsvBroadcast($log->id);
        $job->handle($mockClient);

        $log->refresh();
        $this->assertEquals(1, $log->metadata['unregistered_count']);
        $this->assertEquals(1, $log->metadata['current_index']);
        $this->assertEquals(0, $log->successful_count);
        $logs = $log->metadata['logs'] ?? [];
        $this->assertStringContainsString('Number is NOT registered on WhatsApp', end($logs));

        // Queued next step
        Queue::assertPushed(SendWhatsAppBulkCsvBroadcast::class);
    }

    public function test_bulk_broadcast_pauses_when_session_cap_reached(): void
    {
        Queue::fake();

        $log = WhatsAppBroadcastLog::create([
            'channel' => 'bulk_csv',
            'device_id' => 'default',
            'title' => 'Test Capped Campaign',
            'total_recipients' => 50,
            'successful_count' => 35,
            'failed_count' => 0,
            'status' => 'processing',
            'anti_ban_profile' => 'ultra_safe',
            'metadata' => [
                'recipients' => array_fill(0, 50, ['phone' => '201011112222', 'name' => 'Test', 'message' => 'Hi']),
                'current_index' => 35,
                'session_cap' => 35,
                'session_sent_count' => 35, // Cap already met
                'anti_ban_profile' => 'ultra_safe',
                'device_id' => 'default',
            ],
        ]);

        $mockClient = Mockery::mock(WhatsAppClient::class);

        $job = new SendWhatsAppBulkCsvBroadcast($log->id);
        $job->handle($mockClient);

        $log->refresh();
        $this->assertEquals('paused', $log->status);
        $logs = $log->metadata['logs'] ?? [];
        $this->assertStringContainsString('Safety Session Limit Reached', end($logs));
        Queue::assertNotPushed(SendWhatsAppBulkCsvBroadcast::class);
    }
}
