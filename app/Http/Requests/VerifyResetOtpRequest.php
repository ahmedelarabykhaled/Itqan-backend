<?php

namespace App\Http\Requests;

class VerifyResetOtpRequest extends BaseFormRequest
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
            'token' => 'required_without_all:code,otp|nullable|string',
            'code' => 'nullable|string',
            'otp' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('customers.email_required'),
            'email.email' => __('customers.email_email'),
            'token.required_without_all' => __('customers.token_required'),
        ];
    }
}
