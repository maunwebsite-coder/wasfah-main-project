<?php

return [
    'meta' => [
        'title' => 'جميع الوصفات - موقع وصفة',
    ],
    'hero' => [
        'title' => 'كل وصفات الحلويات في تجربة عرض واحدة',
        'badge' => [
            'default' => 'مختارات وصفة',
            'category' => 'تصنيف :category',
            'search' => 'بحث: ":term"',
        ],
        'subtitle' => [
            'default' => 'اكتشف وصفات الحلويات الراقية المختارة من فريق وصفة.',
            'category' => 'كل ما يتعلق بوصفات :category في مكان واحد.',
            'search' => 'عرض النتائج المطابقة لعبارة ":term".',
        ],
        'meta_search' => 'بحث: ":term"',
        'latest_unavailable' => 'غير متوفر',
    ],
    'sort' => [
        'created_at' => 'الأحدث',
        'rating' => 'الأعلى تقييماً',
        'saved' => 'الأكثر حفظاً',
    ],
    'difficulty' => [
        'easy' => 'سهل',
        'medium' => 'متوسط',
        'hard' => 'صعب',
    ],
    'stats' => [
        'total' => [
            'label' => 'إجمالي الوصفات',
            'hint' => 'في مكتبة وصفة',
        ],
        'current' => [
            'label' => 'المعروضة حالياً',
            'hint' => 'من :first إلى :last',
        ],
        'filters' => [
            'label' => 'عوامل التصفية',
            'hint' => [
                'active' => 'إعدادات مفعّلة',
                'default' => 'عرض افتراضي',
            ],
        ],
        'latest' => [
            'label' => 'أحدث إضافة',
            'hint' => 'لأقرب وصفة منشورة',
        ],
    ],
    'filters' => [
        'search_label' => 'بحث عن وصفة',
        'search_placeholder' => 'مثال: تارت الفواكه الموسمية',
        'category_label' => 'التصنيفات',
        'sort_label' => 'ترتيب النتائج',
        'all_categories' => 'كل التصنيفات',
        'submit' => 'عرض النتائج',
        'reset' => 'إعادة الضبط',
        'active_label' => 'عوامل فعّالة:',
        'chip_search' => 'بحث: ":term"',
        'chip_category' => 'تصنيف: :category',
        'chip_sort' => 'ترتيب: :label',
    ],
    'cards' => [
        'category_fallback' => 'وصفة',
        'fallback_excerpt' => 'تعرّف على خطوات إعداد هذه الوصفة الفاخرة بأسلوب مبسّط وواضح.',
        'prep_time' => ':minutes دقيقة',
        'servings' => '{1} تكفي شخصاً واحداً|[2,10] تكفي :count أشخاص|[11,*] تكفي :count شخصاً',
        'booking_closed' => 'انتهت مهلة الحجز',
        'view_recipe' => 'عرض الوصفة',
        'image_fallback_alt' => 'صورة بديلة',
    ],
    'pagination' => [
        'summary' => ':first - :last من :total وصفة',
    ],
    'empty' => [
        'title' => 'لم نعثر على نتائج مطابقة',
        'subtitle' => 'جرّب تعديل كلمات البحث أو اختيار تصنيف مختلف للحصول على المزيد من الوصفات الملهمة. يتم تحديث مكتبتنا باستمرار بوصفات جديدة.',
        'cta' => 'عرض كل الوصفات',
    ],
];
