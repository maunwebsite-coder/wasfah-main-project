<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Legal + policy settings
    |--------------------------------------------------------------------------
    |
    | Configure where the public Terms of Service and Privacy Policy live along
    | with the current policy version that users must accept.
    |
    */

    'terms_url' => env('LEGAL_TERMS_URL'),

    'privacy_url' => env('LEGAL_PRIVACY_URL'),

    'policies_version' => env('LEGAL_POLICIES_VERSION', '2025-11'),
];
