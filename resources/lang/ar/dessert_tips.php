<?php

$base = require __DIR__ . '/baking_tips.php';

return array_replace_recursive($base, [
    'meta' => [
        'title' => 'نصائح عمل الحلويات - وصفة',
    ],
    'hero' => [
        'title' => 'نصائح عمل الحلويات',
        'subtitle' => 'نصائح وإرشادات من الخبراء لتحضير أجمل وألذ الحلويات.',
    ],
    'categories' => [
        [
            'title' => 'نصائح أساسية للحلويات',
        ],
        [
            'title' => 'درجة الحرارة للحلويات',
        ],
        [
            'title' => 'مكونات الحلويات',
        ],
    ],
    'tools' => [
        'title' => 'أدوات عمل الحلويات الأساسية',
    ],
]);
