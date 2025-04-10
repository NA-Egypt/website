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
            'email'             =>'required|email',
            'phone'             => 'required|numeric',
            'location'          => 'required',
            'group_type'        => 'required',
            'service_body_id'   => 'required',
            'neighborhood_id'   => 'required',
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
            'ar_gsr_name.required' => __('messages.This field is required'),
            'en_gsr_name.regex' => __('messages.The Arabic name must contain only Arabic letters.'),
            'en_gsr_name.min' => __('messages.You must insert 3 characters at least'),
            'en_gsr_name.required' => __('messages.This field is required'),
            'email.required' => __('messages.This field is required'),
            'email.email' => __('messages.The email field must be a valid email address'),
            'phone.required' => __('messages.This field is required'),
            'phone.numeric' => __('messages.This field must contain Numbers only'),
            'location.required' => __('messages.This field is required'),
            'service_body_id.required' => __('messages.This field is required'),
            'neighborhood_id.required' => __('messages.This field is required'),
        ];
    }
}
