<?php

namespace App\Http\Controllers;

use App\Models\CustomForm;
use App\Models\CustomFormField;
use App\Models\CustomFormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Mpdf\Mpdf;

class CustomFormController extends Controller
{
    // Route middleware handled in routes/web.php

    private function checkAccess(CustomForm $form)
    {
        $user = auth()->user();
        if ($user->hasRole('super admin') || $user->hasRole('rsc')) {
            return;
        }
        if ($form->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this form.');
        }
    }

    public function index()
    {
        $user = auth()->user();
        if ($user->hasRole('super admin') || $user->hasRole('rsc')) {
            $forms = CustomForm::with('submissions')->orderBy('created_at', 'desc')->get();
        } else {
            $forms = CustomForm::with('submissions')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('forms.index', compact('forms'));
    }

    public function create()
    {
        return view('forms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:survey,event_registration',
            'status' => 'required|in:draft,published,unpublished',
            'settings' => 'nullable|array',
            'settings.icon' => 'nullable|string|max:50',
            'settings.emails' => 'nullable|string|max:1000',
            'settings.subtitle' => 'nullable|string|max:255',
            'fields' => 'nullable|array',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|string',
            'fields.*.required' => 'nullable|boolean',
            'fields.*.options' => 'nullable|string', // comma separated options
            'fields.*.placeholder' => 'nullable|string|max:255',
            'fields.*.description' => 'nullable|string|max:1000',
            'fields.*.bold' => 'nullable|boolean',
            'fields.*.italic' => 'nullable|boolean',
            'fields.*.align' => 'nullable|string|in:left,center,right',
        ]);

        $settings = $request->input('settings', []);
        if (isset($settings['emails']) && is_string($settings['emails'])) {
            $emails = array_filter(array_map('trim', explode(',', $settings['emails'])));
            $validatedEmails = [];
            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $validatedEmails[] = $email;
                }
            }
            $settings['emails'] = array_slice($validatedEmails, 0, 3);
        } else {
            $settings['emails'] = [];
        }

        $form = CustomForm::create([
            'title' => $request->title,
            'type' => $request->type,
            'status' => $request->status,
            'user_id' => auth()->id(),
            'slug' => Str::random(12),
            'settings' => $settings,
        ]);

        if ($request->has('fields')) {
            foreach ($request->fields as $index => $fieldData) {
                $choices = [];
                if (!empty($fieldData['options'])) {
                    $choices = array_map('trim', explode(',', $fieldData['options']));
                }
                $optionsJSON = [
                    'choices' => $choices,
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'description' => $fieldData['description'] ?? null,
                    'bold' => !empty($fieldData['bold']),
                    'italic' => !empty($fieldData['italic']),
                    'align' => $fieldData['align'] ?? 'left',
                ];
                CustomFormField::create([
                    'custom_form_id' => $form->id,
                    'label' => $fieldData['label'],
                    'type' => $fieldData['type'],
                    'required' => !empty($fieldData['required']),
                    'options' => $optionsJSON,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('forms.index')->with('success', 'Form created successfully.');
    }

    public function edit(CustomForm $form)
    {
        $this->checkAccess($form);
        $form->load('fields');
        return view('forms.edit', compact('form'));
    }

    public function update(Request $request, CustomForm $form)
    {
        $this->checkAccess($form);

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:survey,event_registration',
            'status' => 'required|in:draft,published,unpublished',
            'settings' => 'nullable|array',
            'settings.icon' => 'nullable|string|max:50',
            'settings.emails' => 'nullable|string|max:1000',
            'settings.subtitle' => 'nullable|string|max:255',
            'fields' => 'nullable|array',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|string',
            'fields.*.required' => 'nullable|boolean',
            'fields.*.options' => 'nullable|string',
            'fields.*.placeholder' => 'nullable|string|max:255',
            'fields.*.description' => 'nullable|string|max:1000',
            'fields.*.bold' => 'nullable|boolean',
            'fields.*.italic' => 'nullable|boolean',
            'fields.*.align' => 'nullable|string|in:left,center,right',
        ]);

        $settings = $request->input('settings', []);
        if (isset($settings['emails']) && is_string($settings['emails'])) {
            $emails = array_filter(array_map('trim', explode(',', $settings['emails'])));
            $validatedEmails = [];
            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $validatedEmails[] = $email;
                }
            }
            $settings['emails'] = array_slice($validatedEmails, 0, 3);
        } else {
            $settings['emails'] = [];
        }

        $form->update([
            'title' => $request->title,
            'type' => $request->type,
            'status' => $request->status,
            'settings' => $settings,
        ]);

        // Re-build fields
        $form->fields()->delete();

        if ($request->has('fields')) {
            foreach ($request->fields as $index => $fieldData) {
                $choices = [];
                if (!empty($fieldData['options'])) {
                    $choices = array_map('trim', explode(',', $fieldData['options']));
                }
                $optionsJSON = [
                    'choices' => $choices,
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'description' => $fieldData['description'] ?? null,
                    'bold' => !empty($fieldData['bold']),
                    'italic' => !empty($fieldData['italic']),
                    'align' => $fieldData['align'] ?? 'left',
                ];
                CustomFormField::create([
                    'custom_form_id' => $form->id,
                    'label' => $fieldData['label'],
                    'type' => $fieldData['type'],
                    'required' => !empty($fieldData['required']),
                    'options' => $optionsJSON,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('forms.index')->with('success', 'Form updated successfully.');
    }

    public function destroy(CustomForm $form)
    {
        $this->checkAccess($form);
        $form->delete();
        return redirect()->route('forms.index')->with('success', 'Form deleted successfully.');
    }

    public function toggleStatus(CustomForm $form)
    {
        $this->checkAccess($form);
        $form->status = $form->status === 'published' ? 'unpublished' : 'published';
        $form->save();

        return back()->with('success', 'Form status updated successfully.');
    }

    public function duplicate(CustomForm $form)
    {
        $this->checkAccess($form);

        // Copy form details
        $newForm = $form->replicate();
        $newForm->title = 'Copy of ' . $form->title;
        $newForm->slug = Str::random(12);
        $newForm->views = 0;
        $newForm->status = 'draft';
        $newForm->created_at = now();
        $newForm->updated_at = now();
        $newForm->save();

        // Copy form fields
        foreach ($form->fields as $field) {
            $newField = $field->replicate();
            $newField->custom_form_id = $newForm->id;
            $newField->save();
        }

        return redirect()->route('forms.index')->with('success', 'Form duplicated successfully. It appears at the top of the list.');
    }

    public function resetSubmissions(CustomForm $form)
    {
        $this->checkAccess($form);
        $form->submissions()->delete();
        $form->views = 0;
        $form->save();

        return redirect()->route('forms.index')->with('success', 'Form submissions and views reset successfully.');
    }

    public function showReport(CustomForm $form)
    {
        $this->checkAccess($form);
        $form->load('fields');
        $submissions = $form->submissions()->with('user')->latest()->get();
        return view('forms.report', compact('form', 'submissions'));
    }

    public function exportPdf(CustomForm $form)
    {
        $this->checkAccess($form);
        $form->load(['fields', 'submissions.user']);

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'directionality' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
            'fontDir' => array_merge($fontDirs, [resource_path('fonts')]),
            'fontdata' => $fontData + [
                'amiri' => [
                    'R' => 'Amiri-Regular.ttf',
                ],
                'cairo' => [
                    'R' => 'Cairo-Regular.ttf',
                ],
            ],
            'default_font' => 'xbriyaz',
        ]);
        
        $mpdf->autoArabic = true;
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $html = view('forms.report_pdf', compact('form'))->render();
        $mpdf->WriteHTML($html);

        $filename = "form_{$form->id}_report.pdf";
        
        return response($mpdf->Output($filename, 'S'), 200)
               ->header('Content-Type', 'application/pdf')
               ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exportSubmissionPdf(CustomForm $form, CustomFormSubmission $submission)
    {
        $this->checkAccess($form);
        if ($submission->custom_form_id !== $form->id) {
            abort(404);
        }
        $form->load('fields');

        $pdf = Pdf::loadView('forms.submission_pdf', compact('form', 'submission'));
        return $pdf->download("submission_{$submission->id}.pdf");
    }

    public function exportCsv(CustomForm $form)
    {
        $this->checkAccess($form);
        $form->load(['fields', 'submissions']);

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=form_{$form->id}_submissions.csv",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $callback = function() use ($form) {
            $file = fopen('php://output', 'w');
            
            // CSV Header row
            $header = ['Submission ID', 'Submitted At', 'Submitted By'];
            foreach ($form->fields as $field) {
                $header[] = $field->label;
            }
            fputcsv($file, $header);

            // CSV Data rows
            foreach ($form->submissions as $submission) {
                $row = [
                    $submission->id,
                    $submission->created_at->format('Y-m-d H:i:s'),
                    $submission->user ? $submission->user->name : 'Guest'
                ];
                foreach ($form->fields as $field) {
                    $row[] = $submission->data[$field->id] ?? '';
                }
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
