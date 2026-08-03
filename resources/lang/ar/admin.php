<?php

return [
    'locale' => [
        'switch' => 'تغيير اللغة',
    ],

    'dashboard' => [
        'title' => 'لوحة التحكم',
        'subheading' => 'نظرة عامة على محتوى المصحف ونشاط الحفظ لدى مستخدمي التطبيق.',
    ],

    'resources' => [
        'user' => [
            'label' => 'مشرف',
            'plural_label' => 'المشرفون',
        ],
        'customer' => [
            'label' => 'مستخدم',
            'plural_label' => 'مستخدمو التطبيق',
        ],
        'quran_image' => [
            'label' => 'مصحف مصوّر',
            'plural_label' => 'المصاحف المصوّرة',
        ],
        'quran_translation' => [
            'label' => 'تفسير',
            'plural_label' => 'التفاسير والترجمات',
        ],
    ],

    'audience' => [
        'heading' => 'مستخدمو التطبيق',
        'description' => 'حركة التسجيل والتفعيل خلال آخر ٧ أيام.',
        'total' => 'إجمالي المستخدمين',
        'total_description' => 'كل الحسابات المسجلة في التطبيق',
        'verified' => 'الحسابات المفعّلة',
        'verified_description' => ':percentage% من إجمالي المستخدمين',
        'new_this_week' => 'مستخدمون جدد هذا الأسبوع',
        'new_this_week_description' => 'مقارنة بـ :previous في الأسبوع الماضي',
        'memorizers' => 'مستخدمون بدأوا الحفظ',
        'memorizers_description' => ':percentage% من إجمالي المستخدمين',
    ],

    'content' => [
        'heading' => 'محتوى المصحف',
        'description' => 'ما هو متاح حاليًا داخل التطبيق من مصاحف وتفاسير.',
        'surahs' => 'السور المتاحة',
        'surahs_description' => 'من أصل :total سورة',
        'ayahs' => 'إجمالي الآيات',
        'ayahs_description' => 'مجموع آيات السور المسجلة',
        'mushaf_images' => 'المصاحف المصوّرة',
        'mushaf_images_description' => ':count صفحة مرفوعة',
        'tafaseer' => 'التفاسير والترجمات',
        'tafaseer_description' => 'متاحة بـ :count لغة',
    ],

    'charts' => [
        'memorization' => [
            'heading' => 'نشاط الحفظ',
            'description' => 'عدد الآيات التي تم حفظها شهريًا خلال آخر ١٢ شهرًا.',
            'dataset' => 'آيات محفوظة',
        ],
        'customers' => [
            'heading' => 'نمو المستخدمين',
            'description' => 'التسجيلات الجديدة شهريًا خلال آخر ١٢ شهرًا.',
            'dataset' => 'مستخدمون جدد',
        ],
    ],

    'latest_customers' => [
        'heading' => 'آخر المستخدمين المسجلين',
        'name' => 'الاسم',
        'provider' => 'طريقة التسجيل',
        'provider_email' => 'البريد الإلكتروني',
        'status' => 'الحالة',
        'verified' => 'مفعّل',
        'unverified' => 'غير مفعّل',
        'registered_at' => 'تاريخ التسجيل',
    ],
];
