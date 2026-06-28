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

        return redirect()->route('forms.index')->with('success', __('messages.form_created_success'));
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

        return redirect()->route('forms.index')->with('success', __('messages.form_updated_success'));
    }

    public function destroy(CustomForm $form)
    {
        $this->checkAccess($form);
        $form->delete();
        return redirect()->route('forms.index')->with('success', __('messages.form_deleted_success'));
    }

    public function toggleStatus(CustomForm $form)
    {
        $this->checkAccess($form);
        $form->status = $form->status === 'published' ? 'unpublished' : 'published';
        $form->save();

        return back()->with('success', __('messages.form_status_updated'));
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

        return redirect()->route('forms.index')->with('success', __('messages.form_duplicated_success'));
    }

    public function resetSubmissions(CustomForm $form)
    {
        $this->checkAccess($form);
        $form->submissions()->delete();
        $form->views = 0;
        $form->save();

        return redirect()->route('forms.index')->with('success', __('messages.form_reset_success'));
    }

    public function showReport(CustomForm $form)
    {
        $this->checkAccess($form);
        $form->load('fields');
        $submissions = $form->submissions()->with('user')->latest()->get();

        $groupsMap = [];
        $citiesMap = [];
        $neighborhoodsMap = [];
        $committeesMap = [];
        $serviceBodiesMap = [];

        $locale = app()->getLocale();
        $nameField = $locale === 'ar' ? 'ar_name' : 'en_name';

        foreach ($form->fields as $field) {
            if ($field->type === 'groups') {
                $groupsMap = \App\Models\Group::pluck($nameField, 'id')->toArray();
            } elseif ($field->type === 'cities') {
                $citiesMap = \App\Models\City::pluck($nameField, 'id')->toArray();
            } elseif ($field->type === 'neighborhoods') {
                $neighborhoodsMap = \App\Models\Neighborhood::pluck($nameField, 'id')->toArray();
            } elseif ($field->type === 'committees') {
                $committeesMap = \App\Models\ServiceCommittee::pluck($nameField, 'id')->toArray();
            } elseif ($field->type === 'servicebodies') {
                $serviceBodiesMap = \App\Models\ServiceBody::pluck($nameField, 'id')->toArray();
            }
        }

        $chartData = [];
        foreach ($form->fields as $field) {
            if (!in_array($field->type, ['select', 'checkbox', 'groups', 'cities', 'neighborhoods', 'committees', 'servicebodies', 'date'])) {
                continue;
            }

            if ($field->type === 'date') {
                $totalDays = 0;
                $entriesCount = 0;
                $minInterval = null;
                $maxInterval = null;
                $minDateStr = null;
                $maxDateStr = null;

                $brackets = [
                    'under 30 days' => 0,
                    'under 60 days' => 0,
                    'under 90 days' => 0,
                    'under 6 months' => 0,
                    'under 1 year' => 0,
                    '1-5 Years' => 0,
                    '5-10 years' => 0,
                    '10+ years' => 0,
                ];

                $now = new \DateTime();

                foreach ($submissions as $submission) {
                    $value = $submission->data[$field->id] ?? null;
                    if ($value === null || $value === '' || !strtotime($value)) {
                        continue;
                    }

                    $submittedDate = new \DateTime($value);
                    $interval = $submittedDate->diff($now);
                    $daysElapsed = $interval->days;

                    if ($submittedDate > $now) {
                        $daysElapsed = 0;
                        $years = 0;
                        $months = 0;
                        $days = 0;
                    } else {
                        $years = $interval->y;
                        $months = $interval->m;
                        $days = $interval->d;
                    }

                    $totalDays += $daysElapsed;
                    $entriesCount++;

                    if ($minInterval === null || $daysElapsed < $minInterval) {
                        $minInterval = $daysElapsed;
                        $minDateStr = sprintf($locale === 'ar' ? '%d سنة، %d شهر، %d يوم' : '%d years, %d months, %d days', $years, $months, $days);
                    }
                    if ($maxInterval === null || $daysElapsed > $maxInterval) {
                        $maxInterval = $daysElapsed;
                        $maxDateStr = sprintf($locale === 'ar' ? '%d سنة، %d شهر، %d يوم' : '%d years, %d months, %d days', $years, $months, $days);
                    }

                    if ($daysElapsed < 30) {
                        $brackets['under 30 days']++;
                    } elseif ($daysElapsed < 60) {
                        $brackets['under 60 days']++;
                    } elseif ($daysElapsed < 90) {
                        $brackets['under 90 days']++;
                    } elseif ($daysElapsed < 180) {
                        $brackets['under 6 months']++;
                    } elseif ($daysElapsed < 365) {
                        $brackets['under 1 year']++;
                    } elseif ($daysElapsed < 365 * 5) {
                        $brackets['1-5 Years']++;
                    } elseif ($daysElapsed < 365 * 10) {
                        $brackets['5-10 years']++;
                    } else {
                        $brackets['10+ years']++;
                    }
                }

                if ($entriesCount > 0) {
                    $totalYears = floor($totalDays / 365);
                    $remDays = $totalDays % 365;
                    $totalMonths = floor($remDays / 30);
                    $finalDays = $remDays % 30;

                    $exactTotalStr = sprintf($locale === 'ar' ? '%d سنة، %d شهر، %d يوم' : '%d years, %d months, %d days', $totalYears, $totalMonths, $finalDays);

                    $chartData[$field->id] = [
                        'field_id' => $field->id,
                        'label' => $field->label,
                        'type' => 'date',
                        'total_entries' => $entriesCount,
                        'exact_total' => $exactTotalStr,
                        'newest_elapsed' => $minDateStr,
                        'oldest_elapsed' => $maxDateStr,
                        'labels' => array_keys($brackets),
                        'data' => array_values($brackets),
                    ];
                }
            } else {
                $counts = [];

                // Initialize expected options for select and checkbox
                if (in_array($field->type, ['select', 'checkbox'])) {
                    $choices = isset($field->options['choices']) ? $field->options['choices'] : (is_array($field->options) ? $field->options : []);
                    $choices = array_filter($choices, function($val, $key) {
                        return !in_array($key, ['placeholder', 'description', 'bold', 'italic', 'align']) && !in_array($val, ['placeholder', 'description', 'bold', 'italic', 'align']);
                    }, ARRAY_FILTER_USE_BOTH);

                    foreach ($choices as $choice) {
                        $counts[$choice] = 0;
                    }
                }

                // Aggregate counts from submissions
                foreach ($submissions as $submission) {
                    $value = $submission->data[$field->id] ?? null;
                    if ($value === null || $value === '') {
                        continue;
                    }

                    if (is_array($value)) {
                        foreach ($value as $val) {
                            if ($val !== null && $val !== '') {
                                $counts[$val] = ($counts[$val] ?? 0) + 1;
                            }
                        }
                    } else {
                        $counts[$value] = ($counts[$value] ?? 0) + 1;
                    }
                }

                $labels = [];
                $data = [];
                
                if (in_array($field->type, ['groups', 'cities', 'neighborhoods', 'committees', 'servicebodies'])) {
                    $map = [];
                    if ($field->type === 'groups') $map = $groupsMap;
                    elseif ($field->type === 'cities') $map = $citiesMap;
                    elseif ($field->type === 'neighborhoods') $map = $neighborhoodsMap;
                    elseif ($field->type === 'committees') $map = $committeesMap;
                    elseif ($field->type === 'servicebodies') $map = $serviceBodiesMap;

                    foreach ($counts as $id => $count) {
                        $name = $map[$id] ?? "#$id";
                        $labels[] = $name;
                        $data[] = $count;
                    }
                } else {
                    foreach ($counts as $label => $count) {
                        $labels[] = $label;
                        $data[] = $count;
                    }
                }

                if (!empty($labels)) {
                    $chartData[$field->id] = [
                        'field_id' => $field->id,
                        'label' => $field->label,
                        'type' => $field->type,
                        'labels' => $labels,
                        'data' => $data,
                    ];
                }
            }
        }

        return view('forms.report', compact('form', 'submissions', 'chartData'));
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
