<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Day;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GsrPrivacyProtectionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Group $group;
    private Meeting $meeting;
    private City $city;
    private ServiceBody $serviceBody;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        $this->user = User::factory()->create();

        $day = Day::create([
            'ar_name' => 'السبت',
            'en_name' => 'Saturday',
        ]);

        $this->city = City::create([
            'ar_name' => 'القاهرة',
            'en_name' => 'Cairo',
        ]);

        $neighborhood = Neighborhood::create([
            'ar_name' => 'المعادي',
            'en_name' => 'Maadi',
            'city_id' => $this->city->id,
        ]);

        $this->serviceBody = ServiceBody::create([
            'ar_name' => 'منطقة جنوب القاهرة',
            'en_name' => 'South Cairo ASC',
            'day_id' => $day->id,
            'start_time' => '17:00:00',
            'end_time' => '19:00:00',
            'location' => 'Cairo',
        ]);

        $topic = Topic::create([
            'ar_name' => 'موضوع عام',
            'en_name' => 'General Topic',
        ]);

        $this->group = Group::create([
            'ar_name' => 'مجموعة السكينة',
            'en_name' => 'Serenity Group',
            'ar_gsr_name' => 'طارق السري',
            'en_gsr_name' => 'Tarek Secret',
            'phone' => '+201099887766',
            'ar_address' => 'المعادي، القاهرة',
            'en_address' => 'Maadi, Cairo',
            'location' => 'https://maps.google.com/?q=29.96,31.25',
            'group_type' => 'فعلي',
            'service_body_id' => $this->serviceBody->id,
            'neighborhood_id' => $neighborhood->id,
            'user_id' => $this->user->id,
        ]);

        $this->meeting = Meeting::create([
            'day_id' => $day->id,
            'group_id' => $this->group->id,
            'topic_id' => $topic->id,
            'start_time' => '19:00:00',
            'end_time' => '20:30:00',
            'type' => 'open',
            'lang' => 'arabic',
            'status' => 'available',
        ]);
    }

    public function test_reveal_group_contact_endpoint_works_for_human_and_sets_noindex_noai_headers(): void
    {
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/125.0.0.0 Safari/537.36',
        ])->postJson(route('meetings.group.contact', ['group' => $this->group->id]));

        $response->assertStatus(200)
            ->assertJsonPath('phone', '+201099887766')
            ->assertJsonPath('ar_gsr_name', 'طارق السري')
            ->assertJsonPath('en_gsr_name', 'Tarek Secret');

        $robotsHeader = $response->headers->get('X-Robots-Tag');
        $this->assertNotNull($robotsHeader);
        $this->assertStringContainsString('noindex', $robotsHeader);
        $this->assertStringContainsString('nosnippet', $robotsHeader);
        $this->assertStringContainsString('noai', $robotsHeader);
    }

    public function test_reveal_group_contact_endpoint_blocks_search_engines_and_ai_crawlers(): void
    {
        $botAgents = [
            'Mozilla/5.0 (compatible; GPTBot/1.0; +https://openai.com/gptbot)',
            'Mozilla/5.0 (compatible; ClaudeBot/1.0; +claudebot@anthropic.com)',
            'Mozilla/5.0 (compatible; PerplexityBot/1.0; +https://perplexity.ai/perplexitybot)',
            'Mozilla/5.0 (compatible; Google-Extended/1.0)',
            'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
            'CCBot/2.0 (https://commoncrawl.org/faq/)',
            'python-requests/2.31.0',
            'curl/8.5.0',
        ];

        foreach ($botAgents as $idx => $ua) {
            $response = $this->withServerVariables([
                'REMOTE_ADDR' => '192.0.2.' . ($idx + 1),
            ])->withHeaders([
                'User-Agent' => $ua,
            ])->postJson(route('meetings.group.contact', ['group' => $this->group->id]));

            $response->assertStatus(403, "Expected User-Agent '{$ua}' to be blocked with 403.");
        }
    }

    public function test_reveal_group_contact_endpoint_enforces_rate_limiting(): void
    {
        $ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 Safari/605.1.15';

        for ($i = 0; $i < 5; $i++) {
            $this->withHeaders(['User-Agent' => $ua])
                ->postJson(route('meetings.group.contact', ['group' => $this->group->id]))
                ->assertStatus(200);
        }

        // 6th request within 1 minute must be throttled
        $this->withHeaders(['User-Agent' => $ua])
            ->postJson(route('meetings.group.contact', ['group' => $this->group->id]))
            ->assertStatus(429);
    }

    public function test_csv_export_omits_gsr_and_phone_for_guests_and_includes_for_authenticated_users(): void
    {
        app()->setLocale('en');

        // 1. Unauthenticated Guest CSV download
        $guestRes = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/125.0.0.0',
        ])->get('/export-meetings-csv?day=all');

        $guestRes->assertStatus(200);
        $this->assertStringContainsString('noai', (string) $guestRes->headers->get('X-Robots-Tag'));
        $guestCsv = $guestRes->streamedContent();
        $this->assertStringContainsString('Serenity Group', $guestCsv);
        $this->assertStringNotContainsString('Tarek Secret', $guestCsv);
        $this->assertStringNotContainsString('+201099887766', $guestCsv);
        $this->assertStringNotContainsString(',GSR,', $guestCsv);
        $this->assertStringNotContainsString(',Phone,', $guestCsv);

        // 2. Authenticated User CSV download
        $authRes = $this->actingAs($this->user)->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/125.0.0.0',
        ])->get('/export-meetings-csv?day=all');

        $authRes->assertStatus(200);
        $authCsv = $authRes->streamedContent();
        $this->assertStringContainsString('Serenity Group', $authCsv);
        $this->assertStringContainsString('Tarek Secret', $authCsv);
        $this->assertStringContainsString('+201099887766', $authCsv);

        // 3. AI Crawler blocked from CSV export
        $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (compatible; GPTBot/1.0)',
        ])->get('/export-meetings-csv?day=all')->assertStatus(403);
    }

    public function test_pdf_export_views_omit_gsr_and_phone_for_guests_and_include_for_authenticated_users(): void
    {
        $meetings = collect([$this->meeting->load(['group', 'day', 'topic', 'topics'])]);
        $groups = collect([$this->group]);
        $meetingsByDay = ['السبت' => $meetings];
        $fields = ['topic', 'time', 'type', 'lang'];
        $exportDate = '2026-10-28';
        $pageSize = 'A4';

        // 1. Guest rendering of pdf.meetings and exports.meetings-pdf
        auth()->logout();
        $guestMeetingsHtml = view('pdf.meetings', compact('meetings'))->render();
        $this->assertStringNotContainsString('طارق السري', $guestMeetingsHtml);
        $this->assertStringNotContainsString('+201099887766', $guestMeetingsHtml);

        $guestWizardHtml = view('exports.meetings-pdf', compact('meetingsByDay', 'groups', 'fields', 'exportDate', 'pageSize'))->render();
        $this->assertStringNotContainsString('طارق السري', $guestWizardHtml);
        $this->assertStringNotContainsString('+201099887766', $guestWizardHtml);
        $this->assertStringNotContainsString('ممثل المجموعة', $guestWizardHtml);

        // 2. Authenticated rendering of pdf.meetings and exports.meetings-pdf
        $this->actingAs($this->user);
        $authMeetingsHtml = view('pdf.meetings', compact('meetings'))->render();
        $this->assertStringContainsString('طارق السري', $authMeetingsHtml);
        $this->assertStringContainsString('+201099887766', $authMeetingsHtml);

        $authWizardHtml = view('exports.meetings-pdf', compact('meetingsByDay', 'groups', 'fields', 'exportDate', 'pageSize'))->render();
        $this->assertStringContainsString('طارق السري', $authWizardHtml);
        $this->assertStringContainsString('+201099887766', $authWizardHtml);
        $this->assertStringContainsString('ممثل المجموعة', $authWizardHtml);

        // 3. AI Crawler blocked from PDF export routes
        $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (compatible; ClaudeBot/1.0)',
        ])->get('/export-meetings-pdf')->assertStatus(403);

        $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (compatible; PerplexityBot/1.0)',
        ])->get(route('meetings.export.download', [
            'export_type' => 'cities',
            'cities' => [$this->city->id],
            'fields' => ['time'],
        ]))->assertStatus(403);
    }
}
