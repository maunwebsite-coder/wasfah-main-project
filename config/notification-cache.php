<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Cache Configuration
    |--------------------------------------------------------------------------
    |
    | إعدادات التخزين المؤقت للإشعارات لتحسين الأداء
    |
    */

    // مدة التخزين المؤقت (بالثواني)
    'cache_ttl' => env('NOTIFICATION_CACHE_TTL', 300), // 5 دقائق

    // مدة التخزين المؤقت للعداد (بالثواني)
    'count_cache_ttl' => env('NOTIFICATION_COUNT_CACHE_TTL', 60), // دقيقة واحدة

    // مدة التخزين المؤقت للقائمة (بالثواني)
    'list_cache_ttl' => env('NOTIFICATION_LIST_CACHE_TTL', 180), // 3 دقائق

    // عدد الإشعارات المحفوظة في التخزين المؤقت
    'max_cached_notifications' => env('MAX_CACHED_NOTIFICATIONS', 50),

    // تفعيل التخزين المؤقت
    'enabled' => env('NOTIFICATION_CACHE_ENABLED', true),

    // تنظيف التخزين المؤقت التلقائي
    'auto_cleanup' => env('NOTIFICATION_AUTO_CLEANUP', true),

    // مدة تنظيف التخزين المؤقت (بالثواني)
    'cleanup_interval' => env('NOTIFICATION_CLEANUP_INTERVAL', 3600), // ساعة
];

