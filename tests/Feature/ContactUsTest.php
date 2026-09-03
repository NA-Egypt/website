<?php

namespace Tests\Feature;

use App\Models\ContactUs;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactUsTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
    }

    /**
     * Test contact form displays correctly using named route.
     */
    public function test_contact_page_renders_with_charity_and_form_details()
    {
        $response = $this->get(route('contactus.create'));
        $response->assertStatus(200);
        $response->assertSee('7786 / 2009-12-21');
        $response->assertSee('المقطم');
        $response->assertSee('name="name"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="phone"', false);
        $response->assertSee('name="subject"', false);
        $response->assertSee('name="message"', false);
    }

    /**
     * Test staging portal notice and absence of JSON-LD script on egyptna.org
     */
    public function test_contact_page_on_egyptna_domain()
    {
        $response = $this->get('https://egyptna.org/contactus');

        $response->assertStatus(200);
        $response->assertSee('egyptna.org is a staging portal for Narcotics Anonymous Egypt. The official public site is hosted at', false);
        $response->assertSee('naegypt.org');
        $response->assertDontSee('The website egyptna.org is an authorized, official staging and preview environment');
        $response->assertDontSee('"url": "https://naegypt.org/contactus"');
    }

    /**
     * Test authorized staging notice and presence of JSON-LD script on naegypt.org
     */
    public function test_contact_page_on_naegypt_domain()
    {
        $response = $this->get('https://naegypt.org/contactus');

        $response->assertStatus(200);
        $response->assertSeeText('The website egyptna.org is an authorized, official staging and preview environment operated by');
        $response->assertSee('"url": "https://naegypt.org/contactus"', false);
        $response->assertDontSee('egyptna.org is a staging portal for Narcotics Anonymous Egypt.');
    }

    /**
     * Test ContactUs API endpoints with Sanctum authentication.
     */
    public function test_contact_us_api_endpoints()
    {
        $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user);

        // Store
        $response = $this->postJson('/api/v1/contact-requests', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '01000000000',
            'subject' => 'General Inquiry',
            'message' => 'Hello, I have an inquiry.'
        ]);
        $response->assertStatus(201);
        $response->assertJsonPath('data.phone', '01000000000');
        $response->assertJsonPath('data.subject', 'General Inquiry');

        $contactId = $response->json('data.id');

        // Index
        $indexResponse = $this->getJson('/api/v1/contact-requests');
        $indexResponse->assertStatus(200);

        // Show
        $showResponse = $this->getJson("/api/v1/contact-requests/{$contactId}");
        $showResponse->assertStatus(200);
        $showResponse->assertJsonPath('data.name', 'John Doe');

        // Destroy
        $deleteResponse = $this->deleteJson("/api/v1/contact-requests/{$contactId}");
        $deleteResponse->assertStatus(204);
    }
}
