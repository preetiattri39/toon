<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required_with:password|same:password',
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => 'The current password field is required.',
            'password.required' => 'The password is required.',
            'password.string' => 'The password must be a valid string.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.confirmed' => 'The password confirmation does not match.',
            
            'password_confirmation.required_with' => 'The password confirmation is required when setting a password.',
            'password_confirmation.same' => 'The password confirmation must match the password.',
        ];
    }
}
