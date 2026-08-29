<?php

namespace App\Http\Controllers;

use App\Models\CustomForm;
use App\Models\CustomFormField;
use App\Models\CustomFormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\MpdfService;

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
            'type' => 'required|in:survey,event_registration,service_position_application',
            'status' => 'required|in:draft,published,unpublished',
            'settings' => 'nullable|array',
            'settings.icon' => 'nullable|string|max:50',
            'settings.emails' => 'nullable|string|max:1000',
            'settings.subtitle' => 'nullable|string|max:255',
            'fields' => 'nullable|array',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|string',
            'fields.*.required' => 'nullable|boolean',
            'fields.*.options' => 'nullable|string', // comma separated options or columns
            'fields.*.placeholder' => 'nullable|string|max:255',
            'fields.*.description' => 'nullable|string|max:1000',
            'fields.*.bold' => 'nullable|boolean',
            'fields.*.italic' => 'nullable|boolean',
            'fields.*.align' => 'nullable|string|in:left,center,right',
        ]);

        $settings = $request->input('settings', []);
        if (isset($settings['emails']) && is_string($settings['emails'])) {
            $parts = preg_split('/[\r\n,;،\s]+/', $settings['emails'], -1, PREG_SPLIT_NO_EMPTY);
            $validatedEmails = [];
            foreach ($parts as $email) {
                $email = trim($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $validatedEmails[] = $email;
                }
            }
            $settings['emails'] = array_slice(array_values(array_unique($validatedEmails)), 0, 3);
        } elseif (isset($settings['emails']) && is_array($settings['emails'])) {
            $validatedEmails = [];
            foreach ($settings['emails'] as $item) {
                if (is_string($item)) {
                    $parts = preg_split('/[\r\n,;،\s]+/', $item, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($parts as $email) {
                        $email = trim($email);
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $validatedEmails[] = $email;
                        }
                    }
                }
            }
            $settings['emails'] = array_slice(array_values(array_unique($validatedEmails)), 0, 3);
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
                $optionsJSON = $this->parseFieldOptions($fieldData);
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
            'type' => 'required|in:survey,event_registration,service_position_application',
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
            $parts = preg_split('/[\r\n,;،\s]+/', $settings['emails'], -1, PREG_SPLIT_NO_EMPTY);
            $validatedEmails = [];
            foreach ($parts as $email) {
                $email = trim($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $validatedEmails[] = $email;
                }
            }
            $settings['emails'] = array_slice(array_values(array_unique($validatedEmails)), 0, 3);
        } elseif (isset($settings['emails']) && is_array($settings['emails'])) {
            $validatedEmails = [];
            foreach ($settings['emails'] as $item) {
                if (is_string($item)) {
                    $parts = preg_split('/[\r\n,;،\s]+/', $item, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($parts as $email) {
                        $email = trim($email);
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $validatedEmails[] = $email;
                        }
                    }
                }
            }
            $settings['emails'] = array_slice(array_values(array_unique($validatedEmails)), 0, 3);
        } else {
            $settings['emails'] = [];
        }

        $form->update([
            'title' => $request->title,
            'type' => $request->type,
            'status' => $request->status,
            'settings' => $settings,
        ]);

        // Sync fields in-place to preserve field IDs and keep existing submissions intact
        $existingFields = $form->fields()->get()->keyBy('id');
        $processedFieldIds = [];

        if ($request->has('fields')) {
            foreach ($request->fields as $index => $fieldData) {
                $optionsJSON = $this->parseFieldOptions($fieldData);
                $fieldId = !empty($fieldData['id']) ? (int) $fieldData['id'] : null;

                if ($fieldId && $existingFields->has($fieldId)) {
                    // Update existing field in-place so its ID remains unchanged
                    $field = $existingFields->get($fieldId);
                    $field->update([
                        'label' => $fieldData['label'],
                        'type' => $fieldData['type'],
                        'required' => !empty($fieldData['required']),
                        'options' => $optionsJSON,
                        'sort_order' => $index,
                    ]);
                    $processedFieldIds[] = $fieldId;
                } else {
                    // Create newly added field
                    $newField = CustomFormField::create([
                        'custom_form_id' => $form->id,
                        'label' => $fieldData['label'],
                        'type' => $fieldData['type'],
                        'required' => !empty($fieldData['required']),
                        'options' => $optionsJSON,
                        'sort_order' => $index,
                    ]);
                    $processedFieldIds[] = $newField->id;
                }
            }
        }

        // Only delete fields that were genuinely removed in the editor
        $form->fields()->whereNotIn('id', $processedFieldIds)->delete();

        return redirect()->route('forms.index')->with('success', __('messages.form_updated_success'));
    }

    private function parseFieldOptions(array $fieldData): array
    {
        $rawOptions = $fieldData['options'] ?? '';
        $choices = [];
        $columns = [];
        $optionNotes = [];

        if (!empty($rawOptions)) {
            if (($fieldData['type'] ?? '') === 'select') {
                $items = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $rawOptions))));
                foreach ($items as $item) {
                    if (str_contains($item, ':')) {
                        $parts = explode(':', $item, 2);
                        $optName = trim($parts[0]);
                        $optNote = trim($parts[1]);
                        if (!empty($optName)) {
                            $choices[] = $optName;
                            if (!empty($optNote)) {
                                $optionNotes[$optName] = $optNote;
                            }
                        }
                    } else {
                        $choices[] = $item;
                    }
                }
            } else {
                $choices = array_values(array_filter(array_map('trim', explode(',', $rawOptions))));
            }
            $columns = $choices;
        }

        return [
            'choices' => $choices,
            'columns' => $columns,
            'option_notes' => $optionNotes,
            'raw_options' => $rawOptions,
            'placeholder' => $fieldData['placeholder'] ?? null,
            'description' => $fieldData['description'] ?? null,
            'bold' => !empty($fieldData['bold']),
            'italic' => !empty($fieldData['italic']),
            'align' => $fieldData['align'] ?? 'left',
        ];
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
            if (!in_array($field->type, ['select', 'checkbox', 'groups', 'cities', 'neighborhoods', 'committees', 'servicebodies', 'date', 'yes_no_textbox'])) {
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
            } elseif ($field->type === 'yes_no_textbox') {
                $yesLabel = __('messages.yes') ?? 'Yes';
                $noLabel = __('messages.no') ?? 'No';
                $counts = [$yesLabel => 0, $noLabel => 0];

                foreach ($submissions as $submission) {
                    $value = $submission->data[$field->id] ?? null;
                    if ($value === null || $value === '') continue;

                    $ans = is_array($value) ? ($value['answer'] ?? null) : $value;
                    if ($ans === 'yes') {
                        $counts[$yesLabel]++;
                    } elseif ($ans === 'no') {
                        $counts[$noLabel]++;
                    }
                }

                $chartData[$field->id] = [
                    'field_id' => $field->id,
                    'label' => $field->label,
                    'type' => 'yes_no_textbox',
                    'labels' => array_keys($counts),
                    'data' => array_values($counts),
                ];
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
                            if ($val !== null && $val !== '' && is_string($val)) {
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

        $mpdf = MpdfService::create();

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

        $mpdf = MpdfService::create();
        $html = view('forms.submission_pdf', compact('form', 'submission'))->render();
        $mpdf->WriteHTML($html);

        $cleanTitle = preg_replace('/[^\p{L}\p{N}_-]+/u', '_', $form->title);
        $cleanTitle = trim($cleanTitle, '_');
        $filename = "{$cleanTitle}_Response_{$submission->id}.pdf";

        return response($mpdf->Output($filename, 'S'), 200)
               ->header('Content-Type', 'application/pdf')
               ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exportSubmissionCsv(CustomForm $form, CustomFormSubmission $submission)
    {
        $this->checkAccess($form);
        if ($submission->custom_form_id !== $form->id) {
            abort(404);
        }
        $form->load('fields');

        $cleanTitle = preg_replace('/[^\p{L}\p{N}_-]+/u', '_', $form->title);
        $cleanTitle = trim($cleanTitle, '_');
        $filename = "{$cleanTitle}_Response_{$submission->id}.csv";

        $headers = [
            'Content-type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $callback = function() use ($form, $submission) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel Arabic / Multilingual compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // 2-Column Header
            fputcsv($file, [
                __('messages.Question / Field') ?? 'Question / Field',
                __('messages.Response / Answer') ?? 'Response / Answer',
            ]);

            // Metadata Rows
            fputcsv($file, [__('messages.Submission ID') ?? 'Submission ID', '#' . $submission->id]);
            fputcsv($file, [__('messages.Submitted At') ?? 'Submitted At', $submission->created_at->format('Y-m-d H:i:s')]);
            fputcsv($file, [__('messages.Submitted By') ?? 'Submitted By', $submission->user ? $submission->user->name : (__('messages.Guest') ?? 'Guest')]);

            // Fields Rows
            foreach ($form->fields as $field) {
                if ($field->type === 'section_header') {
                    fputcsv($file, ['--- ' . $field->label . ' ---', '']);
                    continue;
                }
                if ($field->type === 'static_text') {
                    continue;
                }

                $val = $submission->data[$field->id] ?? '-';
                if (is_array($val)) {
                    if ($field->type === 'yes_no_textbox') {
                        $ans = $val['answer'] ?? '';
                        $det = $val['details'] ?? '';
                        $ansText = $ans === 'yes' ? (__('messages.yes') ?? 'Yes') : ($ans === 'no' ? (__('messages.no') ?? 'No') : $ans);
                        $formattedVal = $ansText . ($det ? " (" . (__('messages.Details / Explanation') ?? 'Details') . ": {$det})" : '');
                    } elseif ($field->type === 'table') {
                        $rowStrings = [];
                        foreach ($val as $rIndex => $r) {
                            if (is_array($r)) {
                                $colPairs = [];
                                foreach ($r as $colKey => $colVal) {
                                    $colPairs[] = "{$colKey}: {$colVal}";
                                }
                                $rowStrings[] = "[" . implode(', ', $colPairs) . "]";
                            }
                        }
                        $formattedVal = implode(" ; ", $rowStrings);
                    } else {
                        $formattedVal = implode(', ', $val);
                    }
                } elseif ($field->type === 'date' && !empty($val) && $val !== '-' && strtotime($val)) {
                    $submittedDate = new \DateTime($val);
                    $now = new \DateTime();
                    $interval = $submittedDate->diff($now);
                    $locale = app()->getLocale();
                    if ($submittedDate > $now) {
                        $elapsedStr = $locale === 'ar' ? 'في المستقبل' : 'in the future';
                    } else {
                        $elapsedStr = sprintf(
                            $locale === 'ar' ? '(%d سنة، %d شهر، %d يوم)' : '(%d years, %d months, %d days)',
                            $interval->y,
                            $interval->m,
                            $interval->d
                        );
                    }
                    $formattedVal = "{$val} {$elapsedStr}";
                } else {
                    $formattedVal = $val ?? '-';
                }

                fputcsv($file, [$field->label, $formattedVal]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCsv(CustomForm $form)
    {
        $this->checkAccess($form);
        $form->load(['fields', 'submissions']);

        $headers = [
            'Content-type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=form_{$form->id}_submissions.csv",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $callback = function() use ($form) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel Arabic / Multilingual compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV Header row
            $header = [
                __('messages.Submission ID') ?? 'Submission ID',
                __('messages.Submitted At') ?? 'Submitted At',
                __('messages.Submitted By') ?? 'Submitted By',
            ];
            foreach ($form->fields as $field) {
                if (in_array($field->type, ['section_header', 'static_text'])) continue;
                $header[] = $field->label;
            }
            fputcsv($file, $header);

            // CSV Data rows
            foreach ($form->submissions as $submission) {
                $row = [
                    $submission->id,
                    $submission->created_at->format('Y-m-d H:i:s'),
                    $submission->user ? $submission->user->name : (__('messages.Guest') ?? 'Guest')
                ];
                foreach ($form->fields as $field) {
                    if (in_array($field->type, ['section_header', 'static_text'])) continue;
                    $val = $submission->data[$field->id] ?? '';
                    if (is_array($val)) {
                        if ($field->type === 'yes_no_textbox') {
                            $ans = $val['answer'] ?? '';
                            $det = $val['details'] ?? '';
                            $ansText = $ans === 'yes' ? (__('messages.yes') ?? 'Yes') : ($ans === 'no' ? (__('messages.no') ?? 'No') : $ans);
                            $val = $ansText . ($det ? " (" . (__('messages.Details / Explanation') ?? 'Details') . ": {$det})" : '');
                        } elseif ($field->type === 'table') {
                            $rowStrings = [];
                            foreach ($val as $r) {
                                if (is_array($r)) {
                                    $colPairs = [];
                                    foreach ($r as $colKey => $colVal) {
                                        $colPairs[] = "$colKey: $colVal";
                                    }
                                    $rowStrings[] = "[" . implode(', ', $colPairs) . "]";
                                }
                            }
                            $val = implode(' ; ', $rowStrings);
                        } else {
                            $val = implode(', ', $val);
                        }
                    } elseif ($field->type === 'date' && !empty($val) && $val !== '-' && strtotime($val)) {
                        $submittedDate = new \DateTime($val);
                        $now = new \DateTime();
                        $interval = $submittedDate->diff($now);
                        $locale = app()->getLocale();
                        if ($submittedDate > $now) {
                            $elapsedStr = $locale === 'ar' ? 'في المستقبل' : 'in the future';
                        } else {
                            $elapsedStr = sprintf(
                                $locale === 'ar' ? '(%d سنة، %d شهر، %d يوم)' : '(%d years, %d months, %d days)',
                                $interval->y,
                                $interval->m,
                                $interval->d
                            );
                        }
                        $val = "{$val} {$elapsedStr}";
                    }
                    $row[] = (string)$val;
                }
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
