<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CityNameRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'ar_name' => 'required|min:3|regex:/^[\p{Arabic}0-9 ]+$/u',
            'en_name' => 'required|min:3|regex:/^[A-Za-z0-9 ]+$/u',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ];
    }

    /**
     * Get the custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'ar_name.regex' => __('messages.The Arabic name must contain only Arabic letters.'),
            'en_name.regex' => __('messages.The English name must contain only English letters.'),
            'ar_name.required' => __('messages.This field is required'),
            'en_name.required' => __('messages.This field is required'),
            'ar_name.min' => __('messages.You must insert 3 characters at least'),
            'en_name.min' => __('messages.You must insert 3 characters at least')
        ];
    }
}
