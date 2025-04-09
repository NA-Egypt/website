<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceBodyRequest extends FormRequest
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
            'ar_name' => 'required|min:2|regex:/^[\p{Arabic}0-9 ]+$/u',
            'en_name' => 'required|min:2|regex:/^[A-Za-z0-9 ]+$/u',
            'description'   => 'required',
            'day_id'        => 'required|exists:days,id',
            'date'          => 'required|date',
            'start_time'    => 'required',
            'end_time'      => 'required|after:start_time',
            'location'      => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'ar_name.regex'         => __('messages.The Arabic name must contain only Arabic letters.'),
            'ar_name.required'      => __('messages.This field is required'),
            'ar_name.min'           => __('messages.You must insert 3 characters at least'),
            'en_name.regex'         => __('messages.The English name must contain only English letters.'),
            'en_name.required'      => __('messages.This field is required'),
            'en_name.min'           => __('messages.You must insert 3 characters at least'),
            'description.required'  => __('messages.This field is required'),
            'day_id.required'       => __('messages.This field is required'),
            'date.required'         => __('messages.This field is required'),
            'start_time.required'   => __('messages.This field is required'),
            'end_time.required'     => __('messages.This field is required'),
            'location.required'     => __('messages.This field is required'),
        ];
    }
}
