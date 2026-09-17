<?php

namespace Tests\Feature\Api;

use App\Models\HelplineCall;
use App\Models\HelplineVolunteer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HelplineCallApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_public_can_fetch_helpline_schema_and_options(): void
    {
        HelplineVolunteer::create([
            'name' => 'أحمد ع.',
            'phone' => '01012345678',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->getJson('/api/v1/helpline-calls/schema');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'title',
                'locale',
                'shifts',
                'caller_types',
                'referral_sources',
                'durations',
                'volunteers',
                'fields_order',
            ]);

        $this->assertContains('8:00 PM - 10:00 PM', $response->json('shifts'));
        $this->assertCount(1, $response->json('volunteers'));
        $this->assertEquals('أحمد ع.', $response->json('volunteers.0.name'));
    }

    public function test_public_can_submit_valid_helpline_call(): void
    {
        $volunteer = HelplineVolunteer::create([
            'name' => 'محمد م.',
            'phone' => '01099887766',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $payload = [
            'duration' => 'more_than_5',
            'call_date' => Carbon::today()->format('Y-m-d'),
            'call_time_shift' => '8:00 PM - 10:00 PM',
            'caller_type' => 'عضو حالي',
            'referral_source' => 'جدول الاجتماعات',
            'volunteer_name' => 'محمد م.',
            'is_step_12' => true,
            'call_brief' => 'طلب مساعدة هاتفية لخطوة 12 من عضو حالي.',
            'discuss_in_meeting' => false,
            'additional_info' => 'تمت إحالته لأقرب اجتماع حضوري.',
        ];

        $response = $this->postJson('/api/v1/helpline-calls', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.duration', 'more_than_5')
            ->assertJsonPath('data.call_time_shift', '8:00 PM - 10:00 PM')
            ->assertJsonPath('data.volunteer_id', $volunteer->id)
            ->assertJsonPath('data.is_step_12', true)
            ->assertJsonPath('data.discuss_in_meeting', false);

        $this->assertDatabaseHas('helpline_calls', [
            'call_time_shift' => '8:00 PM - 10:00 PM',
            'volunteer_id' => $volunteer->id,
            'is_step_12' => 1,
        ]);
    }

    public function test_submit_helpline_call_requires_mandatory_fields(): void
    {
        $response = $this->postJson('/api/v1/helpline-calls', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'duration',
                'call_date',
                'call_time_shift',
                'caller_type',
                'referral_source',
                'volunteer_name',
                'is_step_12',
                'call_brief',
                'discuss_in_meeting',
            ]);
    }

    public function test_submit_helpline_call_validates_conditional_other_fields(): void
    {
        $payload = [
            'duration' => 'less_than_5',
            'call_date' => Carbon::today()->format('Y-m-d'),
            'call_time_shift' => '10:00 AM - 12:00 PM',
            'caller_type' => 'أخرى',
            'caller_type_other' => '',
            'referral_source' => 'أخرى',
            'referral_source_other' => '',
            'volunteer_name' => 'أخرى',
            'volunteer_name_other' => '',
            'is_step_12' => false,
            'call_brief' => 'استفسار عام',
            'discuss_in_meeting' => false,
        ];

        $response = $this->postJson('/api/v1/helpline-calls', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'caller_type_other',
            ]);
    }

    public function test_submit_helpline_call_succeeds_with_other_fields_provided(): void
    {
        $payload = [
            'duration' => 'less_than_5',
            'call_date' => Carbon::today()->format('Y-m-d'),
            'call_time_shift' => '10:00 AM - 12:00 PM',
            'caller_type' => 'أخرى',
            'caller_type_other' => 'صحفي مهتم بالزمالة',
            'referral_source' => 'أخرى',
            'referral_source_other' => 'مقال في جريدة',
            'volunteer_name' => 'أخرى',
            'volunteer_name_other' => 'متطوع احتياطي جديد',
            'is_step_12' => false,
            'call_brief' => 'استفسار صحفي حول فعاليات اليوم العالمي للتعافي.',
            'discuss_in_meeting' => true,
        ];

        $response = $this->postJson('/api/v1/helpline-calls', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.caller_type_other', 'صحفي مهتم بالزمالة')
            ->assertJsonPath('data.referral_source_other', 'مقال في جريدة')
            ->assertJsonPath('data.volunteer_name_other', 'متطوع احتياطي جديد');

        $this->assertNull($response->json('data.volunteer_id'));

        $this->assertDatabaseHas('helpline_calls', [
            'caller_type_other' => 'صحفي مهتم بالزمالة',
            'referral_source_other' => 'مقال في جريدة',
            'volunteer_name_other' => 'متطوع احتياطي جديد',
            'volunteer_id' => null,
        ]);
    }

    public function test_unauthenticated_user_cannot_list_helpline_calls(): void
    {
        $response = $this->getJson('/api/v1/helpline-calls');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_list_and_filter_helpline_calls(): void
    {
        Sanctum::actingAs($this->user);

        HelplineCall::create([
            'duration' => 'less_than_5',
            'call_date' => '2026-09-01',
            'call_time_shift' => '10:00 AM - 12:00 PM',
            'caller_type' => 'عضو حالي',
            'referral_source' => 'الموقع الالكتروني',
            'volunteer_name' => 'متطوع 1',
            'is_step_12' => false,
            'call_brief' => 'مكالمة قديمة',
            'discuss_in_meeting' => false,
            'entry_time' => Carbon::parse('2026-09-01 11:00:00'),
        ]);

        HelplineCall::create([
            'duration' => 'more_than_5',
            'call_date' => '2026-09-15',
            'call_time_shift' => '8:00 PM - 10:00 PM',
            'caller_type' => 'أعضاء محتملة',
            'referral_source' => 'بحث جوجل',
            'volunteer_name' => 'متطوع 2',
            'is_step_12' => true,
            'call_brief' => 'مكالمة حديثة',
            'discuss_in_meeting' => true,
            'entry_time' => Carbon::parse('2026-09-15 20:30:00'),
        ]);

        $response = $this->getJson('/api/v1/helpline-calls');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'duration',
                        'duration_label',
                        'call_date',
                        'call_time_shift',
                        'caller_type',
                        'referral_source',
                        'volunteer_name',
                        'is_step_12',
                        'call_brief',
                        'discuss_in_meeting',
                        'entry_time',
                    ]
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(2, 'data');

        // Test filtering with start_date
        $filterResponse = $this->getJson('/api/v1/helpline-calls?start_date=2026-09-10');
        $filterResponse->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.call_brief', 'مكالمة حديثة');
    }
}
