<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
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
            'full_name' => 'required|string|max:255',
            'phone_code' => 'nullable|string|max:10',
            'phone_number' => 'required|string',
        ];
    }
}
