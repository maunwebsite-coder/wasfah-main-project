<?php

return [
    'meta_title' => 'Search Results - Wasfah',
    'badge' => 'Search Results',
    'heading' => [
        'results_prefix' => 'Results for',
        'results_description' => 'You can tweak the keyword or switch the result type to get the best matches.',
        'empty_title' => 'Find an inspiring recipe or workshop',
        'empty_description' => 'Type a keyword to discover recipes, tools, or workshops that spark your dessert creativity.',
    ],
    'form' => [
        'placeholder' => 'Search for a recipe, tool, or workshop...',
    ],
    'types' => [
        'all' => 'All',
        'recipes' => 'Recipes',
        'workshops' => 'Workshops',
    ],
    'stats' => [
        'total' => [
            'label' => 'Total results',
            'subtitle' => 'Current total results',
        ],
        'recipes' => [
            'label' => 'Recipes',
            'subtitle' => 'Tasty recipes we found',
        ],
        'workshops' => [
            'label' => 'Workshops',
            'subtitle' => 'Training opportunities available',
        ],
    ],
    'recipes' => [
        'title' => 'Recipes',
        'summary' => [
            'found' => ':count matching recipes found',
            'empty' => 'No recipes match right now',
        ],
        'view_all' => 'Explore all recipes',
        'prep_time' => ':minutes min',
        'flex_time' => 'Flexible time',
        'category_fallback' => 'Recipe',
        'author_fallback' => 'Recipe',
        'saved' => ':count saves',
        'view_recipe' => 'View recipe',
        'empty_state' => 'We could not find matching recipes. Try different keywords or pick another type.',
    ],
    'workshops' => [
        'title' => 'Workshops',
        'summary' => [
            'found' => ':count workshops available',
            'empty' => 'No workshops match right now',
        ],
        'view_all' => 'Browse all workshops',
        'featured' => 'Featured',
        'date_flexible' => 'Flexible date',
        'online' => 'Available online',
        'location_pending' => 'To be announced',
        'view_workshop' => 'View workshop details',
        'empty_state' => 'We could not find matching workshops. Try other keywords or explore the available sessions.',
    ],
    'inactive' => [
        'title' => 'Start searching now',
        'description' => 'Type the name of a recipe, tool, ingredient, or workshop above to get tailored results, or browse the main categories in the navigation.',
        'chips' => [
            'Brownies',
            'Chocolate cake',
            'Beginner workshops',
            'Baking tools',
        ],
    ],
    'no_results' => [
        'title' => 'We could not find any results for this search',
        'description' => 'Try different synonyms, reduce the number of words, or browse the main sections.',
        'home' => 'Back to home',
        'workshops' => 'Explore workshops',
    ],
];

