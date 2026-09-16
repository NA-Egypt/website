# Cloudflare Turnstile Integration Setup

This document explains the Cloudflare Turnstile integration that has been implemented across all public forms (Helpline Form, Contact Us, Regional Delegate).

## Why Cloudflare Turnstile?

- **Zero Permissions-Policy Violations:** Modern Chromium browsers deprecate synchronous `unload` event handlers (`[Violation] Permissions policy violation: unload is not allowed in this document`). Turnstile uses modern web APIs (`pagehide` / Fetch) and generates zero console violations.
- **Privacy-First:** Turnstile verifies user authenticity without harvesting private tracking data or forcing users through frustrating image puzzles.
- **Lightweight & Native:** Uses Laravel's built-in `Http` client for server-side verification with zero bloated Composer dependencies.

## What's Implemented

1. **Environment Configuration:** `TURNSTILE_SITE_KEY` and `TURNSTILE_SECRET_KEY` configured in `.env` and `config/services.php`. Default testing keys (`1x00000000000000000000AA` / `1x0000000000000000000000000000000AA`) are provided for instantaneous local development and testing.
2. **Dedicated Validation Rule:** `App\Rules\Turnstile` verifies responses against `https://challenges.cloudflare.com/turnstile/v0/siteverify` using `Illuminate\Support\Facades\Http`.
3. **Frontend Integration:**
   - Helpline Call Registration Form (`resources/views/forms/helpline.blade.php`)
   - Contact Us Form (`resources/views/frontend/contactus.blade.php`)
   - Regional Delegate Form (`resources/views/frontend/rd.blade.php`)
4. **Bilingual Localization:** Arabic and English translation strings added in `resources/lang/{locale}/messages.php`.
5. **Comprehensive Feature Tests:** `tests/Feature/TurnstileValidationTest.php`.

## How to Configure Production Keys

1. Go to the [Cloudflare Dashboard](https://dash.cloudflare.com/) and navigate to **Turnstile**.
2. Add a new site with your domain (`naegypt.org` or `*naegypt.org`).
3. Obtain your **Site Key** and **Secret Key**.
4. Update your `.env` file:

```env
TURNSTILE_SITE_KEY=your_production_site_key
TURNSTILE_SECRET_KEY=your_production_secret_key
```

5. Clear the config cache:

```bash
php artisan config:clear
```
