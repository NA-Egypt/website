<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Day;
use App\Models\DirectOnlineGroup;
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
        $this->assertStringContainsString('لليوم فقط', $outgoing->body);
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

    public function test_helpline_keyword_returns_Helpline_numbers(): void
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

    public function test_option_zero_returns_android_app_direct_download_link(): void
    {
        $bot = $this->app->make(WhatsAppBotService::class);

        $bot->handleIncomingMessage([
            'sender' => '201055556666@s.whatsapp.net',
            'message' => '0',
        ]);

        $conv = WhatsAppConversation::where('jid', '201055556666@s.whatsapp.net')->first();
        $this->assertFalse($conv->isLiveAgentActive());

        $outgoing = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('direction', 'outgoing')
            ->where('category', 'app')
            ->first();

        $this->assertNotNull($outgoing);
        $this->assertStringContainsString('https://naegypt.org/apk/direct-download', $outgoing->body);
    }

    public function test_main_menu_shows_android_app_instead_of_live_agent(): void
    {
        $bot = $this->app->make(WhatsAppBotService::class);

        $bot->handleIncomingMessage([
            'sender' => '201055557777@s.whatsapp.net',
            'message' => 'مرحبا',
        ]);

        $conv = WhatsAppConversation::where('jid', '201055557777@s.whatsapp.net')->first();
        $outgoing = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('direction', 'outgoing')
            ->where('category', 'menu')
            ->first();

        $this->assertNotNull($outgoing);
        $this->assertStringContainsString('تحميل تطبيق الأندرويد', $outgoing->body);
        $this->assertStringNotContainsString('التحدث مع متطوع خط المساعدة', $outgoing->body);

        // User typing 'متطوع' no longer triggers live agent takeover
        $bot->handleIncomingMessage([
            'sender' => '201055557777@s.whatsapp.net',
            'message' => 'متطوع',
        ]);
        $conv->refresh();
        $this->assertFalse($conv->isLiveAgentActive());
    }

    public function test_agent_takeover_pauses_bot_and_agent_can_stop_it_from_phone_silently(): void
    {
        $bot = $this->app->make(WhatsAppBotService::class);

        // Agent replies from paired phone -> activates takeover
        $bot->handleIncomingMessage([
            'event' => 'message',
            'payload' => [
                'id' => 'AGENT_TAKEOVER_1',
                'from' => '201006979198@s.whatsapp.net',
                'chat' => '201055558888@s.whatsapp.net',
                'body' => 'أهلاً بك، معك متطوع',
                'is_from_me' => true,
            ],
        ]);

        $conv = WhatsAppConversation::where('jid', '201055558888@s.whatsapp.net')->first();
        $this->assertTrue($conv->isLiveAgentActive());

        // While active, user sends another message -> bot should NOT auto-reply
        $bot->handleIncomingMessage([
            'sender' => '201055558888@s.whatsapp.net',
            'message' => 'هل هناك أحد هنا؟',
        ]);

        $botReplies = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('sender_type', 'bot')
            ->count();
        $this->assertEquals(0, $botReplies);

        // Agent sends '#bot' from phone -> stops takeover silently
        $bot->handleIncomingMessage([
            'event' => 'message',
            'payload' => [
                'id' => 'AGENT_STOP_1',
                'from' => '201006979198@s.whatsapp.net',
                'chat' => '201055558888@s.whatsapp.net',
                'body' => '#bot',
                'is_from_me' => true,
            ],
        ]);

        $conv->refresh();
        $this->assertFalse($conv->isLiveAgentActive());

        // Still 0 bot replies (resumed silently)
        $botRepliesAfterStop = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('sender_type', 'bot')
            ->count();
        $this->assertEquals(0, $botRepliesAfterStop);

        // Next user message gets bot autoreply again
        $bot->handleIncomingMessage([
            'sender' => '201055558888@s.whatsapp.net',
            'message' => 'مرحبا',
        ]);

        $botRepliesFinal = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('sender_type', 'bot')
            ->count();
        $this->assertEquals(1, $botRepliesFinal);
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

    public function test_bot_handles_nested_microservice_payload_structure(): void
    {
        $bot = $this->app->make(WhatsAppBotService::class);

        $payload = [
            'event' => 'message',
            'device_id' => 'default',
            'payload' => [
                'id' => 'WAGO_NESTED_12345',
                'from' => '201099998888@s.whatsapp.net',
                'push_name' => 'Ahmed NA',
                'body' => 'jft',
                'is_from_me' => false,
            ],
        ];

        $bot->handleIncomingMessage($payload);

        $conv = WhatsAppConversation::where('jid', '201099998888@s.whatsapp.net')->first();
        $this->assertNotNull($conv);
        $this->assertEquals('Ahmed NA', $conv->name);

        $outgoing = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('direction', 'outgoing')
            ->where('category', 'jft')
            ->first();

        $this->assertNotNull($outgoing);
        $this->assertStringContainsString('لليوم فقط', $outgoing->body);
    }

    public function test_volunteer_reply_from_phone_activates_live_agent_and_suppresses_bot(): void
    {
        $bot = $this->app->make(WhatsAppBotService::class);

        $payload = [
            'event' => 'message',
            'device_id' => 'default',
            'payload' => [
                'id' => 'PHONE_VOLUNTEER_MSG_99',
                'from' => '201551590069@s.whatsapp.net',
                'chat' => '201012345678@s.whatsapp.net',
                'body' => 'أهلاً بك، معك متطوع خط المساعدة. كيف أساعدك؟',
                'is_from_me' => true,
            ],
        ];

        $bot->handleIncomingMessage($payload);

        $conv = WhatsAppConversation::where('jid', '201012345678@s.whatsapp.net')->first();
        $this->assertNotNull($conv);
        $this->assertTrue($conv->isLiveAgentActive());

        $msg = WhatsAppMessage::where('message_id', 'PHONE_VOLUNTEER_MSG_99')->first();
        $this->assertNotNull($msg);
        $this->assertEquals('outgoing', $msg->direction);
        $this->assertEquals('agent', $msg->sender_type);
        $this->assertEquals('phone_reply', $msg->category);

        // Verify bot did NOT send any automated reply to this volunteer message
        $botReplies = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('sender_type', 'bot')
            ->count();
        $this->assertEquals(0, $botReplies);
    }

    public function test_meetings_submenu_responds_to_available_city_meetings(): void
    {
        $todayName = Carbon::now('Africa/Cairo')->format('l');
        $day = Day::firstOrCreate(['en_name' => $todayName], ['ar_name' => 'اليوم']);
        $city = City::create(['en_name' => 'Cairo', 'ar_name' => 'القاهرة']);
        $neighborhood = Neighborhood::create(['en_name' => 'Maadi', 'ar_name' => 'المعادي', 'city_id' => $city->id]);
        $user = \App\Models\User::factory()->create();
        $serviceBody = \App\Models\ServiceBody::create(['ar_name' => 'لجنة', 'en_name' => 'ASC', 'day_id' => $day->id, 'start_time' => '18:00', 'end_time' => '19:00', 'location' => 'Cairo']);
        $topic = \App\Models\Topic::create(['ar_name' => 'موضوع', 'en_name' => 'Topic']);

        $group = new Group([
            'en_name' => 'Maadi Hope',
            'ar_name' => 'أمل المعادي',
            'ar_gsr_name' => 'خادم',
            'en_gsr_name' => 'GSR',
            'phone' => '01012345678',
            'location' => 'المعادي',
            'neighborhood_id' => $neighborhood->id,
            'ar_address' => 'شارع 9 المعادي',
            'en_address' => 'St 9 Maadi',
        ]);
        $group->user_id = $user->id;
        $group->service_body_id = $serviceBody->id;
        $group->save();

        Meeting::create([
            'group_id' => $group->id,
            'topic_id' => $topic->id,
            'day_id' => $day->id,
            'start_time' => '21:00',
            'end_time' => '22:00',
            'type' => 'open',
            'status' => 'available',
        ]);

        Meeting::create([
            'group_id' => $group->id,
            'topic_id' => $topic->id,
            'day_id' => $day->id,
            'start_time' => '18:00',
            'end_time' => '19:00',
            'type' => 'open',
            'status' => 'available',
        ]);

        $bot = $this->app->make(WhatsAppBotService::class);
        $bot->handleIncomingMessage(['sender' => '201022223333@s.whatsapp.net', 'message' => '2']);
        $bot->handleIncomingMessage(['sender' => '201022223333@s.whatsapp.net', 'message' => '1']);

        $conv = WhatsAppConversation::where('jid', '201022223333@s.whatsapp.net')->first();
        $outgoing = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('direction', 'outgoing')
            ->where('category', 'meetings')
            ->orderByDesc('id')
            ->first();

        $this->assertNotNull($outgoing);
        $this->assertStringContainsString('أمل المعادي', $outgoing->body);
        $this->assertStringContainsString('18:00', $outgoing->body);
        $this->assertStringContainsString('21:00', $outgoing->body);
        $this->assertLessThan(strpos($outgoing->body, '21:00'), strpos($outgoing->body, '18:00'));
    }

    public function test_sending_group_name_returns_todays_meetings_for_that_group(): void
    {
        $now = Carbon::now('Africa/Cairo');
        $todayDay = Day::firstOrCreate(['en_name' => $now->format('l')], ['ar_name' => 'السبت']);
        $otherDay = Day::firstOrCreate(['en_name' => $now->copy()->addDay()->format('l')], ['ar_name' => 'الأحد']);

        $city = City::create(['en_name' => 'Cairo', 'ar_name' => 'القاهرة']);
        $neighborhood = Neighborhood::create(['en_name' => 'Abbaseya', 'ar_name' => 'العباسية', 'city_id' => $city->id]);
        $user = \App\Models\User::factory()->create();
        $serviceBody = \App\Models\ServiceBody::create(['ar_name' => 'لجنة', 'en_name' => 'ASC', 'day_id' => $todayDay->id, 'start_time' => '18:00', 'end_time' => '19:00', 'location' => 'Cairo']);
        $topic = \App\Models\Topic::create(['ar_name' => 'موضوع', 'en_name' => 'Topic']);

        $group = new Group([
            'en_name' => 'Abbaseya',
            'ar_name' => 'العباسية',
            'ar_gsr_name' => 'خادم',
            'en_gsr_name' => 'GSR',
            'phone' => '01012345678',
            'location' => 'العباسية',
            'neighborhood_id' => $neighborhood->id,
            'ar_address' => '54 شارع بين الجناين',
            'en_address' => '54 Been El Gnaien',
        ]);
        $group->user_id = $user->id;
        $group->service_body_id = $serviceBody->id;
        $group->save();

        Meeting::create([
            'group_id' => $group->id,
            'topic_id' => $topic->id,
            'day_id' => $todayDay->id,
            'start_time' => '20:30',
            'end_time' => '21:30',
            'type' => 'open',
            'status' => 'available',
        ]);

        // Group with no meeting today
        $tomorrowGroup = new Group([
            'en_name' => 'Serenity',
            'ar_name' => 'السكينة',
            'ar_gsr_name' => 'خادم',
            'en_gsr_name' => 'GSR',
            'phone' => '01012345679',
            'location' => 'العباسية',
            'neighborhood_id' => $neighborhood->id,
            'ar_address' => 'عنوان',
            'en_address' => 'Address',
        ]);
        $tomorrowGroup->user_id = $user->id;
        $tomorrowGroup->service_body_id = $serviceBody->id;
        $tomorrowGroup->save();

        Meeting::create([
            'group_id' => $tomorrowGroup->id,
            'topic_id' => $topic->id,
            'day_id' => $otherDay->id,
            'start_time' => '19:00',
            'end_time' => '20:00',
            'type' => 'open',
            'status' => 'available',
        ]);

        $bot = $this->app->make(WhatsAppBotService::class);
        $bot->handleIncomingMessage(['sender' => '201044445555@s.whatsapp.net', 'message' => 'العباسية']);

        $conv = WhatsAppConversation::where('jid', '201044445555@s.whatsapp.net')->first();
        $outgoing = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('direction', 'outgoing')
            ->where('category', 'meetings')
            ->orderByDesc('id')
            ->first();

        $this->assertNotNull($outgoing);
        $this->assertStringContainsString('العباسية', $outgoing->body);
        $this->assertStringContainsString('20:30', $outgoing->body);

        $bot->handleIncomingMessage(['sender' => '201044445555@s.whatsapp.net', 'message' => 'السكينة']);
        $outgoingNone = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('direction', 'outgoing')
            ->where('category', 'meetings')
            ->orderByDesc('id')
            ->first();

        $this->assertStringContainsString('السكينة', $outgoingNone->body);
        $this->assertStringContainsString('لا توجد اجتماعات مسجلة لهذه المجموعة اليوم', $outgoingNone->body);
    }

    public function test_online_zoom_keywords_return_nearest_three_zoom_meetings_earliest_first(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 28, 18, 0, 0, 'Africa/Cairo'));

        $todayDay = Day::firstOrCreate(['en_name' => 'Saturday'], ['ar_name' => 'السبت']);
        $tomorrowDay = Day::firstOrCreate(['en_name' => 'Sunday'], ['ar_name' => 'الأحد']);
        $topic = \App\Models\Topic::create(['ar_name' => 'موضوع', 'en_name' => 'Topic']);

        $og1 = DirectOnlineGroup::create(['ar_name' => 'أونلاين 1', 'en_name' => 'Online 1', 'ar_gsr_name' => 'خادم', 'en_gsr_name' => 'GSR', 'phone' => '0101', 'location' => 'https://zoom.us/j/111']);
        $og2 = DirectOnlineGroup::create(['ar_name' => 'أونلاين 2', 'en_name' => 'Online 2', 'ar_gsr_name' => 'خادم', 'en_gsr_name' => 'GSR', 'phone' => '0102', 'location' => 'https://zoom.us/j/222']);
        $og3 = DirectOnlineGroup::create(['ar_name' => 'أونلاين 3', 'en_name' => 'Online 3', 'ar_gsr_name' => 'خادم', 'en_gsr_name' => 'GSR', 'phone' => '0103', 'location' => 'https://zoom.us/j/333']);
        $ogPast = DirectOnlineGroup::create(['ar_name' => 'أونلاين قديم', 'en_name' => 'Online Past', 'ar_gsr_name' => 'خادم', 'en_gsr_name' => 'GSR', 'phone' => '0100', 'location' => 'https://zoom.us/j/000']);

        // Past meeting today (should be skipped)
        Meeting::create(['direct_online_group_id' => $ogPast->id, 'topic_id' => $topic->id, 'day_id' => $todayDay->id, 'start_time' => '15:00', 'end_time' => '16:00', 'type' => 'open', 'status' => 'available']);
        // 2 upcoming meetings today (21:00 and 19:00)
        Meeting::create(['direct_online_group_id' => $og2->id, 'topic_id' => $topic->id, 'day_id' => $todayDay->id, 'start_time' => '21:00', 'end_time' => '22:00', 'type' => 'open', 'status' => 'available']);
        Meeting::create(['direct_online_group_id' => $og1->id, 'topic_id' => $topic->id, 'day_id' => $todayDay->id, 'start_time' => '19:00', 'end_time' => '20:00', 'type' => 'open', 'status' => 'available']);
        // 1 earliest meeting tomorrow (10:00) to fill the 3rd slot
        Meeting::create(['direct_online_group_id' => $og3->id, 'topic_id' => $topic->id, 'day_id' => $tomorrowDay->id, 'start_time' => '10:00', 'end_time' => '11:00', 'type' => 'open', 'status' => 'available']);

        $bot = $this->app->make(WhatsAppBotService::class);
        $bot->handleIncomingMessage(['sender' => '201066667777@s.whatsapp.net', 'message' => 'زووم']);

        $conv = WhatsAppConversation::where('jid', '201066667777@s.whatsapp.net')->first();
        $outgoing = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('direction', 'outgoing')
            ->where('category', 'meetings')
            ->orderByDesc('id')
            ->first();

        Carbon::setTestNow();

        $this->assertNotNull($outgoing);
        $this->assertStringNotContainsString('https://zoom.us/j/000', $outgoing->body);
        $this->assertStringContainsString('https://zoom.us/j/111', $outgoing->body);
        $this->assertStringContainsString('https://zoom.us/j/222', $outgoing->body);
        $this->assertStringContainsString('https://zoom.us/j/333', $outgoing->body);
        // Earliest today (19:00) before later today (21:00) before tomorrow (10:00)
        $this->assertLessThan(strpos($outgoing->body, 'https://zoom.us/j/222'), strpos($outgoing->body, 'https://zoom.us/j/111'));
        $this->assertLessThan(strpos($outgoing->body, 'https://zoom.us/j/333'), strpos($outgoing->body, 'https://zoom.us/j/222'));
    }
}
