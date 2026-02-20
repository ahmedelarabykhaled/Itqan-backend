<?php

namespace App\Http\Requests;

class ActivateAccountRequest extends BaseFormRequest
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
            'code' => 'required|string|min:6',
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
