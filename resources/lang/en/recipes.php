<?php

return [
    'meta' => [
        'title' => 'All Recipes - Wasfah',
    ],
    'hero' => [
        'title' => 'Every dessert recipe in one showcase',
        'badge' => [
            'default' => 'Wasfah Picks',
            'category' => 'Category :category',
            'search' => 'Search: ":term"',
        ],
        'subtitle' => [
            'default' => 'Discover curated gourmet desserts from the Wasfah team.',
            'category' => 'Everything about :category desserts in one place.',
            'search' => 'Showing results for ":term".',
        ],
        'meta_search' => 'Search: ":term"',
        'latest_unavailable' => 'Not available',
    ],
    'sort' => [
        'created_at' => 'Newest',
        'rating' => 'Top rated',
        'saved' => 'Most saved',
    ],
    'difficulty' => [
        'easy' => 'Easy',
        'medium' => 'Medium',
        'hard' => 'Hard',
    ],
    'stats' => [
        'total' => [
            'label' => 'Total recipes',
            'hint' => 'Across the Wasfah library',
        ],
        'current' => [
            'label' => 'Currently showing',
            'hint' => 'From :first to :last',
        ],
        'filters' => [
            'label' => 'Filter count',
            'hint' => [
                'active' => 'Custom settings',
                'default' => 'Default view',
            ],
        ],
        'latest' => [
            'label' => 'Latest addition',
            'hint' => 'Most recently published recipe',
        ],
    ],
    'filters' => [
        'search_label' => 'Search recipe',
        'search_placeholder' => 'Example: Seasonal fruit tart',
        'category_label' => 'Categories',
        'sort_label' => 'Sort results',
        'all_categories' => 'All categories',
        'submit' => 'Show results',
        'reset' => 'Reset',
        'active_label' => 'Active filters:',
        'chip_search' => 'Search: ":term"',
        'chip_category' => 'Category: :category',
        'chip_sort' => 'Sort: :label',
    ],
    'cards' => [
        'category_fallback' => 'Recipe',
        'fallback_excerpt' => 'Learn how to prepare this gourmet recipe with clear, friendly steps.',
        'prep_time' => ':minutes min',
        'servings' => '{1} Serves one person|[2,*] Serves :count people',
        'booking_closed' => 'Booking closed',
        'view_recipe' => 'View recipe',
        'image_fallback_alt' => 'Fallback image',
    ],
    'pagination' => [
        'summary' => ':first - :last of :total recipes',
    ],
    'empty' => [
        'title' => 'No matching results',
        'subtitle' => 'Try adjusting your search or picking a different category. We add new recipes regularly.',
        'cta' => 'Show all recipes',
    ],
];
