<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomForm;
use App\Models\CustomFormField;
use App\Models\CustomFormSubmission;
use App\Http\Resources\CustomFormResource;
use App\Http\Resources\CustomFormSubmissionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CustomFormController extends Controller
{
    /**
     * Display a listing of custom forms (Authenticated).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = CustomForm::with(['fields'])->withCount('submissions')->latest();

        if ($user && !$user->hasRole('super admin')) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = min((int) $request->get('per_page', 15), 100);
        return CustomFormResource::collection($query->paginate($perPage));
    }

    /**
     * Store a newly created form (Authenticated).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'type'               => 'nullable|string|max:50',
            'status'             => 'required|in:draft,published,archived',
            'slug'               => 'nullable|string|max:255|unique:custom_forms,slug',
            'settings'           => 'nullable|array',
            'fields'             => 'nullable|array',
            'fields.*.label'     => 'required_with:fields|string|max:255',
            'fields.*.type'      => 'required_with:fields|string',
            'fields.*.required'  => 'nullable|boolean',
            'fields.*.options'   => 'nullable|array',
            'fields.*.sort_order'=> 'nullable|integer',
        ]);

        $form = CustomForm::create([
            'title'    => $validated['title'],
            'type'     => $validated['type'] ?? 'general',
            'status'   => $validated['status'],
            'slug'     => $validated['slug'] ?? Str::random(12),
            'views'    => 0,
            'user_id'  => Auth::id(),
            'settings' => $validated['settings'] ?? [],
        ]);

        if (!empty($validated['fields'])) {
            foreach ($validated['fields'] as $index => $fieldData) {
                $form->fields()->create([
                    'label'      => $fieldData['label'],
                    'type'       => $fieldData['type'],
                    'required'   => $fieldData['required'] ?? false,
                    'options'    => $fieldData['options'] ?? null,
                    'sort_order' => $fieldData['sort_order'] ?? ($index + 1),
                ]);
            }
        }

        return (new CustomFormResource($form->load('fields')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified form (Authenticated).
     */
    public function show(CustomForm $form)
    {
        $user = Auth::user();
        if ($user && !$user->hasRole('super admin') && $form->user_id !== $user->id) {
            abort(403, 'Unauthorized to access this form.');
        }

        return new CustomFormResource($form->load('fields')->loadCount('submissions'));
    }

    /**
     * Update the specified form (Authenticated).
     */
    public function update(Request $request, CustomForm $form)
    {
        $user = Auth::user();
        if ($user && !$user->hasRole('super admin') && $form->user_id !== $user->id) {
            abort(403, 'Unauthorized to modify this form.');
        }

        $validated = $request->validate([
            'title'    => 'sometimes|required|string|max:255',
            'type'     => 'nullable|string|max:50',
            'status'   => 'sometimes|required|in:draft,published,archived',
            'slug'     => 'nullable|string|max:255|unique:custom_forms,slug,' . $form->id,
            'settings' => 'nullable|array',
        ]);

        $form->update($validated);

        return new CustomFormResource($form->load('fields')->loadCount('submissions'));
    }

    /**
     * Remove the specified form (Authenticated).
     */
    public function destroy(CustomForm $form)
    {
        $user = Auth::user();
        if ($user && !$user->hasRole('super admin') && $form->user_id !== $user->id) {
            abort(403, 'Unauthorized to delete this form.');
        }

        $form->delete();

        return response()->noContent();
    }

    /**
     * Display submissions for the specified form (Authenticated).
     */
    public function submissions(Request $request, CustomForm $form)
    {
        $user = Auth::user();
        if ($user && !$user->hasRole('super admin') && $form->user_id !== $user->id) {
            abort(403, 'Unauthorized to view submissions for this form.');
        }

        $perPage = min((int) $request->get('per_page', 15), 100);
        $submissions = $form->submissions()->with('user')->latest()->paginate($perPage);

        return CustomFormSubmissionResource::collection($submissions);
    }

    /**
     * Public show endpoint: view an active form and its fields by slug.
     */
    public function showPublic(string $slug)
    {
        $form = CustomForm::where('slug', $slug)->firstOrFail();

        if ($form->status !== 'published') {
            abort(404, 'Form is not published or inactive.');
        }

        $form->increment('views');

        return new CustomFormResource($form->load('fields'));
    }

    /**
     * Public submit endpoint: submit response data for an active form.
     */
    public function submitPublic(Request $request, string $slug)
    {
        $form = CustomForm::where('slug', $slug)->firstOrFail();

        if ($form->status !== 'published') {
            abort(422, 'Form is not currently active.');
        }

        $form->load('fields');

        // Validation rules
        $rules = [];
        $messages = [];

        foreach ($form->fields as $field) {
            if (in_array($field->type, ['section_header', 'static_text'])) {
                continue;
            }

            $inputKey = $request->has('field_' . $field->id)
                ? 'field_' . $field->id
                : ($request->has($field->id) ? (string)$field->id : 'field_' . $field->id);

            if ($field->required) {
                $rules[$inputKey] = 'required';
                $messages[$inputKey . '.required'] = "Field '{$field->label}' is required.";
            } else {
                $rules[$inputKey] = 'nullable';
            }

            if ($field->type === 'email') {
                $rules[$inputKey] .= '|email';
            } elseif ($field->type === 'number') {
                $rules[$inputKey] .= '|numeric';
            } elseif ($field->type === 'date') {
                $rules[$inputKey] .= '|date';
            }
        }

        $request->validate($rules, $messages);

        // Gather submission data mapping field IDs
        $submissionData = [];
        foreach ($form->fields as $field) {
            if (in_array($field->type, ['section_header', 'static_text'])) {
                continue;
            }

            $key = $request->has('field_' . $field->id)
                ? 'field_' . $field->id
                : ($request->has((string)$field->id) ? (string)$field->id : null);

            $submissionData[$field->id] = $key !== null ? $request->input($key) : null;
        }

        $submission = CustomFormSubmission::create([
            'custom_form_id' => $form->id,
            'user_id'        => Auth::id(),
            'data'           => $submissionData,
        ]);

        return (new CustomFormSubmissionResource($submission))
            ->response()
            ->setStatusCode(201);
    }
}
