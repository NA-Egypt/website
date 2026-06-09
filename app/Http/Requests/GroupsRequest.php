<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupsRequest extends FormRequest
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
            'ar_name'           =>'required|min:3|regex:/^[\p{Arabic}0-9 ]+$/u',
            'en_name'           =>'required|min:3|regex:/^[A-Za-z0-9 ]+$/u',
            'ar_gsr_name'       =>'required|min:3|regex:/^[\p{Arabic}0-9 ]+$/u',
            'en_gsr_name'       =>'required|min:3|regex:/^[A-Za-z0-9 ]+$/u',
            'user_id'           =>'required',
            'phone'             => 'required|numeric',
            'location'          => [
                'nullable',
                'regex:~^(https?://)?(www\.)?(google\.com/maps|maps\.google\.com|maps\.app\.goo\.gl|goo\.gl/maps)[^\s]*$|^https?://[^\s]+$~'
            ],
            'ar_address'        => 'required|min:3',
            'en_address'        => 'required|min:3',
            'group_type'        => 'required',
            'service_body_id'   => 'required',
            'neighborhood_id'   => 'required',
            'capacity'          => 'nullable|integer',
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
            'ar_address.regex' => __('messages.The Arabic Address must contain only Arabic letters.'),
            'ar_address.required' => __('messages.This field is required'),
            'ar_address.min' => __('messages.You must insert 3 characters at least'),
            'en_address.regex' => __('messages.The English Address must contain only English letters.'),
            'en_address.required' => __('messages.This field is required'),
            'en_address.min' => __('messages.You must insert 3 characters at least'),
            'ar_gsr_name.regex' => __('messages.The Arabic name must contain only Arabic letters.'),
            'ar_gsr_name.min' => __('messages.You must insert 3 characters at least'),
            'ar_gsr_name.required' => __('messages.This field is required'),
            'en_gsr_name.regex' => __('messages.The Arabic name must contain only Arabic letters.'),
            'en_gsr_name.min' => __('messages.You must insert 3 characters at least'),
            'en_gsr_name.required' => __('messages.This field is required'),
            'user_id.required' => __('messages.This field is required'),
            'phone.required' => __('messages.This field is required'),
            'phone.numeric' => __('messages.This field must contain Numbers only'),
            'location.required' => __('messages.This field is required'),
            'location.regex' => __('messages.This field Must be Google Map Link'),
            'service_body_id.required' => __('messages.This field is required'),
            'neighborhood_id.required' => __('messages.This field is required'),
        ];
    }
}
