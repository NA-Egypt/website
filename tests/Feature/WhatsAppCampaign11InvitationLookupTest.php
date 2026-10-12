<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Day;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Neighborhood;
use App\Models\User;
use App\Models\WhatsAppBroadcastLog;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\WhatsApp\WhatsAppBotService;
use App\Services\WhatsApp\WhatsAppClient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

class WhatsAppCampaign11InvitationLookupTest extends TestCase
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
            'data' => ['results' => ['message_id' => 'TEST_INVITATION_123']],
        ]);

        $this->app->instance(WhatsAppClient::class, $this->mockClient);

        // Seed days
        Day::create(['id' => 1, 'en_name' => 'Saturday', 'ar_name' => 'السبت']);
        Day::create(['id' => 2, 'en_name' => 'Sunday', 'ar_name' => 'الأحد']);
        Day::create(['id' => 3, 'en_name' => 'Monday', 'ar_name' => 'الاثنين']);
        Day::create(['id' => 4, 'en_name' => 'Tuesday', 'ar_name' => 'الثلاثاء']);
        Day::create(['id' => 5, 'en_name' => 'Wednesday', 'ar_name' => 'الأربعاء']);
        Day::create(['id' => 6, 'en_name' => 'Thursday', 'ar_name' => 'الخميس']);
        Day::create(['id' => 7, 'en_name' => 'Friday', 'ar_name' => 'الجمعة']);
    }

    public function test_sending_9_falls_back_to_main_menu(): void
    {
        $bot = $this->app->make(WhatsAppBotService::class);

        $bot->handleIncomingMessage([
            'sender' => '201066693306@s.whatsapp.net',
            'phone' => '201066693306',
            'message' => '9',
        ]);

        $conv = WhatsAppConversation::where('phone', '201066693306')->first();
        $this->assertNotNull($conv);

        $outgoing = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('direction', 'outgoing')
            ->where('category', 'menu')
            ->first();

        $this->assertNotNull($outgoing);
        $this->assertStringContainsString('أهلاً بك في واتساب زمالة المدمنين المجهولين', $outgoing->body);
        $this->assertStringNotContainsString('9️⃣', $outgoing->body);
        $this->assertStringNotContainsString('مسار يجمعنا', $outgoing->body);
    }

    public function test_eastern_arabic_numeral_9_and_convention_keywords_fall_back_to_main_menu(): void
    {
        $bot = $this->app->make(WhatsAppBotService::class);

        // Sending Eastern Arabic numeral ٩
        $bot->handleIncomingMessage([
            'sender' => '201005855853@s.whatsapp.net',
            'phone' => '201005855853',
            'message' => '٩',
        ]);

        $conv = WhatsAppConversation::where('phone', '201005855853')->first();
        $outgoing = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('direction', 'outgoing')
            ->where('category', 'menu')
            ->first();

        $this->assertNotNull($outgoing);
        $this->assertStringContainsString('أهلاً بك في واتساب زمالة المدمنين المجهولين', $outgoing->body);
        $this->assertStringNotContainsString('9️⃣', $outgoing->body);

        // Sending convention keyword 'دعوة'
        $bot->handleIncomingMessage([
            'sender' => '201030076077@s.whatsapp.net',
            'phone' => '201030076077',
            'message' => 'دعوة',
        ]);

        $conv2 = WhatsAppConversation::where('phone', '201030076077')->first();
        $outgoing2 = WhatsAppMessage::where('conversation_id', $conv2->id)
            ->where('direction', 'outgoing')
            ->where('category', 'menu')
            ->first();

        $this->assertNotNull($outgoing2);
        $this->assertStringContainsString('أهلاً بك في واتساب زمالة المدمنين المجهولين', $outgoing2->body);
    }

    public function test_second_level_auto_reply_queries_meetings_without_sql_crash(): void
    {
        $city = City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $neigh = Neighborhood::create(['city_id' => $city->id, 'ar_name' => 'المعادي', 'en_name' => 'Maadi']);
        $serviceBody = new \App\Models\ServiceBody();
        $serviceBody->id = 1;
        $serviceBody->ar_name = 'لجنة الخدمة';
        $serviceBody->en_name = 'Service Body';
        $serviceBody->day_id = 1;
        $serviceBody->date = '2026-10-08';
        $serviceBody->start_time = '18:00:00';
        $serviceBody->end_time = '20:00:00';
        $serviceBody->location = 'القاهرة';
        $serviceBody->save();

        $user = User::factory()->create();

        $group = new Group();
        $group->user_id = $user->id;
        $group->neighborhood_id = $neigh->id;
        $group->service_body_id = $serviceBody->id;
        $group->ar_name = 'مجموعة الأمل';
        $group->en_name = 'Al-Amal Group';
        $group->ar_gsr_name = 'خادم';
        $group->en_gsr_name = 'GSR';
        $group->phone = '01012345678';
        $group->location = 'المعادي';
        $group->ar_address = 'شارع النصر';
        $group->en_address = 'El Nasr St';
        $group->save();

        $topic = \App\Models\Topic::create([
            'id' => 1,
            'ar_name' => 'موضوع',
            'en_name' => 'Topic',
        ]);

        Meeting::create([
            'group_id' => $group->id,
            'topic_id' => $topic->id,
            'day_id' => 4, // Tuesday
            'start_time' => '19:00:00',
            'end_time' => '20:30:00',
            'status' => 'active',
        ]);

        $bot = $this->app->make(WhatsAppBotService::class);

        // Step 1: User asks for meetings
        $bot->handleIncomingMessage([
            'sender' => '201077778888@s.whatsapp.net',
            'phone' => '201077778888',
            'message' => '2',
        ]);

        $conv = WhatsAppConversation::where('phone', '201077778888')->first();
        $this->assertEquals('awaiting_city', $conv->current_step);

        // Step 2: User sends Eastern Arabic numeral '١' (Cairo)
        $bot->handleIncomingMessage([
            'sender' => '201077778888@s.whatsapp.net',
            'phone' => '201077778888',
            'message' => '١',
        ]);

        $conv->refresh();
        $this->assertNull($conv->current_step);

        $outgoing = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('category', 'meetings')
            ->orderBy('id', 'desc')
            ->first();

        $this->assertNotNull($outgoing);
    }

    public function test_typing_0_during_city_selection_returns_to_main_menu_instead_of_live_agent(): void
    {
        $bot = $this->app->make(WhatsAppBotService::class);

        // Step 1: Prompt city
        $bot->handleIncomingMessage([
            'sender' => '201055556666@s.whatsapp.net',
            'phone' => '201055556666',
            'message' => '2',
        ]);

        $conv = WhatsAppConversation::where('phone', '201055556666')->first();
        $this->assertEquals('awaiting_city', $conv->current_step);

        // Step 2: User sends '0' to go back
        $bot->handleIncomingMessage([
            'sender' => '201055556666@s.whatsapp.net',
            'phone' => '201055556666',
            'message' => '0',
        ]);

        $conv->refresh();
        $this->assertNull($conv->current_step);
        // Live agent should NOT be active!
        $this->assertFalse($conv->is_live_agent_mode);

        $outgoing = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('category', 'menu')
            ->orderBy('id', 'desc')
            ->first();

        $this->assertNotNull($outgoing);
        $this->assertStringContainsString('زمالة المدمنين المجهولين', $outgoing->body);
    }

    public function test_admin_upload_convention_attendees_csv_updates_dataset(): void
    {
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LocalizationRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LocaleViewPath::class,
        ]);

        $roleSuper = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole($roleSuper);

        $csvContent = "phone,name,message\n201288889999,أحمد جديد,دعوتك الشخصية {name} كود: EVT-NEW999\n";
        $file = UploadedFile::fake()->createWithContent('attendees.csv', $csvContent);

        $response = $this->actingAs($admin)
            ->post(route('whatsapp.convention-attendees.upload'), [
                'csv_file' => $file,
                'title' => 'كشف حضور جديد',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $log = WhatsAppBroadcastLog::where('channel', 'convention_attendees')->first();
        $this->assertNotNull($log);
        $this->assertEquals(1, $log->total_recipients);
        $this->assertEquals('completed', $log->status);

        // Now test that sending 9 returns main menu since Option 9 is removed
        $bot = $this->app->make(WhatsAppBotService::class);
        $bot->handleIncomingMessage([
            'sender' => '201288889999@s.whatsapp.net',
            'phone' => '201288889999',
            'message' => '9',
        ]);

        $conv = WhatsAppConversation::where('phone', '201288889999')->first();
        $outgoing = WhatsAppMessage::where('conversation_id', $conv->id)
            ->where('category', 'menu')
            ->first();

        $this->assertNotNull($outgoing);
        $this->assertStringContainsString('أهلاً بك في واتساب زمالة المدمنين المجهولين', $outgoing->body);
        $this->assertStringNotContainsString('9️⃣', $outgoing->body);
    }
}
