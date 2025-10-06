<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Redis Performance Configuration
    |--------------------------------------------------------------------------
    |
    | إعدادات Redis لتحسين الأداء
    |
    */

    'client' => env('REDIS_CLIENT', 'phpredis'),

    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', 'wasfah_database_'),
        'serializer' => env('REDIS_SERIALIZER', 'php'),
        'compression' => env('REDIS_COMPRESSION', 'gzip'),
        'compression_level' => env('REDIS_COMPRESSION_LEVEL', 6),
    ],

    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
        'timeout' => env('REDIS_TIMEOUT', 5),
        'retry_interval' => env('REDIS_RETRY_INTERVAL', 100),
        'read_timeout' => env('REDIS_READ_TIMEOUT', 5),
        'persistent' => env('REDIS_PERSISTENT', true),
        'pool' => [
            'min_connections' => env('REDIS_POOL_MIN', 1),
            'max_connections' => env('REDIS_POOL_MAX', 10),
        ],
    ],

    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
        'timeout' => env('REDIS_TIMEOUT', 5),
        'retry_interval' => env('REDIS_RETRY_INTERVAL', 100),
        'read_timeout' => env('REDIS_READ_TIMEOUT', 5),
        'persistent' => env('REDIS_PERSISTENT', true),
        'pool' => [
            'min_connections' => env('REDIS_POOL_MIN', 1),
            'max_connections' => env('REDIS_POOL_MAX', 10),
        ],
    ],

    'session' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_SESSION_DB', '2'),
        'timeout' => env('REDIS_TIMEOUT', 5),
        'retry_interval' => env('REDIS_RETRY_INTERVAL', 100),
        'read_timeout' => env('REDIS_READ_TIMEOUT', 5),
        'persistent' => env('REDIS_PERSISTENT', true),
        'pool' => [
            'min_connections' => env('REDIS_POOL_MIN', 1),
            'max_connections' => env('REDIS_POOL_MAX', 10),
        ],
    ],
];
