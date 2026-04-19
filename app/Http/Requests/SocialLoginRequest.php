<?php

namespace App\Http\Requests;

class SocialLoginRequest extends BaseFormRequest
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
            'email' => 'required|email',
            'provider' => 'required|string|in:google,apple',
            'provider_id' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('customers.email_required'),
            'email.email' => __('customers.email_email'),
            'provider.required' => __('customers.provider_required'),
            'provider.in' => __('customers.provider_in'),
            'provider_id.required' => __('customers.provider_id_required'),
            'provider_id.string' => __('customers.provider_id_string'),
        ];
    }
}
