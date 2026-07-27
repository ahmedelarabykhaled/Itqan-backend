<?php

namespace App\Http\Requests;

class SocialRegisterRequest extends BaseFormRequest
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
            'gender' => 'required|string|in:male,female',
            'avatar' => 'nullable|string',
            'provider' => 'required|string|in:google,apple',
            'provider_id' => 'required|string|min:8|unique:customers,provider_id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('customers.name_required'),
            'email.required' => __('customers.email_required'),
            'email.email' => __('customers.email_email'),
            'email.unique' => __('customers.email_unique'),
            'gender.required' => __('customers.gender_required'),
            'gender.in' => __('customers.gender_in'),
            'avatar.nullable' => __('customers.avatar_nullable'),
            'avatar.string' => __('customers.avatar_string'),
            'provider.required' => __('customers.provider_required'),
            'provider.in' => __('customers.provider_in'),
            'provider_id.required' => __('customers.provider_id_required'),
            'provider_id.string' => __('customers.provider_id_string'),
            'provider_id.min' => __('customers.provider_id_min'),
            'provider_id.unique' => __('customers.provider_id_unique'),
        ];
    }
}
