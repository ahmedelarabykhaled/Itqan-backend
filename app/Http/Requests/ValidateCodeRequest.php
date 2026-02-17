<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateCodeRequest extends FormRequest
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
            'email' => 'required|email|exists:customers,email',
            'token' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('customers.email_required'),
            'email.email' => __('customers.email_email'),
            'email.exists' => __('customers.email_exists'),
        ];
    }
}
