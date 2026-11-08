<?php

namespace Tests\Feature;

use App\Http\Controllers\CommitteeReportsGuideController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommitteeReportsGuidePublicLinkTest extends TestCase
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

    public function test_get_public_url_helper_generates_correct_tokenized_url(): void
    {
        config(['services.reports_guide.token' => 'test-secret-token-123']);

        $url = CommitteeReportsGuideController::getPublicUrl();

        $this->assertStringContainsString('/reports-guide', $url);
        $this->assertStringContainsString('token=test-secret-token-123', $url);
    }

    public function test_guest_without_token_is_redirected_to_login(): void
    {
        $response = $this->get('/reports-guide');

        $response->assertRedirect(route('login.azure'));
        $response->assertSessionHas('error', __('messages.reports_guide_invalid_token'));
    }

    public function test_guest_with_invalid_token_is_redirected_to_login(): void
    {
        config(['services.reports_guide.token' => 'correct-secret-token']);

        $response = $this->get('/reports-guide?token=invalid-wrong-token');

        $response->assertRedirect(route('login.azure'));
        $response->assertSessionHas('error', __('messages.reports_guide_invalid_token'));
    }

    public function test_guest_with_valid_token_can_access_guide_publicly(): void
    {
        config(['services.reports_guide.token' => 'valid-secret-token']);

        $response = $this->get('/reports-guide?token=valid-secret-token');

        $response->assertStatus(200);
        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet, noimageindex');
        $response->assertSee(__('messages.reports_workgroups_guide'));
        // Guest should see portal login button and not internal buttons
        $response->assertSee(__('messages.portal_login'));
        $response->assertDontSee('copy-public-link-btn');
    }

    public function test_guest_with_valid_token_can_access_via_committee_reports_guide_url(): void
    {
        config(['services.reports_guide.token' => 'valid-secret-token']);

        $response = $this->get('/committee-reports-guide?token=valid-secret-token');

        $response->assertStatus(200);
        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet, noimageindex');
        $response->assertSee(__('messages.reports_workgroups_guide'));
        $response->assertSee(__('messages.portal_login'));
    }

    public function test_authenticated_user_can_access_guide_without_token(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/committee-reports-guide');

        $response->assertStatus(200);
        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet, noimageindex');
        $response->assertSee(__('messages.reports_workgroups_guide'));
        $response->assertSee(__('messages.copy_public_link'));
        $response->assertSee('copy-public-link-btn');
        $response->assertSee(CommitteeReportsGuideController::getPublicUrl());
    }

    public function test_authenticated_user_can_access_public_route_without_token(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/reports-guide');

        $response->assertStatus(200);
        $response->assertSee(__('messages.reports_workgroups_guide'));
        $response->assertSee(__('messages.copy_public_link'));
        $response->assertSee('copy-public-link-btn');
    }
}
