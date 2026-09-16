<?php

namespace Tests\Feature;

use App\Models\HelplineVolunteer;
use App\Rules\Turnstile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TurnstileValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
    }
    public function test_turnstile_rule_passes_when_cloudflare_verifies_success()
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'challenge_ts' => now()->toIso8601String(),
                'hostname' => 'naegypt.org',
            ], 200),
        ]);

        request()->headers->set('X-Test-Turnstile-Verification', '1');

        $validator = Validator::make([
            'cf-turnstile-response' => 'test-valid-turnstile-token',
        ], [
            'cf-turnstile-response' => ['required', new Turnstile],
        ]);

        $this->assertTrue($validator->passes());
    }

    public function test_turnstile_rule_fails_when_cloudflare_rejects_token()
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ], 200),
        ]);

        request()->headers->set('X-Test-Turnstile-Verification', '1');

        $validator = Validator::make([
            'cf-turnstile-response' => 'invalid-token',
        ], [
            'cf-turnstile-response' => ['required', new Turnstile],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('cf-turnstile-response', $validator->errors()->toArray());
    }

    public function test_helpline_form_renders_turnstile_widget_and_script()
    {
        $response = $this->get('/forms/helpline');
        $response->assertStatus(200);
        $response->assertHeader('Permissions-Policy', 'unload=*');
        $response->assertSee('cf-turnstile');
        $response->assertSee('challenges.cloudflare.com/turnstile/v0/api.js');
        $response->assertDontSee('google.com/recaptcha');
    }

    public function test_helpline_submission_with_turnstile_token()
    {
        $volunteer = HelplineVolunteer::firstOrCreate(
            ['name' => 'متطوع اختبار'],
            ['phone' => '01000000000', 'is_active' => true]
        );

        $payload = [
            'duration' => 'less_than_5',
            'call_date' => date('Y-m-d'),
            'call_time_shift' => '10:00 ص - 02:00 م',
            'caller_type' => 'مدمن يبحث عن مساعدة',
            'referral_source' => 'فيسبوك',
            'volunteer_name' => $volunteer->name,
            'is_step_12' => 0,
            'call_brief' => 'استفسار عن عناوين ومواعيد أقرب اجتماع',
            'discuss_in_meeting' => 0,
            'cf-turnstile-response' => 'dummy-turnstile-token',
        ];

        $response = $this->postJson('/forms/helpline', $payload);
        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
    }
}
