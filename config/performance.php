<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | إعدادات تحسين الأداء للموقع
    |
    */

    // تحسينات قاعدة البيانات
    'database' => [
        'query_cache' => env('DB_QUERY_CACHE', true),
        'query_cache_ttl' => env('DB_QUERY_CACHE_TTL', 300), // 5 دقائق
        'connection_pooling' => env('DB_CONNECTION_POOLING', true),
        'max_connections' => env('DB_MAX_CONNECTIONS', 100),
    ],

    // تحسينات التخزين المؤقت
    'cache' => [
        'driver' => env('CACHE_DRIVER', 'redis'),
        'default_ttl' => env('CACHE_DEFAULT_TTL', 3600), // ساعة
        'session_ttl' => env('CACHE_SESSION_TTL', 1800), // 30 دقيقة
        'view_ttl' => env('CACHE_VIEW_TTL', 3600), // ساعة
    ],

    // تحسينات الجلسات
    'session' => [
        'driver' => env('SESSION_DRIVER', 'redis'),
        'lifetime' => env('SESSION_LIFETIME', 120), // دقيقتان
        'encrypt' => env('SESSION_ENCRYPT', true),
        'secure' => env('SESSION_SECURE', true),
        'http_only' => env('SESSION_HTTP_ONLY', true),
    ],

    // تحسينات الإشعارات
    'notifications' => [
        'batch_size' => env('NOTIFICATION_BATCH_SIZE', 50),
        'polling_interval' => env('NOTIFICATION_POLLING_INTERVAL', 30), // 30 ثانية بدلاً من 5
        'cache_enabled' => env('NOTIFICATION_CACHE_ENABLED', true),
        'cache_ttl' => env('NOTIFICATION_CACHE_TTL', 300), // 5 دقائق
    ],

    // تحسينات الذاكرة
    'memory' => [
        'limit' => env('MEMORY_LIMIT', '256M'),
        'gc_probability' => env('GC_PROBABILITY', 1),
        'gc_divisor' => env('GC_DIVISOR', 100),
    ],

    // تحسينات الملفات
    'files' => [
        'max_upload_size' => env('MAX_UPLOAD_SIZE', '32M'),
        'max_file_uploads' => env('MAX_FILE_UPLOADS', 20),
        'temp_cleanup' => env('TEMP_CLEANUP', true),
    ],
];

