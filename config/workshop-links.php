<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Participant Join Link Lifetime (minutes)
    |--------------------------------------------------------------------------
    |
    | Determines how long participant join links remain valid. Links are
    | generated as signed URLs and will expire after the configured window.
    |
    */
    'participant_join_ttl' => env('WORKSHOP_LINK_PARTICIPANT_TTL', 360),

    /*
    |--------------------------------------------------------------------------
    | Participant Status Polling Link Lifetime (minutes)
    |--------------------------------------------------------------------------
    |
    | Determines how long signed polling endpoints stay valid while the
    | participant page refreshes the workshop status.
    |
    */
    'participant_status_ttl' => env('WORKSHOP_LINK_STATUS_TTL', 90),

    /*
    |--------------------------------------------------------------------------
    | Allow Same Email Multi-Device Access
    |--------------------------------------------------------------------------
    |
    | When enabled, participants who authenticate (or re-authenticate) with the
    | same email address that owns the booking can join from multiple devices
    | without being blocked by device fingerprint checks.
    |
    */
    'allow_same_email_multi_device' => env('WORKSHOP_ALLOW_SAME_EMAIL_MULTI_DEVICE', true),
];
