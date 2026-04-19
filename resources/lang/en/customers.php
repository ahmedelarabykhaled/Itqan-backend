<?php

return [
    // Authentication messages
    'customer_not_found' => 'Customer not found.',
    'customer_not_verified' => 'Customer account is not verified.',
    'customer_logged_in_successfully' => 'Customer logged in successfully.',
    'customer_registered_successfully' => 'Customer registered successfully.',

    // Name validation
    'name_required' => 'The name field is required.',

    // Email validation
    'email_required' => 'The email field is required.',
    'email_email' => 'The email must be a valid email address.',
    'email_unique' => 'The email has already been taken.',
    'email_exists' => 'The selected email does not exist.',

    // Password validation
    'password_required' => 'The password field is required.',
    'password_min' => 'The password must be at least 6 characters.',
    'password_confirmation_required_with' => 'The password confirmation is required when password is present.',

    // Gender validation
    'gender_required' => 'The gender field is required.',
    'gender_in' => 'The selected gender is invalid.',

    // Avatar validation
    'avatar_nullable' => 'The avatar field must be null or a valid value.',
    'avatar_string' => 'The avatar must be a string.',
    'avatar_image' => 'The avatar must be an image.',
    'avatar_mimes' => 'The avatar must be a file of type: jpeg, png, jpg, gif.',
    'avatar_max' => 'The avatar may not be greater than 2048 kilobytes.',

    // Provider validation
    'provider_required' => 'The provider field is required.',
    'provider_in' => 'The selected provider is invalid.',
    'provider_id_required' => 'The provider ID field is required.',
    'provider_id_string' => 'The provider ID must be a string.',

    // Token validation
    'token_required' => 'The token field is required.',
    'token_string' => 'The token must be a string.',

    // Social token verification
    'social_token_invalid' => 'The social authentication token is invalid or expired.',
    'social_email_mismatch' => 'The email from the social provider does not match the provided email.',
];
