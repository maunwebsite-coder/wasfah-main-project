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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'amazon' => [
        'affiliate_tag' => env('AMAZON_AFFILIATE_TAG', 'wasfah-21'),
        'marketplace' => env('AMAZON_MARKETPLACE', 'ae'),
    ],

    'jitsi' => [
        'provider' => env('JITSI_PROVIDER', 'meet'),
        'base_url' => env('JITSI_BASE_URL', 'https://meet.jit.si'),
        'room_prefix' => env('JITSI_ROOM_PREFIX', 'wasfah'),
        'default_duration' => (int) env('JITSI_DEFAULT_DURATION', 90),
        'allow_participant_subject_edit' => env('JITSI_ALLOW_PARTICIPANT_SUBJECT_EDIT', true),
        'jaas' => [
            'app_id' => env('JITSI_JAAS_APP_ID'),
            'api_key' => env('JITSI_JAAS_API_KEY'),
            'private_key_path' => env('JITSI_JAAS_PRIVATE_KEY_PATH'),
            'base_url' => env('JITSI_JAAS_BASE_URL', 'https://8x8.vc'),
            'token_ttl_minutes' => (int) env('JITSI_JAAS_TOKEN_TTL', 240),
        ],
    ],

    'registration' => [
        'require_email_verification' => env('REGISTRATION_REQUIRE_EMAIL_VERIFICATION'),
    ],

];
