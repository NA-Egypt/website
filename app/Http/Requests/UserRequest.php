<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        $isApi = $this->is('api/*');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $req = $isUpdate ? 'sometimes|required' : 'required';

        if ($isApi) {
            return [
                'name' => $req.'|string|max:255',
                'email' => [$req, 'email', Rule::unique('users', 'email')->ignore($userId)],
                'password' => 'nullable|string|min:8',
                'service_body_id' => 'nullable|exists:service_bodies,id',
            ];
        }

        return [
            'display_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'roles' => 'array',
            'permissions' => 'array',
            'service_body_id' => 'nullable|exists:service_bodies,id',
        ];
    }
}
