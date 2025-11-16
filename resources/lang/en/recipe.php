<?php

return [
    'misc' => [
        'author_fallback' => 'Wasfah team',
        'image_alt' => 'Recipe photo',
        'gallery_image_alt' => 'Image :number',
        'placeholder_image_alt' => 'Placeholder image',
    ],

    'hero' => [
        'badge' => 'Wasfah signature recipe',
        'registration_closed' => 'The booking window for this recipe has ended.',
        'stats' => [
            'rating' => [
                'label' => 'Overall rating',
                'sub' => 'Community reviews',
                'empty' => 'No ratings yet',
            ],
            'saved' => [
                'label' => 'Members who saved it',
                'value' => '{0} No one saved this recipe yet|{1} :count member saved this recipe|[2,*] :count members saved this recipe',
            ],
            'time' => [
                'total' => 'Total time',
                'servings' => 'Servings',
                'details' => [
                    'prep_and_cook' => 'Prep :prep • Cook :cook',
                    'prep_only' => 'Prep :minutes',
                    'cook_only' => 'Cook :minutes',
                ],
            ],
            'servings' => [
                'value' => '{1} Serves :count person|[2,*] Serves :count people',
            ],
            'published_by' => 'Published by',
            'updated_at' => 'Last updated :date',
        ],
        'byline' => [
            'chef' => 'Chef :name',
            'team' => 'Wasfah culinary team',
        ],
        'actions' => [
            'save' => 'Save',
            'saved' => 'Saved',
            'rate' => 'Rate',
            'rated' => 'Rated',
        ],
        'buttons' => [
            'print' => 'Print',
            'share' => 'Share',
        ],
    ],

    'sections' => [
        'info' => 'Key information',
        'ingredients' => 'Ingredients',
        'tools' => 'Equipment used',
        'instructions' => 'Preparation steps',
        'community' => 'Community reactions',
        'share' => 'Share this recipe',
        'rating' => 'Rate this recipe',
        'related' => 'Similar recipes from the same category',
    ],

    'info' => [
        'prep' => 'Prep time',
        'cook' => 'Cook time',
        'servings' => 'Serves',
    ],

    'ingredients' => [
        'title' => 'Ingredients',
        'original_yield' => 'The original recipe (1x) yields :count servings',
        'tooltip' => 'This recipe was developed with its original yield. Ingredient quantities scale automatically, but timings and steps stay the same. Not every recipe scales perfectly.',
        'quantity_as_needed' => 'As needed',
    ],

    'tools' => [
        'title' => 'Equipment used',
        'price_label' => 'AED :price',
        'price_unknown' => 'Not provided',
        'empty' => 'No equipment was specified for this recipe.',
        'actions' => [
            'save' => 'Save for later',
            'saving' => 'Saving...',
            'saved' => 'Saved',
            'removing' => 'Removing...',
            'error' => 'Error saving',
            'view' => 'View on Amazon',
        ],
        'messages' => [
            'save_success' => 'Tool saved for later!',
            'save_error' => 'We couldn’t save this tool. Please try again.',
            'remove_success' => 'Tool removed from saved items!',
            'remove_error' => 'We couldn’t remove this tool. Please try again.',
        ],
    ],

    'instructions' => [
        'title' => 'Preparation steps',
    ],

    'community' => [
        'title' => 'Community reactions',
        'question' => 'Have you tried this recipe?',
        'count' => '{0} Be the first to try this recipe! 🚀|{1} :count member tried this recipe!|[2,*] :count members tried this recipe!',
        'button' => [
            'default' => 'I made it!',
            'active' => 'Made it',
        ],
    ],

    'share' => [
        'section_title' => 'Share this recipe',
        'modal' => [
            'title' => 'Share this recipe',
            'options_title' => 'Choose how you want to share:',
            'copy_link' => 'Copy link',
            'whatsapp' => 'WhatsApp',
            'telegram' => 'Telegram',
            'copied' => 'Link copied successfully!',
            'stats' => [
                'prep' => ':minutes prep',
                'servings' => ':count servings',
            ],
        ],
    ],

    'rating' => [
        'title' => 'Rate this recipe',
        'user_rating' => 'Your rating: :rating stars',
        'prompt' => 'Please rate the recipe',
        'button' => [
            'submit' => 'Submit rating',
            'remove' => 'Remove rating',
            'rate' => 'Rate',
            'rated' => 'Rated',
        ],
        'cta' => [
            'login_link' => 'Sign in to rate this recipe',
            'login_button' => 'Sign in to rate',
        ],
        'state' => [
            'submitting' => 'Submitting...',
            'submitted' => 'Submitted',
            'removing' => 'Removing...',
        ],
        'messages' => [
            'choose_rating' => 'Please select a rating before submitting.',
            'login_required' => 'You need to sign in to rate this recipe.',
            'login_required_remove' => 'You need to sign in to remove the rating.',
            'submit_success' => 'Rating submitted successfully!',
            'submit_error' => 'We couldn’t submit the rating. Please try again.',
            'remove_success' => 'Rating removed successfully!',
            'remove_error' => 'We couldn’t remove the rating. Please try again.',
        ],
        'empty_state' => 'Please rate the recipe',
        'star_title' => '{1} :count star|[2,*] :count stars',
        'modal' => [
            'title' => 'Cancel rating',
            'question' => 'Are you sure you want to remove the rating?',
            'hint' => 'Your save or “I made it” status will stay.',
            'cancel' => 'Cancel',
            'confirm' => 'Yes, remove rating',
        ],
    ],

    'related' => [
        'title' => 'Similar recipes from the same category',
        'no_ratings' => 'No ratings yet',
        'saved' => '{0} No saves yet|{1} :count save|[2,*] :count saves',
        'empty' => 'No similar recipes were found in this category.',
    ],

    'units' => [
        'minutes' => '{0} 0 minutes|{1} :count minute|[2,*] :count minutes',
        'minutes_short' => '{0} 0 min|{1} :count min|[2,*] :count min',
        'people' => '{0} 0 people|{1} :count person|[2,*] :count people',
        'servings' => '{0} Serves nobody|{1} Serves :count person|[2,*] Serves :count people',
    ],

    'actions' => [
        'print' => 'Print',
        'share' => 'Share',
        'view_recipe' => 'View recipe',
    ],
];
