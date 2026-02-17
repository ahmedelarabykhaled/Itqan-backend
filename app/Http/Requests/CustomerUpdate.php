<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'password' => 'nullable|string|min:6',
            'password_confirmation' => 'required_with=password|string|min:6',
            'gender' => 'nullable|in:male,female',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',   
        ];
    }

    public function messages()
    {
        return [
            'email.email' => __('customers.email_email'),
            'email.unique' => __('customers.email_unique'),
            'password.min' => __('customers.password_min'),
            'password_confirmation.required_with' => __('customers.password_confirmation_required_with'),
            'gender.in' => __('customers.gender_in'),
            'avatar.image' => __('customers.avatar_image'),
            'avatar.mimes' => __('customers.avatar_mimes'),
            'avatar.max' => __('customers.avatar_max'),
        ];
    }
}
