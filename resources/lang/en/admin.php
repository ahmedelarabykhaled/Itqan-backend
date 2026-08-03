<?php

return [
    'locale' => [
        'switch' => 'Change language',
    ],

    'dashboard' => [
        'title' => 'Dashboard',
        'subheading' => 'An overview of the Quran content and the memorization activity of your app users.',
    ],

    'resources' => [
        'user' => [
            'label' => 'Administrator',
            'plural_label' => 'Administrators',
        ],
        'customer' => [
            'label' => 'App user',
            'plural_label' => 'App users',
        ],
        'quran_image' => [
            'label' => 'Mushaf image set',
            'plural_label' => 'Mushaf images',
        ],
        'quran_translation' => [
            'label' => 'Tafseer',
            'plural_label' => 'Tafaseer & translations',
        ],
    ],

    'audience' => [
        'heading' => 'App users',
        'description' => 'Sign-up and verification activity over the last 7 days.',
        'total' => 'Total users',
        'total_description' => 'All accounts registered in the app',
        'verified' => 'Verified accounts',
        'verified_description' => ':percentage% of all users',
        'new_this_week' => 'New users this week',
        'new_this_week_description' => 'Compared to :previous last week',
        'memorizers' => 'Users who started memorizing',
        'memorizers_description' => ':percentage% of all users',
    ],

    'content' => [
        'heading' => 'Quran content',
        'description' => 'What is currently available inside the app.',
        'surahs' => 'Available surahs',
        'surahs_description' => 'Out of :total surahs',
        'ayahs' => 'Total ayahs',
        'ayahs_description' => 'Sum of ayahs across registered surahs',
        'mushaf_images' => 'Mushaf image sets',
        'mushaf_images_description' => ':count uploaded pages',
        'tafaseer' => 'Tafaseer & translations',
        'tafaseer_description' => 'Available in :count languages',
    ],

    'charts' => [
        'memorization' => [
            'heading' => 'Memorization activity',
            'description' => 'Ayahs memorized per month over the last 12 months.',
            'dataset' => 'Memorized ayahs',
        ],
        'customers' => [
            'heading' => 'User growth',
            'description' => 'New sign-ups per month over the last 12 months.',
            'dataset' => 'New users',
        ],
    ],

    'latest_customers' => [
        'heading' => 'Latest registered users',
        'name' => 'Name',
        'provider' => 'Sign-up method',
        'provider_email' => 'Email address',
        'status' => 'Status',
        'verified' => 'Verified',
        'unverified' => 'Unverified',
        'registered_at' => 'Registered at',
    ],
];
