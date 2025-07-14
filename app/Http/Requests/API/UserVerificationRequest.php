<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UserVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:4', // Adjust 'digits' based on your OTP length
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.exists' => 'The provided email does not exist in our records.',
            'otp.required' => 'The OTP field is required.',
            'otp.digits' => 'The OTP must be exactly 4 digits.', // Adjust based on OTP length
        ];
    }
}
