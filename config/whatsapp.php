<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Microservice API Connection
    |--------------------------------------------------------------------------
    |
    | Base URL of the aldinokemal/go-whatsapp-web-multidevice container or service.
    |
    */
    'api_url' => env('WHATSAPP_API_URL', 'http://127.0.0.1:3000'),

    /*
    |--------------------------------------------------------------------------
    | API Key / Basic Auth Password
    |--------------------------------------------------------------------------
    |
    | Password/token configured in the Go WhatsApp service.
    |
    */
    'api_key' => env('WHATSAPP_API_KEY', 'secret_whatsapp_token_change_me'),

    /*
    |--------------------------------------------------------------------------
    | Device ID
    |--------------------------------------------------------------------------
    |
    | Identifier for the WhatsApp session / device in the multi-device microservice.
    |
    */
    'device_id' => env('WHATSAPP_DEVICE_ID', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Verification Secret
    |--------------------------------------------------------------------------
    |
    | Optional secret token sent in Webhook header or payload to verify incoming events.
    |
    */
    'webhook_secret' => env('WHATSAPP_WEBHOOK_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Webhook Callback URL
    |--------------------------------------------------------------------------
    |
    | The full HTTPS endpoint the microservice calls when incoming messages arrive.
    |
    */
    'webhook_url' => env('WHATSAPP_WEBHOOK_URL', 'https://naegypt.org/api/v1/whatsapp/webhook'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Request Timeout
    |--------------------------------------------------------------------------
    |
    | Timeout in seconds for API calls to the WhatsApp service.
    |
    */
    'timeout' => (int) env('WHATSAPP_TIMEOUT', 15),

    /*
    |--------------------------------------------------------------------------
    | Bot Automation Master Switch
    |--------------------------------------------------------------------------
    |
    | When false, incoming messages are still logged in the database, but no
    | automated responses will be dispatched.
    |
    */
    'bot_enabled' => (bool) env('WHATSAPP_BOT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Live Agent Takeover Timeout
    |--------------------------------------------------------------------------
    |
    | Number of minutes the automated bot remains paused after a human agent
    | responds or when the user requests a volunteer.
    |
    */
    'live_agent_timeout_minutes' => (int) env('WHATSAPP_LIVE_AGENT_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Anti-Ban Throttling Delays (Broadcasts)
    |--------------------------------------------------------------------------
    |
    | Delays in seconds between each broadcast message to prevent anti-spam flags.
    |
    */
    'broadcast_delay_min_seconds' => (int) env('WHATSAPP_BROADCAST_DELAY_MIN', 2),
    'broadcast_delay_max_seconds' => (int) env('WHATSAPP_BROADCAST_DELAY_MAX', 5),

    /*
    |--------------------------------------------------------------------------
    | Development & Staging Controls (egyptna.org)
    |--------------------------------------------------------------------------
    |
    | In development mode (or on egyptna.org), outbound messages are restricted
    | to explicit whitelisted test numbers to avoid sending test data to real users.
    |
    */
    'dev_mode' => (bool) env('WHATSAPP_DEV_MODE', false),
    'dev_whitelist' => array_filter(array_map('trim', explode(',', env('WHATSAPP_DEV_WHITELIST', '')))),

    /*
    |--------------------------------------------------------------------------
    | Convention Invitation Lookup (Option 9 / 3-Day Window)
    |--------------------------------------------------------------------------
    */
    'campaign_11_log_id' => (int) env('WHATSAPP_CAMPAIGN_11_LOG_ID', 11),
    'convention_lookup_expires_at' => env('WHATSAPP_CONVENTION_EXPIRES_AT', '2026-10-10 23:59:59'),
];
