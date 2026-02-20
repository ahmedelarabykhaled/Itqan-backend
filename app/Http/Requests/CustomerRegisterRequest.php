<?php

namespace App\Http\Requests;

class CustomerRegisterRequest extends BaseFormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|string|min:6',
            'gender' => 'required|in:male,female',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('customers.name_required'),
            'email.required' => __('customers.email_required'),
            'email.email' => __('customers.email_email'),
            'email.unique' => __('customers.email_unique'),
            'password.required' => __('customers.password_required'),
            'password.min' => __('customers.password_min'),
            'gender.required' => __('customers.gender_required'),
            'gender.in' => __('customers.gender_in'),
        ];
    }
}
