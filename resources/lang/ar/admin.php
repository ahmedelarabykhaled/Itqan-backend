<?php

return [
    'locale' => [
        'switch' => 'تغيير اللغة',
    ],

    'dashboard' => [
        'title' => 'لوحة التحكم',
        'subheading' => 'نظرة عامة على محتوى المصحف ونشاط الحفظ لدى مستخدمي التطبيق.',
    ],

    /*
     * Labels for model attributes, applied automatically to every table column,
     * form field, infolist entry and table filter of the same name.
     */
    'attributes' => [
        'avatar' => 'الصورة الشخصية',
        'created_at' => 'تاريخ الإنشاء',
        'current_version' => 'الإصدار الحالي',
        'display_name' => 'الاسم المعروض',
        'download_type' => 'نوع التنزيل',
        'email' => 'البريد الإلكتروني',
        'email_verified_at' => 'تاريخ تفعيل البريد',
        'external_id' => 'المعرّف الخارجي',
        'file' => 'الملف المضغوط',
        'mp3_file' => 'ملف الصوت',
        'file_name' => 'اسم الملف',
        'file_url' => 'رابط الملف',
        'gender' => 'النوع',
        'language_code' => 'اللغة',
        'minimum_version' => 'أقل إصدار مدعوم',
        'ayah_number' => 'رقم الآية',
        'memorized_at' => 'تاريخ الحفظ',
        'name' => 'الاسم',
        'name_en' => 'الاسم بالإنجليزية',
        'name_ar' => 'الاسم بالعربية',
        'status' => 'الحالة',
        'parts_count' => 'عدد الأجزاء',
        'password' => 'كلمة المرور',
        'provider' => 'طريقة التسجيل',
        'provider_id' => 'معرّف المزوّد',
        'remote_last_modified' => 'آخر تحديث في المصدر',
        'save_to' => 'مسار الحفظ',
        'source_url' => 'رابط المصدر',
        'slug' => 'المعرّف',
        'bitrate' => 'معدل البت',
        'surah' => 'السورة',
        'ayah' => 'الآية',
        'ayahs_count' => 'عدد الآيات',
        'statuses' => 'الحالات',
        'surah_id' => 'رقم السورة',
        'translator' => 'المترجم',
        'translator_foreign' => 'اسم المترجم بالإنجليزية',
        'updated_at' => 'تاريخ آخر تعديل',
        'width' => 'العرض',
    ],

    'memorized_statuses' => [
        'memorized' => 'محفوظ',
        'bookmarked' => 'معلّم',
        'saved' => 'محفوظ للمراجعة',
    ],

    'gender' => [
        'male' => 'ذكر',
        'female' => 'أنثى',
    ],

    'actions' => [
        'download' => 'تنزيل',
    ],

    'relations' => [
        'quran_image_parts' => 'أجزاء المصحف',
        'quran_recitation_ayahs' => 'آيات التلاوة',
        'memorized_ayahs' => 'سجل الحفظ',
    ],

    'customer' => [
        'memorized_count' => 'عدد الآيات في سجل الحفظ',
        'last_memorized_at' => 'آخر نشاط حفظ',
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
        'quran_recitation' => [
            'label' => 'تلاوة',
            'plural_label' => 'أصوات المشايخ',
        ],
    ],

    'quran_recitation_status' => [
        'enabled' => 'مفعّل',
        'disabled' => 'معطّل',
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
        'mushaf_images' => 'المصاحف المصوّرة',
        'mushaf_images_description' => ':count صفحة مرفوعة',
        'tafaseer' => 'التفاسير والترجمات',
        'tafaseer_description' => 'متاحة بـ :count لغة',
        'recitations' => 'أصوات المشايخ',
        'recitations_description' => ':count ملف صوتي',
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
        'provider_email' => 'البريد الإلكتروني',
        'status' => 'الحالة',
        'verified' => 'مفعّل',
        'unverified' => 'غير مفعّل',
        'registered_at' => 'تاريخ التسجيل',
    ],
];
