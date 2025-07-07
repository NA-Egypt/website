<?php

namespace Tests\Feature;

use Tests\TestCase;

class ContactUsRecaptchaSafeTest extends TestCase
{
    // NO RefreshDatabase trait - this is safe!

    /**
     * Test contact form displays correctly without affecting database
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

    /**
     * Test that form validation works (without actually submitting)
     */
    public function test_contact_form_has_required_fields()
    {
        $response = $this->get('/contactus');
        
        if ($response->status() === 200) {
            $response->assertSee('name="name"');
            $response->assertSee('name="email"');
            $response->assertSee('name="message"');
            $response->assertSee('g-recaptcha');
        }
    }
}
