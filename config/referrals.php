<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Referral Commission Rate
    |--------------------------------------------------------------------------
    |
    | Used when a referral partner does not have a custom commission rate.
    |
    */
    'default_rate' => 5.0,

    /*
    |--------------------------------------------------------------------------
    | Default Currency For Referral Partners
    |--------------------------------------------------------------------------
    |
    | Used to initialize the currency for new partners, and provides the list
    | of supported currencies within the referral dashboards.
    |
    */
    'default_currency' => 'JOD',

    'currencies' => [
        'AED' => [
            'label' => 'درهم إماراتي',
            'symbol' => 'د.إ',
        ],
        'JOD' => [
            'label' => 'دينار أردني',
            'symbol' => 'د.أ',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Referral Tracking Cookie
    |--------------------------------------------------------------------------
    |
    | Identifier and lifetime (in days) for the cookie that keeps track of
    | the referral partner while the visitor is browsing the site.
    |
    */
    'cookie_name' => 'wasfah_ref',
    'cookie_lifetime_days' => 30,
];
