<?php

namespace Tests\Feature\Api;

use App\Jobs\ProcessIncomingWhatsAppMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WhatsAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_dispatches_queue_job_successfully(): void
    {
        Queue::fake();

        $payload = [
            'sender' => '201006979198@s.whatsapp.net',
            'phone' => '201006979198',
            'name' => 'Fellowship Member',
            'message' => 'jft',
            'message_id' => 'WAMSGID12345',
        ];

        $response = $this->postJson(route('api.v1.whatsapp.webhook'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'queued',
            ]);

        Queue::assertPushed(ProcessIncomingWhatsAppMessage::class, function ($job) use ($payload) {
            return $job->payload['message'] === 'jft' && $job->payload['sender'] === $payload['sender'];
        });
    }

    public function test_webhook_rejects_unauthorized_requests_when_secret_configured(): void
    {
        Config::set('whatsapp.webhook_secret', 'correct_secret_token_123');

        $payload = [
            'sender' => '201006979198@s.whatsapp.net',
            'message' => 'hello',
        ];

        // Without header -> 401
        $response = $this->postJson(route('api.v1.whatsapp.webhook'), $payload);
        $response->assertStatus(401);

        // With wrong header -> 401
        $responseWrong = $this->postJson(route('api.v1.whatsapp.webhook'), $payload, [
            'X-Webhook-Secret' => 'wrong_token',
        ]);
        $responseWrong->assertStatus(401);

        // With correct header -> 200
        Queue::fake();
        $responseCorrect = $this->postJson(route('api.v1.whatsapp.webhook'), $payload, [
            'X-Webhook-Secret' => 'correct_secret_token_123',
        ]);
        $responseCorrect->assertStatus(200);
    }
}
