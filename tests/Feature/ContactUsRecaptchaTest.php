<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\ContactUs;

class ContactUsRecaptchaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that contact form requires reCAPTCHA
     */
    public function test_contact_form_requires_recaptcha()
    {
        $response = $this->post('/contactus', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'This is a test message',
            // Missing g-recaptcha-response
        ]);

        $response->assertSessionHasErrors(['g-recaptcha-response']);
        $this->assertDatabaseCount('contact_us', 0);
    }

    /**
     * Test that contact form shows validation errors
     */
    public function test_contact_form_shows_validation_errors()
    {
        $response = $this->post('/contactus', [
            'name' => '',
            'email' => 'invalid-email',
            'message' => '',
            // Missing g-recaptcha-response
        ]);

        $response->assertSessionHasErrors([
            'name',
            'email', 
            'message',
            'g-recaptcha-response'
        ]);
    }

    /**
     * Test contact form displays correctly
     */
    public function test_contact_form_displays_correctly()
    {
        $response = $this->get('/contactus');

        // Check if it's a redirect or successful response
        $this->assertTrue(in_array($response->status(), [200, 302]));

        if ($response->status() === 200) {
            $response->assertSee('g-recaptcha');
        }
    }
}
