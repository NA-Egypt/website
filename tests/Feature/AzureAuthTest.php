<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class AzureAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_to_azure_redirects()
    {
        $response = $this->get('/login/microsoft');
        $response->assertRedirect();
    }

    public function test_handle_callback_logs_in_user_and_redirects_to_dashboard()
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getEmail')->andReturn('officer@naegypt.org');
        $abstractUser->shouldReceive('getName')->andReturn('Officer User');

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('azure')->andReturn($provider);

        $response = $this->get('/login/microsoft/callback');

        $this->assertDatabaseHas('users', [
            'email' => 'officer@naegypt.org',
            'name' => 'Officer User',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_handle_callback_redirects_authenticated_user_without_re_querying()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login/microsoft/callback');

        $response->assertRedirect(route('dashboard'));
    }

    public function test_handle_callback_handles_duplicate_or_expired_code_gracefully()
    {
        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andThrow(new \Exception('AADSTS54005: OAuth2 Authorization code was already redeemed'));

        Socialite::shouldReceive('driver')->with('azure')->andReturn($provider);

        $response = $this->get('/login/microsoft/callback');

        $response->assertRedirect(route('frontend.home'));
        $response->assertSessionHas('error');
    }
}
