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
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        $response = $this->get(route('contactus.create'));
        $response->assertStatus(200);
        $response->assertSee('g-recaptcha');
    }

    /**
     * Test that form validation works (without actually submitting)
     */
    public function test_contact_form_has_required_fields()
    {
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        $response = $this->get(route('contactus.create'));
        $response->assertStatus(200);
        $response->assertSee('name="name"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="message"', false);
        $response->assertSee('g-recaptcha', false);
    }
}
