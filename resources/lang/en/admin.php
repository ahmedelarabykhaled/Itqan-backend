<?php

return [
    'locale' => [
        'switch' => 'Change language',
    ],

    'dashboard' => [
        'title' => 'Dashboard',
        'subheading' => 'An overview of the Quran content and the memorization activity of your app users.',
    ],

    /*
     * Labels for model attributes, applied automatically to every table column,
     * form field, infolist entry and table filter of the same name.
     */
    'attributes' => [
        'avatar' => 'Avatar',
        'created_at' => 'Created at',
        'current_version' => 'Current version',
        'display_name' => 'Display name',
        'download_type' => 'Download type',
        'email' => 'Email address',
        'email_verified_at' => 'Email verified at',
        'external_id' => 'External ID',
        'file' => 'File',
        'file_name' => 'File name',
        'file_url' => 'File URL',
        'gender' => 'Gender',
        'language_code' => 'Language',
        'minimum_version' => 'Minimum version',
        'ayah_number' => 'Ayah number',
        'memorized_at' => 'Memorized at',
        'name' => 'Name',
        'parts_count' => 'Parts',
        'password' => 'Password',
        'provider' => 'Sign-up method',
        'provider_id' => 'Provider ID',
        'remote_last_modified' => 'Remote last modified',
        'save_to' => 'Save to',
        'source_url' => 'Source URL',
        'slug' => 'Slug',
        'bitrate' => 'Bitrate',
        'surah' => 'Surah',
        'ayah' => 'Ayah',
        'ayahs_count' => 'Ayah files',
        'statuses' => 'Statuses',
        'surah_id' => 'Surah number',
        'translator' => 'Translator',
        'translator_foreign' => 'Translator name in English',
        'updated_at' => 'Updated at',
        'width' => 'Width',
    ],

    'memorized_statuses' => [
        'memorized' => 'Memorized',
        'bookmarked' => 'Bookmarked',
        'saved' => 'Saved for review',
    ],

    'gender' => [
        'male' => 'Male',
        'female' => 'Female',
    ],

    'actions' => [
        'download' => 'Download',
    ],

    'relations' => [
        'quran_image_parts' => 'Mushaf parts',
        'quran_recitation_ayahs' => 'Recitation ayahs',
        'memorized_ayahs' => 'Memorization history',
    ],

    'customer' => [
        'memorized_count' => 'Memorization history count',
        'last_memorized_at' => 'Last memorization activity',
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
        'quran_recitation' => [
            'label' => 'Recitation',
            'plural_label' => 'Quran recitations',
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
        'mushaf_images' => 'Mushaf image sets',
        'mushaf_images_description' => ':count uploaded pages',
        'tafaseer' => 'Tafaseer & translations',
        'tafaseer_description' => 'Available in :count languages',
        'recitations' => 'Quran recitations',
        'recitations_description' => ':count audio files',
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
        'provider_email' => 'Email address',
        'status' => 'Status',
        'verified' => 'Verified',
        'unverified' => 'Unverified',
        'registered_at' => 'Registered at',
    ],
];
