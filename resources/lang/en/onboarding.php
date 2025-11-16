<?php

return [
    'page_title' => 'Complete Your Chef Profile',
    'header' => [
        'title' => 'Welcome to the Wasfah chef community',
        'subtitle' => 'One last step to activate your professional account and showcase your expertise to cooking lovers and workshop attendees. The clearer your information, the higher your chances of being featured across Wasfah.',
        'chips' => [
            'instant' => 'Instant approval',
            'secure' => 'Your data stays protected',
            'spotlight' => 'Visibility to thousands of users',
        ],
        'badge_tip' => 'Activate your account immediately after submitting the form',
        'signed_in_as' => 'Signed in as :email',
    ],
    'steps' => [
        [
            'title' => 'Fill in your basics',
            'body' => 'Pick your country and add a valid mobile number.',
        ],
        [
            'title' => 'Add your digital presence',
            'body' => 'One working link is enough for us to learn more about you.',
        ],
        [
            'title' => 'Write a compelling bio',
            'body' => 'Describe your expertise and achievements to attract the right audience.',
        ],
    ],
    'checklist' => [
        'title' => 'Double-check before submitting',
        'items' => [
            'A verified mobile number that receives SMS or WhatsApp calls.',
            'At least one public link that we can open without logging in.',
            'A bio between 20 and 2,000 characters that explains your content.',
        ],
    ],
    'sections' => [
        'contact' => [
            'title' => 'Core contact information',
            'hint' => 'All booking updates will reach this number based on the selected country.',
            'country_label' => 'Country *',
            'country_placeholder' => 'Select your country',
            'phone_label' => 'Mobile number *',
            'phone_placeholder' => 'Example: 5XXXXXXXX',
            'phone_note' => 'We will use this number to confirm bookings and send alerts.',
            'google_email_label' => 'Google Meet email *',
            'google_email_hint' => 'Use the exact Google account that will host your workshops so Google Meet lets you in instantly.',
        ],
        'social' => [
            'title' => 'Social presence',
            'hint' => 'One active link is enough. If you do not have YouTube, just share Instagram and vice versa.',
            'instagram_label' => 'Instagram link (optional)',
            'youtube_label' => 'YouTube channel link (optional)',
            'required_error' => 'Please add at least one Instagram or YouTube link.',
            'public_notice' => 'Make sure the links are public so we can verify your content quickly.',
        ],
        'bio' => [
            'title' => 'Standout professional bio',
            'hint' => 'Share your experience, specialty, and achievements to impress Wasfah audiences.',
            'specialty_label' => 'Main specialty *',
            'specialty_placeholder' => 'Select your specialty',
            'specialty_food' => 'Food & culinary arts',
            'description_label' => 'Describe your culinary experience *',
            'description_placeholder' => 'Talk about the content you create, your signature recipes or workshops, and why your audience trusts you.',
        ],
    ],
    'alerts' => [
        'post_submit' => 'After submission your chef account will be activated instantly so you can publish recipes, launch workshops, and manage payments right away.',
    ],
    'submit' => [
        'cta' => 'Activate my chef account',
        'time_notice' => 'This form takes less than two minutes to complete.',
    ],
];
