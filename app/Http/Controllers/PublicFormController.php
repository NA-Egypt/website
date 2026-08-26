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

            if ($field->type === 'yes_no_textbox') {
                $hasAnswerDirect = $request->has($fieldName);
                $answerFieldKey = $hasAnswerDirect ? $fieldName : ($fieldName . '_answer');
                if ($field->required) {
                    $rules[$answerFieldKey] = 'required|in:yes,no';
                    $messages[$answerFieldKey . '.required'] = "The field '{$field->label}' is required.";
                } else {
                    $rules[$answerFieldKey] = 'nullable|in:yes,no';
                }
            } elseif ($field->type === 'table') {
                if ($field->required) {
                    $rules[$fieldName] = 'required|array|min:1';
                    $messages[$fieldName . '.required'] = "The field '{$field->label}' is required.";
                } else {
                    $rules[$fieldName] = 'nullable|array';
                }
            } else {
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

                // If "Other" is chosen for dynamic or select field, validate other text input if field is required
                if ($field->required && $request->input($fieldName) === '__other__') {
                    $rules[$fieldName . '_other'] = 'required|string|max:255';
                    $messages[$fieldName . '_other.required'] = "Please specify a value for '{$field->label}'.";
                }
            }
        }

        $validated = $request->validate($rules, $messages);

        // Map request inputs back to field IDs
        $submissionData = [];
        foreach ($form->fields as $field) {
            if (in_array($field->type, ['section_header', 'static_text'])) {
                continue;
            }
            $fieldName = 'field_' . $field->id;

            if ($field->type === 'yes_no_textbox') {
                $answer = $request->input($fieldName . '_answer', $request->input($fieldName));
                $details = $request->input($fieldName . '_details');
                if ($answer) {
                    $submissionData[$field->id] = [
                        'answer' => $answer,
                        'details' => $answer === 'yes' ? $details : null,
                    ];
                } else {
                    $submissionData[$field->id] = null;
                }
            } elseif ($field->type === 'table') {
                $rawRows = $request->input($fieldName, []);
                $cleanRows = [];
                if (is_array($rawRows)) {
                    foreach ($rawRows as $row) {
                        if (is_array($row)) {
                            // Filter out empty rows
                            $hasContent = false;
                            foreach ($row as $cell) {
                                if (trim((string)$cell) !== '') {
                                    $hasContent = true;
                                    break;
                                }
                            }
                            if ($hasContent) {
                                $cleanRows[] = $row;
                            }
                        }
                    }
                }
                $submissionData[$field->id] = $cleanRows;
            } else {
                $val = $request->input($fieldName);
                if ($val === '__other__') {
                    $otherText = $request->input($fieldName . '_other');
                    $submissionData[$field->id] = !empty($otherText) ? trim($otherText) : 'Other';
                } else {
                    $submissionData[$field->id] = $val;
                }
            }
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
