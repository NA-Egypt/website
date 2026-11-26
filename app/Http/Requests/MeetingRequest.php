<?php

namespace App\Http\Requests;

use App\Models\Meeting;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class MeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $meeting = $this->route('meeting');
        if ($meeting instanceof Meeting) {
            return Gate::allows('update', $meeting);
        }

        return Gate::allows('create', Meeting::class);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isApi = $this->is('api/*');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $req = $isUpdate ? 'sometimes|required' : 'required';

        if ($isApi) {
            return [
                'group_id' => $req.'|exists:groups,id',
                'topics' => 'nullable|array|max:3',
                'topics.*' => 'exists:topics,id',
                'day_id' => $req.'|exists:days,id',
                'start_time' => $req,
                'end_time' => $req.'|after:start_time',
                'notes' => 'nullable|string',
                'type' => $req,
                'lang' => $req.'|in:arabic,english',
                'status' => $req.'|in:suspended,available',
                'options' => 'nullable|array',
                'options.*' => 'exists:options,id',
                'recurrence' => 'nullable|array',
                'recurrence.*' => 'in:weekly,1st,2nd,3rd,4th,5th,last',
            ];
        }

        return [
            'group_id' => 'required_without:direct_online_group_id|nullable|exists:groups,id',
            'direct_online_group_id' => 'required_without:group_id|nullable|exists:direct_online_groups,id',
            'topics' => 'nullable|array|max:3',
            'topics.*' => 'exists:topics,id',
            'day_id' => $isUpdate ? 'required|exists:days,id' : 'required',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'notes' => $isUpdate ? 'nullable|string' : 'nullable|string|not_regex:/https?:\/\/[^\s]+/',
            'type' => 'required',
            'lang' => 'required|in:'.($this->filled('direct_online_group_id') ? 'arabic' : 'arabic,english'),
            'status' => 'required|in:suspended,available',
            'options' => 'nullable|array',
            'options.*' => 'exists:options,id',
            'recurrence' => 'required|array',
            'recurrence.*' => 'in:weekly,1st,2nd,3rd,4th,5th,last',
        ];
    }
}
