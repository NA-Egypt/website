<?php

namespace App\Http\Controllers;

use App\Models\CustomForm;
use App\Models\CustomFormSubmission;
use App\Models\Group;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\ServiceCommittee;
use App\Models\ServiceBody;
use Illuminate\Http\Request;

class PublicFormController extends Controller
{
    public function show($slug)
    {
        $form = CustomForm::where('slug', $slug)->firstOrFail();

        // Draft forms are only viewable by owner/admin
        if ($form->status !== 'published') {
            if (!auth()->check()) {
                abort(403, 'This form is currently a draft and not open to the public.');
            }
            $user = auth()->user();
            if (!$user->hasRole('super admin') && $form->user_id !== $user->id) {
                abort(403, 'This form is currently a draft.');
            }
        }

        // Increment views
        $form->increment('views');

        // Load fields
        $form->load('fields');

        // Fetch dynamic option data if any fields require them
        $entities = [];
        foreach ($form->fields as $field) {
            if ($field->type === 'groups') {
                $entities['groups'] = Group::all();
            } elseif ($field->type === 'cities') {
                $entities['cities'] = City::all();
            } elseif ($field->type === 'neighborhoods') {
                $entities['neighborhoods'] = Neighborhood::all();
            } elseif ($field->type === 'committees') {
                $entities['committees'] = ServiceCommittee::all();
            } elseif ($field->type === 'servicebodies') {
                $entities['servicebodies'] = ServiceBody::all();
            }
        }

        return view('forms.show', compact('form', 'entities'));
    }

    public function submit(Request $request, $slug)
    {
        $form = CustomForm::where('slug', $slug)->firstOrFail();

        if ($form->status !== 'published') {
            if (!auth()->check()) {
                abort(403, 'This form is not active.');
            }
            $user = auth()->user();
            if (!$user->hasRole('super admin') && $form->user_id !== $user->id) {
                abort(403, 'This form is not active.');
            }
        }

        $form->load('fields');

        // Build validation rules dynamically
        $rules = [];
        $messages = [];
        foreach ($form->fields as $field) {
            if (in_array($field->type, ['section_header', 'static_text'])) {
                continue;
            }
            $fieldName = 'field_' . $field->id;
            $rule = [];
            if ($field->required) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }

            if ($field->type === 'email') {
                $rule[] = 'email';
            } elseif ($field->type === 'number') {
                $rule[] = 'numeric';
            } elseif ($field->type === 'date') {
                $rule[] = 'date';
            }

            $rules[$fieldName] = implode('|', $rule);
            $messages[$fieldName . '.required'] = "The field '{$field->label}' is required.";
        }

        $validated = $request->validate($rules, $messages);

        // Map request inputs back to field IDs
        $submissionData = [];
        foreach ($form->fields as $field) {
            if (in_array($field->type, ['section_header', 'static_text'])) {
                continue;
            }
            $fieldName = 'field_' . $field->id;
            $submissionData[$field->id] = $request->input($fieldName);
        }

        $submission = CustomFormSubmission::create([
            'custom_form_id' => $form->id,
            'user_id' => auth()->id(),
            'data' => $submissionData,
        ]);

        // Send submission notifications if configured
        $emails = $form->settings['emails'] ?? [];
        if (!empty($emails) && is_array($emails)) {
            try {
                \Illuminate\Support\Facades\Mail::send('emails.form_submitted', ['form' => $form, 'submission' => $submission], function ($message) use ($emails, $form) {
                    $message->to($emails)
                            ->subject('New Submission for Form: ' . $form->title);
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send form submission email: ' . $e->getMessage());
            }
        }

        return view('forms.thankyou', compact('form'));
    }
}
