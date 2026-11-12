<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'azure' => [
        'client_id' => env('AZURE_CLIENT_ID'),
        'client_secret' => env('AZURE_CLIENT_SECRET'),
        'redirect' => env('AZURE_REDIRECT_URI'),
        'tenant' => env('AZURE_TENANT_ID'),
    ],

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY', '1x00000000000000000000AA'),
        'secret_key' => env('TURNSTILE_SECRET_KEY', '1x0000000000000000000000000000000AA'),
    ],

    'apk' => [
        'release_url' => env('APK_RELEASE_URL', 'https://github.com/NA-Egypt/na-mobile/releases/download/1.2.0/app-release.apk'),
        'filename' => env('APK_FILENAME', 'na-egypt-1.2.0.apk'),
        'token_expiry_hours' => (int) env('APK_TOKEN_EXPIRY_HOURS', 24),
    ],

    'reports_guide' => [
        'token' => env('REPORTS_GUIDE_ACCESS_TOKEN', 'naegypt-reports-guide-secret-key-2025'),
    ],

    'onedrive' => [
        'enabled' => env('ONEDRIVE_SYNC_ENABLED', true),
        'rclone_binary' => env('ONEDRIVE_RCLONE_BINARY', '/usr/bin/rclone'),
        'rclone_config' => env('ONEDRIVE_RCLONE_CONFIG', storage_path('app/private/rclone.conf')),
        'remote' => env('ONEDRIVE_RCLONE_REMOTE', 'onedrive'),
        'remote_path' => env('ONEDRIVE_REMOTE_PATH', ''),
        'sync_mode' => env('ONEDRIVE_SYNC_MODE', 'bisync'),
    ],
];
