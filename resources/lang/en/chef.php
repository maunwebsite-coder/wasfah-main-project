<?php

return [
    'title' => 'Chef dashboard',

    'meta' => [
        'title' => 'Chef :name profile',
    ],

    'defaults' => [
        'bio' => 'A creative chef sharing signature recipes with the Wasfah community.',
        'specialty_with_area' => 'Specialized in :area',
        'specialty_generic' => 'Member of the Wasfah community',
    ],

    'formats' => [
        'date_time' => 'j F Y • h:i a',
        'table_date_time' => 'j M Y · h:i a',
        'date' => 'j F Y',
    ],

    'status' => [
        'labels' => [
            'draft' => 'Draft',
            'pending' => 'Pending review',
            'approved' => 'Approved',
            'rejected' => 'Needs updates',
        ],
        'descriptions' => [
            'draft' => 'Keep editing privately before you submit the recipe.',
            'pending' => 'Our editorial team is reviewing the recipe details.',
            'approved' => 'Published on Wasfah and ready to share.',
            'rejected' => 'Review the comments, update the recipe, and resubmit.',
        ],
    ],

    'visibility' => [
        'public' => [
            'label' => 'Public',
            'hint' => 'Visible to everyone on Wasfah.',
        ],
        'private' => [
            'label' => 'Private link',
            'hint' => 'Only guests with the private link can view it.',
        ],
    ],

    'hero' => [
        'badge' => 'Chef tools',
        'heading' => 'Chef :name',
        'description' => 'Manage your recipes, workshops, and payouts in one dashboard.',
        'avatar_alt' => 'Photo of chef :name',
        'stats' => [
            'wasfah_followers' => 'Wasfah subscribers',
            'other_platform_followers' => 'Other platform followers',
            'recipes' => 'Published recipes',
            'average_rating' => 'Average rating',
        ],
        'buttons' => [
            'follow' => 'Follow',
            'following' => 'Following',
        ],
        'actions' => [
            'public_profile' => 'View public profile',
            'workshops' => 'Manage workshops',
            'earnings' => 'View earnings',
            'new_recipe' => 'New recipe',
        ],
    ],

    'impact' => [
        'badge' => 'Community pulse',
        'title' => 'Impact snapshot',
        'description' => ':name\'s recipes inspire Wasfah members. Here is how the community responds.',
        'highlight_label' => 'Published recipes',
        'highlight_hint' => 'Unique dishes shared on Wasfah',
        'cards' => [
            'saves' => [
                'title' => 'Saved recipes',
                'hint' => 'Times members bookmarked these dishes.',
            ],
            'made' => [
                'title' => 'Tried it',
                'hint' => 'Members who marked the recipe as made.',
            ],
            'reviews' => [
                'title' => 'Ratings & reviews',
                'hint' => 'Total feedback shared by the community.',
            ],
        ],
    ],

    'dashboard' => [
        'workshops' => [
            'meta_title' => 'Chef online workshops',
            'hero' => [
                'eyebrow' => 'Chef zone',
                'title' => 'My workshops',
                'description' => 'Create online sessions easily and share the Jitsi link once people book.',
            ],
            'ctas' => [
                'new' => 'Add new workshop',
                'earnings' => 'Workshop earnings',
                'recipes' => 'Back to recipes',
            ],
            'device_reset' => [
                'badge' => 'Trusted device verification',
                'title' => 'Reopen workshop room: :title',
                'description' => 'To protect attendees, only one trusted device can open the workshop room. Enter your password to assign this browser.',
                'password_label' => 'Password',
                'password_placeholder' => 'Enter current password',
                'confirm' => 'Confirm reset',
                'retry' => 'Retry after reset',
                'footnote' => 'Your current device becomes the trusted host and other devices are blocked until you reset again.',
                'reasons' => [
                    'missing_cookie' => 'The trusted device profile was not found in this browser. Cookies may have been cleared or a new device is being used.',
                    'cookie_mismatch' => 'The current device code does not match the previously trusted one.',
                    'fingerprint_mismatch' => 'We detected a different device or browser than the trusted one.',
                    'manual_reset_validation_failed' => 'Password verification failed. Please try again so we can confirm it is you.',
                ],
            ],
            'stats' => [
                'total' => 'Total workshops',
                'active' => 'Active workshops',
                'online' => 'Online workshops',
                'drafts' => 'Drafts',
            ],
            'badges' => [
                'delivery' => [
                    'online' => 'Online',
                    'in_person' => 'In person',
                ],
                'status' => [
                    'published' => 'Published',
                    'draft' => 'Draft',
                ],
            ],
            'duration' => '• Lasts :minutes minutes',
            'capacity' => ':current / :max participants',
            'jitsi_card' => [
                'title' => 'Online session via Jitsi',
                'description' => 'The link stays hidden for everyone. Only the button below opens the room.',
                'launch' => 'Start session',
                'launched' => 'Room opened :time',
                'not_ready' => 'Participants cannot join until you click "Start meeting" on the launch page.',
                'pending_link' => 'The button will be enabled after the link is generated.',
                'passcode' => 'Participant passcode: :code',
            ],
            'actions' => [
                'edit' => 'Edit',
                'delete' => 'Delete',
                'delete_confirm' => 'Are you sure you want to delete this workshop?',
                'view_public' => 'View workshop page',
                'pending_public' => 'The link will appear once the workshop is active.',
            ],
            'empty' => [
                'title' => 'No workshops yet',
                'description' => 'Create your first workshop now and we will generate a Jitsi link instantly.',
                'cta' => 'Create workshop',
            ],
        ],
    ],

    'workshops' => [
        'eyebrow' => 'Workshops hub',
        'title' => 'Chef workshops',
        'description' => 'Discover the learning experiences led by :name and explore upcoming and past workshops.',
        'view_all' => 'Browse all workshops',
        'upcoming' => 'Upcoming workshops',
        'past' => 'Past workshops',
        'latest_workshop' => 'Latest workshop: :title',
        'placeholder_text' => 'Workshop',
        'tbd_time' => 'To be announced',
        'unscheduled_time' => 'Unscheduled date',
        'location_tbd' => 'Location to be announced',
        'online_live' => 'Live online',
        'delivery' => [
            'online_short' => 'Online',
            'in_person_short' => 'In-person',
        ],
        'levels' => [
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
        ],
        'level_label' => 'Level :level',
        'capacity_with_limit' => ':current / :max attendees',
        'capacity_open' => ':count attendees',
        'date_format' => 'j F Y',
        'datetime_format' => 'j F Y • h:i a',
        'badges' => [
            'open' => 'Available to book',
            'closed' => 'Registration closed',
            'completed' => 'Completed',
        ],
        'image_alt' => 'Workshop :title',
        'with_instructor' => 'With :name',
        'delivered_by' => 'Delivered by :name',
        'register_until' => 'Registration open until :date',
        'book_now' => 'Book your seat',
        'registration_closed' => 'Registration closed',
        'view_details' => 'View details',
        'stats' => [
            'total' => [
                'title' => 'Total workshops',
                'hint' => 'All sessions you have published.',
            ],
            'active' => [
                'title' => 'Active sessions',
                'hint' => 'Live now or open for bookings.',
            ],
            'online' => [
                'title' => 'Online workshops',
                'hint' => 'Hosted virtually through Wasfah.',
            ],
            'upcoming' => [
                'title' => 'Upcoming',
                'hint' => 'Confirmed sessions scheduled next.',
            ],
        ],
        'buttons' => [
            'create' => 'Create workshop',
            'manage' => 'Manage workshops',
        ],
        'next' => [
            'heading' => 'Next workshops',
            'limit' => 'Showing up to :count workshops',
            'quick_join' => 'Quick join',
            'fallback_time' => 'Time to be scheduled',
            'open_room' => 'Open live room',
        ],
        'labels' => [
            'online' => 'Online',
            'onsite' => 'Onsite',
        ],
        'host_status' => [
            'live' => 'Live now',
            'online_upcoming' => 'Virtual room ready',
            'onsite' => 'Onsite session',
            'live_since' => 'Live for :time',
        ],
        'sections' => [
            'host' => 'Host status',
            'participants' => 'Participants',
        ],
        'participant_status' => [
            'online_live' => 'Participants live',
            'online_waiting' => 'Participants waiting',
            'onsite' => 'Onsite attendees',
        ],
        'participant_hints' => [
            'online_live' => 'Participants are in the room now; keep the link handy.',
            'online_waiting' => 'Participants can join once you open the room.',
            'onsite' => 'Share arrival instructions and venue updates.',
        ],
        'items' => [
            'participants_count' => '{0} No participants yet|{1} :count participant|[2,*] :count participants',
            'capacity_count' => '{1} :count seat total|[2,*] :count seats total',
            'capacity_unlimited' => 'Unlimited capacity',
            'participant_link' => 'Open meeting link',
            'participant_link_restricted' => 'Link hidden for privacy',
            'location_value' => 'Location: :location',
            'location_missing' => 'Location shared after confirmation.',
            'edit_details' => 'Edit workshop details',
            'no_upcoming' => 'No upcoming workshops yet.',
            'plan_next' => 'Plan the next workshop',
        ],
    ],

    'workshops_create' => [
        'page_title' => 'Launch a new workshop',
        'hero_badge' => 'Chef area',
        'hero_heading' => 'Launch a new online workshop',
        'hero_description' => 'Once you save, we will prepare a shareable Jitsi link for your attendees.',
        'back_to_list' => 'Back to workshops list',
        'validation_heading' => 'Please review the following fields:',
        'draft_notice' => 'The workshop will be saved under your account so you can edit or publish it whenever you are ready.',
        'submit_label' => 'Save workshop',
    ],

    'workshop_form' => [
        'currencies' => [
            'jod' => 'Jordanian dinar',
        ],
        'sections' => [
            'basics' => [
                'eyebrow' => 'Basic information',
                'title' => 'Workshop details',
                'description' => 'Introduce participants to the workshop topic and why it stands out.',
            ],
            'pricing' => [
                'eyebrow' => 'Pricing & capacity',
                'title' => 'Financial settings',
                'description' => 'Let participants know how many seats are available to encourage early registrations.',
            ],
            'schedule' => [
                'eyebrow' => 'Scheduling',
                'title' => 'Dates & times',
                'description' => 'These dates appear to learners as soon as the workshop is published.',
            ],
            'delivery' => [
                'eyebrow' => 'Delivery method',
                'title' => 'Online via Jitsi',
                'description' => 'We can automatically generate a secured Jitsi meeting link for you.',
                'highlight' => 'We provide automatic Jitsi meeting links for you.',
            ],
            'host' => [
                'eyebrow' => 'Your info',
                'title' => 'Introduce the instructor',
            ],
            'image' => [
                'eyebrow' => 'Workshop image',
                'title' => 'Visual impact',
                'description' => 'Upload sharp imagery (up to 5 MB) to encourage bookings.',
                'preview_placeholder' => 'The preview will appear here after you upload an image.',
                'preview_alt' => 'Workshop cover preview',
            ],
            'publish' => [
                'eyebrow' => 'Publish workshop',
                'title' => 'Ready to go live?',
                'auto_activate' => 'Activate the workshop immediately after saving',
            ],
        ],
        'fields' => [
            'title' => [
                'label' => 'Workshop title *',
            ],
            'category' => [
                'label' => 'Workshop category *',
            ],
            'level' => [
                'label' => 'Level *',
            ],
            'duration' => [
                'label' => 'Duration (minutes) *',
            ],
            'description' => [
                'label' => 'Inspiring description *',
            ],
            'content' => [
                'label' => 'Content outline (optional)',
            ],
            'learning_points' => [
                'label' => 'What will participants learn?',
            ],
            'requirements' => [
                'label' => 'Prerequisites',
            ],
            'price' => [
                'label' => 'Workshop price *',
            ],
            'currency' => [
                'label' => 'Currency *',
            ],
            'max_participants' => [
                'label' => 'Maximum participants *',
            ],
            'start_date' => [
                'label' => 'Start date *',
            ],
            'end_date' => [
                'label' => 'End date *',
            ],
            'registration_deadline' => [
                'label' => 'Registration deadline',
            ],
            'meeting_link' => [
                'label' => 'Meeting link',
            ],
            'location' => [
                'label' => 'Location *',
            ],
            'address' => [
                'label' => 'Full address',
            ],
            'instructor' => [
                'label' => 'Instructor name',
            ],
            'instructor_bio' => [
                'label' => 'Short bio',
            ],
            'image' => [
                'label' => 'Upload cover image',
            ],
            'passcode' => [
                'label' => 'Passcode:',
            ],
        ],
        'placeholders' => [
            'category' => 'e.g., Baking, main dishes, desserts...',
            'description' => 'Share the goals, your teaching style, and the value participants will take away.',
        ],
        'options' => [
            'online_label' => 'This workshop is online',
            'online_hint' => 'You can switch it to an in-person session anytime.',
            'auto_generate_label' => 'Auto-generate a Jitsi link on save',
        ],
        'buttons' => [
            'generate_link' => 'Generate link now',
            'remove_image' => 'Remove current image',
        ],
        'messages' => [
            'pricing_notice_title' => 'Important:',
            'pricing_notice_body' => 'Wasfah retains a :fee_range service fee to cover payment gateways, technical support, and marketing.',
            'pricing_notice_followup' => 'After this fee is deducted, the net payout is transferred to you within 7 business days of the workshop.',
            'meeting_hint_auto' => 'The link will be assigned automatically after saving.',
            'meeting_hint_manual' => 'You can paste an existing meeting link if you prefer.',
            'jitsi_ready' => 'A Jitsi link is ready:',
            'managed_link_title' => 'Link managed by the Wasfah team',
            'managed_link_description' => 'We will generate and secure the meeting link automatically after saving, and the raw link stays hidden for privacy.',
            'image_max_size' => 'You cannot upload an image larger than 5 MB.',
        ],
        'js' => [
            'title_required' => 'Please enter the workshop title first.',
            'generate_failed' => 'Unable to generate the link right now.',
            'generic_error' => 'An unexpected error occurred.',
        ],
    ],

    'workshops_earnings' => [
        'title' => 'Workshop earnings',
        'hero' => [
            'eyebrow' => 'Chef area',
            'heading' => 'Workshop earnings',
            'description' => 'Review the financial performance of your online workshops and track the expected net payout after platform fees.',
            'cta' => [
                'new' => 'Launch new workshop',
                'back' => 'Back to workshops list',
            ],
        ],
        'notice' => [
            'eyebrow' => 'Important note',
            'title' => 'Wasfah retains a 25%–30% platform commission',
            'description' => 'Whenever you launch a new workshop we withhold 25% to 30% to cover payments, technical support, and marketing operations. The remaining balance is transferred within a week after the workshop ends once payouts are settled.',
        ],
        'stats' => [
            'gross' => [
                'label' => 'Total collected amount',
                'hint' => 'From all paid bookings',
            ],
            'net' => [
                'label' => 'Estimated net after platform fee',
                'hint' => 'Depends on the commission range (25%–30%)',
                'range_to' => 'to',
            ],
            'paid_seats' => [
                'label' => 'Paid seats',
                'hint' => 'Includes all completed workshops',
            ],
            'average' => [
                'label' => 'Average revenue per attendee',
                'hint' => 'Helps you price upcoming workshops',
            ],
        ],
        'monthly' => [
            'current' => [
                'label' => 'This month\'s earnings',
                'delta' => ':amount vs last month',
                'hint' => 'Updates as soon as participants\' payments are confirmed',
            ],
            'previous' => [
                'label' => 'Last month\'s earnings',
                'hint' => 'For historical comparison only',
            ],
        ],
        'leaderboard' => [
            'eyebrow' => 'Top-performing workshops',
            'title' => 'Workshop-by-workshop breakdown',
            'description' => 'Approximate net amounts after the platform fee.',
            'button' => 'Manage workshops',
            'table' => [
                'workshop' => 'Workshop',
                'start_date' => 'Start date',
                'paid' => 'Paid participants',
                'gross' => 'Total paid',
                'net' => 'Estimated net',
            ],
            'capacity' => 'Capacity :capacity',
            'capacity_unknown' => 'Not set yet',
            'date_pending' => 'To be scheduled',
            'empty' => 'No financial data yet. Create a workshop and confirm paid bookings.',
        ],
    ],

    'recipes_create' => [
        'page_title' => 'Add New Recipe - Chef Area',
        'hero_badge' => 'Chef area',
        'hero_heading' => 'Add a new recipe',
        'hero_description' => 'Fill in the details below to submit your recipe for review.',
        'back_to_index' => 'Back to recipes dashboard',
        'validation_heading' => 'Please review the following fields:',
        'actions' => [
            'save_draft' => 'Save as draft',
            'submit_review' => 'Submit for review',
        ],
    ],

    'recipe_form' => [
        'sections' => [
            'basics' => [
                'title' => 'Recipe essentials',
                'description' => 'Provide the information our editors need to approve your recipe quickly.',
            ],
            'steps' => [
                'title' => 'Preparation steps',
                'description' => 'Add clear, sequential steps so the recipe is easy to follow.',
                'add_button' => 'Add step',
                'placeholder' => 'Step description',
            ],
            'ingredients' => [
                'title' => 'Ingredients',
                'description' => 'List each ingredient with its exact quantity.',
                'add_button' => 'Add ingredient',
                'name_label' => 'Ingredient name',
                'name_placeholder' => 'Example: All-purpose flour',
                'amount_label' => 'Quantity',
                'amount_placeholder' => 'Example: 2 cups',
                'remove_button' => 'Remove',
            ],
            'tools' => [
                'title' => 'Suggested tools (optional)',
            ],
            'media' => [
                'title' => 'Recipe photos',
                'description' => 'Upload up to 5 high-quality images to showcase your dish.',
                'primary' => 'Main image',
                'additional' => 'Additional image :number',
                'max_size_message' => 'Images must be 5 MB or less.',
                'max_size_hint' => 'Maximum file size per image is 5 MB.',
                'current_alt' => 'Recipe image',
                'remove_current' => 'Remove this image',
            ],
            'external_image' => [
                'title' => 'External image link (optional)',
                'description' => 'Paste a direct image link (Google Drive, Unsplash, etc.) if you have one.',
                'placeholder' => 'https://example.com/your-image.jpg',
            ],
        ],
        'fields' => [
            'title' => [
                'label' => 'Recipe title *',
            ],
            'description' => [
                'label' => 'Short recipe description',
                'placeholder' => 'Share the story or any serving tips.',
            ],
            'category' => [
                'label' => 'Category',
                'placeholder' => 'Select a category',
            ],
            'difficulty' => [
                'label' => 'Difficulty level',
                'placeholder' => 'Select a level',
                'options' => [
                    'easy' => 'Easy',
                    'medium' => 'Intermediate',
                    'hard' => 'Advanced',
                ],
            ],
            'visibility' => [
                'label' => 'Visibility',
                'options' => [
                    'public' => [
                        'label' => 'Public',
                        'description' => 'Visible to everyone once approved.',
                    ],
                    'private' => [
                        'label' => 'Private',
                        'description' => 'Stays hidden even after approval.',
                    ],
                ],
            ],
            'prep_time' => [
                'label' => 'Prep time (minutes)',
            ],
            'cook_time' => [
                'label' => 'Cook time (minutes)',
            ],
            'servings' => [
                'label' => 'Servings',
            ],
        ],
    ],

    'recipes' => [
        'tabs' => [
            'public' => 'Public recipes',
            'exclusive' => 'Exclusive recipes',
        ],
        'public_empty' => 'No public recipes yet. Stay tuned for the chef’s upcoming creations!',
        'exclusive_empty' => 'No exclusive recipes yet. Share your premium creations here.',
        'category_fallback' => 'Recipe',
        'no_rating' => 'No rating yet',
        'saves' => ':count saves',
        'likes' => ':count likes',
        'private_tag' => 'Exclusive recipe',
        'private_details' => 'Detailed step-by-step guidance',
        'private_access' => 'Exclusive view',
        'view_recipe' => 'View recipe',
    ],

    'link_page' => [
        'title' => 'Wasfah link page',
        'description' => 'Share one branded URL that gathers your recipes, workshops, and booking links.',
        'features' => [
            'customize' => 'Custom branding',
            'instant_updates' => 'Instant updates',
            'shareable' => 'Share anywhere',
        ],
        'actions' => [
            'manage' => 'Customize page',
            'view' => 'View public link',
        ],
    ],

    'empty_state' => [
        'title' => 'No recipes yet',
        'description' => 'Create your first recipe to populate this list.',
        'cta' => 'Create a recipe',
    ],

    'table' => [
        'headers' => [
            'recipe' => 'Recipe',
            'status' => 'Status',
            'visibility' => 'Visibility',
            'updated_at' => 'Last updated',
            'category' => 'Category',
            'actions' => 'Actions',
        ],
        'approved_on' => 'Approved on :date',
        'no_category' => 'Uncategorized',
        'actions' => [
            'edit' => 'Edit',
            'submit_for_review' => 'Submit for review',
            'delete_confirm' => 'Are you sure you want to delete this recipe? This action cannot be undone.',
            'delete' => 'Delete',
        ],
    ],

    'popular' => [
        'title' => 'Most-viewed chef recipes',
        'subtitle' => 'Explore the standout dishes winning over food lovers and followers.',
        'view_details' => 'Discover the details',
    ],
];
