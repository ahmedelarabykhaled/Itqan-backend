<?php

return [
    // Authentication messages
    'customer_not_found' => 'المستخدم غير موجود.',
    'customer_not_verified' => 'حساب المستخدم غير مفعل.',
    'customer_already_verified' => 'حساب المستخدم مفعل بالفعل.',
    'customer_logged_in_successfully' => 'تم تسجيل الدخول بنجاح.',
    'customer_registered_successfully' => 'تم تسجيل الحساب بنجاح.',
    'customer_deleted_successfully' => 'تم حذف حساب المستخدم بنجاح.',
    'otp_resent_successfully' => 'تم إعادة إرسال رمز التحقق بنجاح.',
    'invalid_code' => 'رمز التحقق غير صالح.',
    'code_expired' => 'انتهت صلاحية رمز التحقق.',
    'invalid_token' => 'الرمز (token) غير صالح.',
    'token_expired' => 'انتهت صلاحية الرمز (token).',
    'otp_valid' => 'رمز التحقق صحيح.',

    // Name validation
    'name_required' => 'حقل الاسم مطلوب.',

    // Email validation
    'email_required' => 'حقل البريد الإلكتروني مطلوب.',
    'email_email' => 'يجب أن يكون البريد الإلكتروني عنوان بريد صالحًا.',
    'email_unique' => 'البريد الإلكتروني مستخدم بالفعل.',
    'email_exists' => 'البريد الإلكتروني المحدد غير موجود.',

    // Password validation
    'password_required' => 'حقل كلمة المرور مطلوب.',
    'password_min' => 'يجب ألا تقل كلمة المرور عن 6 أحرف.',
    'password_confirmation_required_with' => 'حقل تأكيد كلمة المرور مطلوب عند إدخال كلمة المرور.',

    // Gender validation
    'gender_required' => 'حقل الجنس مطلوب.',
    'gender_in' => 'الجنس المحدد غير صالح.',

    // Avatar validation
    'avatar_nullable' => 'حقل الصورة الشخصية يجب أن يكون فارغًا أو قيمة صالحة.',
    'avatar_string' => 'يجب أن تكون الصورة الشخصية نصًا.',
    'avatar_image' => 'يجب أن تكون الصورة الشخصية صورة.',
    'avatar_mimes' => 'يجب أن تكون الصورة الشخصية ملفًا من نوع: jpeg, png, jpg, gif.',
    'avatar_max' => 'يجب ألا يزيد حجم الصورة الشخصية عن 2048 كيلوبايت.',

    // Provider validation
    'provider_required' => 'حقل مزود الخدمة مطلوب.',
    'provider_in' => 'مزود الخدمة المحدد غير صالح.',
    'provider_id_required' => 'حقل معرف مزود الخدمة مطلوب.',
    'provider_id_string' => 'يجب أن يكون معرف مزود الخدمة نصًا.',
    'provider_id_min' => 'يجب ألا يقل معرف مزود الخدمة عن 8 أحرف.',
    'provider_id_unique' => 'معرف مزود الخدمة مستخدم بالفعل.',

    // Token validation
    'token_required' => 'حقل الرمز (token) مطلوب.',
    'token_string' => 'يجب أن يكون الرمز (token) نصًا.',

    'customer_profile_fetched_successfully' => 'تم جلب ملف المستخدم بنجاح',
    'customer_updated_successfully' => 'تم تحديث ملف المستخدم بنجاح',
];
