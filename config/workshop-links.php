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
];
