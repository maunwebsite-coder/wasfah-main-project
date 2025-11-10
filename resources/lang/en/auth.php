<?php

return [
    'title' => 'Wasfah | Sign in or create an account',
    'logo_alt' => 'Wasfah logo',
    'alerts' => [
        'review_fields' => 'Please review the highlighted fields.',
    ],
    'intro' => [
        'default' => 'Our best experience is Google sign-in. Pick your account type and continue in one tap.',
        'chef' => 'Joining as a chef unlocks extra tools to showcase workshops and verify your profile after signing in.',
    ],
    'intent' => [
        'customer' => 'Customer account',
        'chef' => 'Chef account',
    ],
    'google' => [
        'cta' => 'Continue with Google (sign in / sign up)',
        'flags' => [
            'secure' => 'Secure authentication',
            'support' => 'Live Arabic support',
        ],
    ],
    'divider' => [
        'email' => 'Or continue with email',
    ],
    'form' => [
        'email' => [
            'label' => 'Email address',
            'hint' => 'Use the email associated with your Wasfah account.',
        ],
        'password' => [
            'label' => 'Password',
            'hint' => 'Use the strong password you picked earlier.',
        ],
        'remember' => 'Remember me for this session',
        'help' => 'Need help?',
        'submit' => 'Sign in now',
    ],
    'validation' => [
        'email_required' => 'Please enter your email address.',
        'email_email' => 'Enter a valid email address.',
        'password_required' => 'Please enter your password.',
        'credentials' => 'These credentials do not match our records. Check your email or password.',
    ],
    'flash' => [
        'workshop_success' => 'Signed in successfully! You can now complete the workshop booking.',
        'login_success' => 'Signed in successfully! Welcome back to Wasfah 👋',
    ],
];
