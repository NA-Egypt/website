<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DirectOnlineGroupsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ar_name'      => 'required|min:3|regex:/^[\p{Arabic}0-9 ]+$/u',
            'en_name'      => 'required|min:3|regex:/^[A-Za-z0-9 ]+$/u',
            'ar_gsr_name'  => 'nullable|min:3|regex:/^[\p{Arabic}0-9 ]+$/u',
            'en_gsr_name'  => 'nullable|min:3|regex:/^[A-Za-z0-9 ]+$/u',
            'phone'        => 'nullable|numeric',
            'location'     => [
                'required',
                'url',
                'regex:/^(https?:\/\/)?([a-zA-Z0-9-]+\.)?zoom\.us\/[^\s]+$/i'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'ar_name.regex' => __('messages.The Arabic name must contain only Arabic letters.'),
            'ar_name.required' => __('messages.This field is required'),
            'ar_name.min' => __('messages.You must insert 3 characters at least'),
            'en_name.regex' => __('messages.The English name must contain only English letters.'),
            'en_name.required' => __('messages.This field is required'),
            'en_name.min' => __('messages.You must insert 3 characters at least'),
            'ar_gsr_name.regex' => __('messages.The Arabic name must contain only Arabic letters.'),
            'ar_gsr_name.min' => __('messages.You must insert 3 characters at least'),
            'en_gsr_name.regex' => __('messages.The English name must contain only English letters.'),
            'en_gsr_name.min' => __('messages.You must insert 3 characters at least'),
            'phone.numeric' => __('messages.This field must contain Numbers only'),
            'location.required' => __('messages.This field is required'),
            'location.url' => __('messages.This field Must be Google Map Link'),
            'location.regex' => 'This field must be a valid Zoom meeting link (zoom.us)',
        ];
    }
}
