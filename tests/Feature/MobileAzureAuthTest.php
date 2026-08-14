<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class MobileAzureAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_for_mobile_stores_session_and_redirects()
    {
        $response = $this->get('/auth/azure/redirect?redirect_uri=customapp://callback');

        $response->assertSessionHas('mobile_redirect_uri', 'customapp://callback');
        $response->assertRedirect();
    }

    public function test_handle_callback_for_mobile_creates_user_and_redirects_with_token_and_user_data()
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getEmail')->andReturn('servant@egyptna.org');
        $abstractUser->shouldReceive('getName')->andReturn('Servant User');
        $abstractUser->shouldReceive('getUser')->andReturn([]);

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('azure')->andReturn($provider);

        $response = $this->withSession(['mobile_redirect_uri' => 'naegypt://auth-callback'])
            ->get('/auth/azure/callback');

        $this->assertDatabaseHas('users', [
            'email' => 'servant@egyptna.org',
            'name' => 'Servant User',
        ]);

        $user = User::where('email', 'servant@egyptna.org')->first();
        $this->assertNotNull($user);

        $response->assertRedirect();
        $targetUrl = $response->headers->get('Location');
        
        $this->assertStringStartsWith('naegypt://auth-callback?', $targetUrl);
        $this->assertStringContainsString('token=', $targetUrl);
        $this->assertStringContainsString('user=', $targetUrl);

        // Parse user param
        parse_str(parse_url($targetUrl, PHP_URL_QUERY), $queryParams);
        $this->assertArrayHasKey('token', $queryParams);
        $this->assertArrayHasKey('user', $queryParams);

        $userData = json_decode($queryParams['user'], true);
        $this->assertEquals($user->id, $userData['id']);
        $this->assertEquals('Servant User', $userData['name']);
        $this->assertEquals('servant@egyptna.org', $userData['email']);
    }
}
