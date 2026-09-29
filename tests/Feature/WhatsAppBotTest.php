<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Day;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Neighborhood;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppSubscriber;
use App\Services\WhatsApp\WhatsAppBotService;
use App\Services\WhatsApp\WhatsAppClient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class WhatsAppBotTest extends TestCase
{
    use RefreshDatabase;

    protected $mockClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockClient = Mockery::mock(WhatsAppClient::class);
        $this->mockClient->shouldReceive('normalizePhone')->andReturnUsing(function ($val) {
            $c = preg_replace('/@.*$/', '', $val);
            return preg_replace('/[^0-9]/', '', $c);
        });
        $this->mockClient->shouldReceive('formatJid')->andReturnUsing(function ($val) {
            return str_contains($val, '@') ? $val : $val . '@s.whatsapp.net';
        });
        $this->mockClient->shouldReceive('sendTextMessage')->andReturn([
            'success' => true,
            'data' => ['results' => ['message_id' => 'TEST_SENT_123']],
        ]);

        $this->app->instance(WhatsAppClient::class, $this->mockClient);
    }

    public function test_jft_keyword_returns_todays_reading(): void
    {
        $bot = $this->app->make(WhatsAppBotService::class);

        $payload = [
            'sender' => '201006979198@s.whatsapp.net',
            'phone' => '201006979198',
            'name' => 'Test User',
            'message' => 'jft',
        ];

        $bot->handleIncomingMessage($payload);

        $conv = WhatsAppConversation::where('jid', '201006979198@s.whatsapp.net')->first();
        $this->assertNotNull($conv);

        $outgoing = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('direction', 'outgoing')
            ->where('category', 'jft')
            ->first();

        $this->assertNotNull($outgoing);
        $this->assertStringContainsString('فقط لليوم', $outgoing->body);
    }

    public function test_meetings_two_step_city_selection(): void
    {
        $bot = $this->app->make(WhatsAppBotService::class);

        // Step 1: User says meetings
        $bot->handleIncomingMessage([
            'sender' => '201011112222@s.whatsapp.net',
            'message' => 'اجتماعات',
        ]);

        $conv = WhatsAppConversation::where('jid', '201011112222@s.whatsapp.net')->first();
        $this->assertEquals('awaiting_city', $conv->current_step);

        // Step 2: User selects Cairo
        $bot->handleIncomingMessage([
            'sender' => '201011112222@s.whatsapp.net',
            'message' => '1',
        ]);

        $conv->refresh();
        $this->assertNull($conv->current_step);

        $outgoing = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('direction', 'outgoing')
            ->where('category', 'meetings')
            ->orderBy('id', 'desc')
            ->first();

        $this->assertNotNull($outgoing);
    }

    public function test_helpline_keyword_returns_hotline_numbers(): void
    {
        $bot = $this->app->make(WhatsAppBotService::class);

        $bot->handleIncomingMessage([
            'sender' => '201033334444@s.whatsapp.net',
            'message' => 'خط المساعدة',
        ]);

        $conv = WhatsAppConversation::where('jid', '201033334444@s.whatsapp.net')->first();
        $outgoing = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('direction', 'outgoing')
            ->where('category', 'helpline')
            ->first();

        $this->assertNotNull($outgoing);
        $this->assertStringContainsString('01006979198', $outgoing->body);
        $this->assertStringContainsString('01503884411', $outgoing->body);
    }

    public function test_live_agent_takeover_pauses_bot(): void
    {
        $bot = $this->app->make(WhatsAppBotService::class);

        // User requests volunteer
        $bot->handleIncomingMessage([
            'sender' => '201055556666@s.whatsapp.net',
            'message' => 'متطوع',
        ]);

        $conv = WhatsAppConversation::where('jid', '201055556666@s.whatsapp.net')->first();
        $this->assertTrue($conv->isLiveAgentActive());

        $outgoingCountBefore = WhatsAppMessage::where('conversation_id', $conv->id)->where('direction', 'outgoing')->count();

        // While active, user sends another message -> bot should NOT auto-reply
        $bot->handleIncomingMessage([
            'sender' => '201055556666@s.whatsapp.net',
            'message' => 'هل هناك أحد هنا؟',
        ]);

        $outgoingCountAfter = WhatsAppMessage::where('conversation_id', $conv->id)->where('direction', 'outgoing')->count();
        $this->assertEquals($outgoingCountBefore, $outgoingCountAfter);

        // User sends 'انهاء' -> resumes bot mode
        $bot->handleIncomingMessage([
            'sender' => '201055556666@s.whatsapp.net',
            'message' => 'انهاء',
        ]);

        $conv->refresh();
        $this->assertFalse($conv->isLiveAgentActive());
    }

    public function test_subscription_opt_in_and_opt_out(): void
    {
        $bot = $this->app->make(WhatsAppBotService::class);

        // Subscribe
        $bot->handleIncomingMessage([
            'sender' => '201077778888@s.whatsapp.net',
            'phone' => '201077778888',
            'message' => 'اشتراك',
        ]);

        $sub = WhatsAppSubscriber::where('jid', '201077778888@s.whatsapp.net')->first();
        $this->assertNotNull($sub);
        $this->assertTrue($sub->is_active);

        // Unsubscribe
        $bot->handleIncomingMessage([
            'sender' => '201077778888@s.whatsapp.net',
            'phone' => '201077778888',
            'message' => 'الغاء الاشتراك',
        ]);

        $sub->refresh();
        $this->assertFalse($sub->is_active);
    }
}
