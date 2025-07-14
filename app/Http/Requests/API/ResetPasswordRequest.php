<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'email' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required_with:password|same:password',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required.',
            'password.required' => 'Password is required.',
            'password.string' => 'Password must be a string.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Passwords do not match.',
            'password_confirmation.required_with' => 'Password confirmation is required when password is provided.'
        ];
    }
}
