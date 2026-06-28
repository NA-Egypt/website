<?php

namespace App\Http\Controllers;

use App\Models\CommitteeReport;
use App\Models\ServiceCommittee;
use App\Models\CommitteeReportAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommitteeReportController extends Controller
{
    use AuthorizesRequests;

    protected function getServiceCommittee()
    {
        $user = Auth::user();
        if (!$user) return null;
        
        return ServiceCommittee::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();
    }

    protected function isRsc()
    {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        return $user->hasRole('super admin') || $user->hasRole('rsc');
    }

    public function index(Request $request)
    {
        $isRsc = $this->isRsc();
        $query = CommitteeReport::with('serviceCommittee');
        $user = Auth::user();

        if ($this->isRestrictedConsumer($user)) {
            $query->where('status', 'approved');
            $threshold = now()->day >= 10 ? now() : now()->subMonth();
            $query->where(function($q) use ($threshold) {
                $q->whereYear('meeting_date', '<', $threshold->year)
                  ->orWhere(function($sub) use ($threshold) {
                      $sub->whereYear('meeting_date', $threshold->year)
                          ->whereMonth('meeting_date', '<', $threshold->month);
                  });
            });
        } elseif (!$isRsc) {
            $committee = $this->getServiceCommittee();
            if (!$committee) {
                abort(403, 'You are not assigned to any Service Committee.');
            }
            $query->where('service_committee_id', $committee->id);
        } else {
            // RSC can see both submitted and approved reports in their management dashboard.
            // Super admins can also see draft reports.
            if (Auth::user()->hasRole('super admin')) {
                $query->whereIn('status', ['draft', 'submitted', 'approved']);
            } else {
                $query->whereIn('status', ['submitted', 'approved']);
            }
            
            // RSC Filters
            if ($request->has('committee_id') && $request->committee_id) {
                $query->where('service_committee_id', $request->committee_id);
            }
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('meeting_day_description', 'like', "%$search%")
                  ->orWhereDate('meeting_date', $search); // exact date match
            });
        }

        $reports = $query->latest('meeting_date')->paginate(10);
        
        $committees = $isRsc ? ServiceCommittee::all() : [];

        return view('reports.index', compact('reports', 'isRsc', 'committees'));
    }

    public function create()
    {
        $isRsc = $this->isRsc();
        $committee = $this->getServiceCommittee();
        $committees = [];

        if ($isRsc) {
            $committees = ServiceCommittee::all();
        } elseif (!$committee) {
            abort(403, 'Only Committee members can create reports.');
        }

        return view('reports.create', compact('committee', 'committees', 'isRsc'));
    }

    public function store(Request $request)
    {
        $isRsc = $this->isRsc();
        $committee = $this->getServiceCommittee();

        if (!$isRsc && !$committee) {
             abort(403, 'Unauthorized');
        }

        $request->validate([
            'service_committee_id' => $isRsc ? 'required|exists:service_committees,id' : 'nullable',
            'meeting_date' => 'required|date',
            'meeting_day_description' => 'required|string|max:255',
            'sections' => 'required|array|min:1',
            'sections.*.headline' => 'nullable|string|max:255',
            'sections.*.content' => 'required|string',
            'positions' => 'nullable|array',
            'positions.*.name' => 'required|string',
            'positions.*.status' => 'required|string',
            'positions.*.election' => 'nullable',
            'status' => 'required|in:draft,submitted,approved',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:pdf,png,jpg,jpeg,docx,xlsx|max:5120',
            'is_exceptional' => 'nullable|boolean',
            'attended_members' => 'nullable|string',
            'footer' => 'nullable|string|max:1000',
        ]);

        $committeeId = $isRsc ? $request->service_committee_id : $committee->id;
        $positionsStatus = $request->positions;

        $report = CommitteeReport::create([
            'service_committee_id' => $committeeId,
            'meeting_date' => $request->meeting_date,
            'meeting_day_description' => $request->meeting_day_description,
            'body' => json_encode($request->sections),
            'positions_status' => $positionsStatus,
            'status' => $request->status,
            'report_date' => now()->toDateString(),
            'is_exceptional' => $request->boolean('is_exceptional'),
            'attended_members' => $request->attended_members,
            'footer' => $request->footer,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file && $file->isValid()) {
                    // Store in a private directory inside storage (not public)
                    $path = $file->store('report_attachments');
                    CommitteeReportAttachment::create([
                        'committee_report_id' => $report->id,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }
        }

        if ($report->status === 'submitted') {
            $this->sendNotificationEmail($report);
        }

        if ($report->status === 'approved') {
            app(\App\Services\ReportArchiver::class)->archive($report);
        }

        return redirect()->route('committee-reports.index')->with('success', __('messages.report_created_success'));
    }

    protected function isRestrictedConsumer($user)
    {
        if (!$user) {
            return true;
        }
        return $user->hasRole('ServiceBody') || $user->hasRole('gsr');
    }

    protected function isReportVisibleToUser($report)
    {
        $user = auth()->user();

        $committee = $this->getServiceCommittee();
        if ($committee && $committee->id === $report->service_committee_id) {
            return true;
        }

        // If they are a ServiceBody user or GSR/group user:
        if ($this->isRestrictedConsumer($user)) {
            // Only approved reports are visible
            if ($report->status !== 'approved') {
                return false;
            }

            // Available on the 10th of the month following the meeting date
            $meetingDate = $report->meeting_date;
            $publishDate = \Carbon\Carbon::parse($meetingDate)->addMonth()->day(10)->startOfDay();
            if (now()->lt($publishDate)) {
                return false;
            }
            return true;
        }

        // For other users (e.g. other committees): they can see submitted and approved reports at any time
        return in_array($report->status, ['submitted', 'approved']);
    }

    public function show($id)
    {
        $report = CommitteeReport::with(['serviceCommittee', 'attachments'])->findOrFail($id);
        
        if ($this->isRsc()) {
            if (!Auth::user()->hasRole('super admin') && !in_array($report->status, ['submitted', 'approved'])) {
                abort(403, 'Unauthorized');
            }
        } else {
            if (!$this->isReportVisibleToUser($report)) {
                abort(403, 'Unauthorized');
            }
        }

        return view('reports.show', compact('report'));
    }

    public function edit($id)
    {
        $report = CommitteeReport::with(['serviceCommittee', 'attachments'])->findOrFail($id);

        if (!$this->isRsc()) {
            $committee = $this->getServiceCommittee();
            if (!$committee || $committee->id !== $report->service_committee_id) {
                abort(403, 'Unauthorized');
            }
        }

        // Only drafts can be edited by committee members
        if (!$this->isRsc() && $report->status !== 'draft') {
            return redirect()->route('committee-reports.show', $report->id)
                ->with('error', __('messages.submitted_reports_cannot_be_edited'));
        }

        $committees = $this->isRsc() ? ServiceCommittee::all() : [];
        $committee = !$this->isRsc() ? $this->getServiceCommittee() : null;

        return view('reports.edit', compact('report', 'committee', 'committees', 'isRsc'));
    }

    public function update(Request $request, $id)
    {
        $report = CommitteeReport::findOrFail($id);

        if (!$this->isRsc()) {
            $committee = $this->getServiceCommittee();
            if (!$committee || $committee->id !== $report->service_committee_id) {
                abort(403, 'Unauthorized');
            }
        }

        if (!$this->isRsc() && $report->status !== 'draft') {
            return redirect()->route('committee-reports.show', $report->id)
                ->with('error', __('messages.submitted_reports_cannot_be_edited'));
        }

        $isRsc = $this->isRsc();

        $request->validate([
            'service_committee_id' => $isRsc ? 'required|exists:service_committees,id' : 'nullable',
            'meeting_date' => 'required|date',
            'meeting_day_description' => 'required|string|max:255',
            'sections' => 'required|array|min:1',
            'sections.*.headline' => 'nullable|string|max:255',
            'sections.*.content' => 'required|string',
            'positions' => 'nullable|array',
            'positions.*.name' => 'required|string',
            'positions.*.status' => 'required|string',
            'positions.*.election' => 'nullable',
            'status' => 'required|in:draft,submitted,approved',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:pdf,png,jpg,jpeg,docx,xlsx|max:5120',
            'is_exceptional' => 'nullable|boolean',
            'attended_members' => 'nullable|string',
            'footer' => 'nullable|string|max:1000',
        ]);

        $committeeId = $isRsc ? $request->service_committee_id : $report->service_committee_id;
        $positionsStatus = $request->positions;
        
        // Check if adding new attachments exceeds the limit
        if ($request->hasFile('attachments')) {
            $validFiles = array_filter($request->file('attachments'), function($file) {
                return $file && $file->isValid();
            });
            $currentCount = $report->attachments()->count();
            $newCount = count($validFiles);
            if ($currentCount + $newCount > 5) {
                return redirect()->back()->withErrors(['attachments' => 'A report can have a maximum of 5 attachments.'])->withInput();
            }

            foreach ($validFiles as $file) {
                $path = $file->store('report_attachments');
                CommitteeReportAttachment::create([
                    'committee_report_id' => $report->id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        $wasDraft = $report->status === 'draft';

        $report->update([
            'service_committee_id' => $committeeId,
            'meeting_date' => $request->meeting_date,
            'meeting_day_description' => $request->meeting_day_description,
            'body' => json_encode($request->sections),
            'positions_status' => $positionsStatus,
            'status' => $request->status,
            'is_exceptional' => $request->boolean('is_exceptional'),
            'attended_members' => $request->attended_members,
            'footer' => $request->footer,
        ]);

        if ($wasDraft && $report->status === 'submitted') {
            $this->sendNotificationEmail($report);
        }

        if ($report->status === 'approved') {
            app(\App\Services\ReportArchiver::class)->archive($report->fresh());
        }

        return redirect()->route('committee-reports.index')->with('success', __('messages.report_updated_success'));
    }

    public function destroy($id)
    {
        $report = CommitteeReport::findOrFail($id);

        if (!$this->isRsc()) {
            $committee = $this->getServiceCommittee();
            if (!$committee || $committee->id !== $report->service_committee_id) {
                abort(403, 'Unauthorized');
            }
            if ($report->status !== 'draft') {
                abort(403, 'Only drafts can be deleted.');
            }
        }

        // Delete physical files
        foreach ($report->attachments as $attachment) {
            if (Storage::exists($attachment->file_path)) {
                Storage::delete($attachment->file_path);
            }
            $attachment->delete();
        }

        $report->delete();

        return redirect()->route('committee-reports.index')->with('success', __('messages.report_deleted_success'));
    }

    public function pdf($id)
    {
        $report = CommitteeReport::with('serviceCommittee')->findOrFail($id);

        if ($this->isRsc()) {
            if (!Auth::user()->hasRole('super admin') && !in_array($report->status, ['submitted', 'approved'])) {
                abort(403, 'Unauthorized');
            }
        } else {
            if (!$this->isReportVisibleToUser($report)) {
                abort(403, 'Unauthorized');
            }
        }

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
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

        $reports = collect([$report]);
        $html = view('reports.pdf', compact('reports'))->render();
        $mpdf->WriteHTML($html);

        $filename = 'report_' . $report->id . '_' . ($report->report_date ? $report->report_date->format('Y-m-d') : $report->created_at->format('Y-m-d')) . '.pdf';

        $disposition = request()->query('disposition', 'attachment');
        if (!in_array($disposition, ['attachment', 'inline'])) {
            $disposition = 'attachment';
        }

        return response($mpdf->Output($filename, 'S'), 200)
               ->header('Content-Type', 'application/pdf')
               ->header('Content-Disposition', $disposition . '; filename="' . $filename . '"');
    }

    public function exportReportsPdf(Request $request)
    {
        $reportIds = $request->input('report_ids', []);
        
        if (empty($reportIds)) {
            return back()->with('error', __('messages.no_reports_selected') ?? 'No reports selected for export.');
        }

        $query = CommitteeReport::whereIn('id', $reportIds)->with('serviceCommittee');
        
        if (!$this->isRsc()) {
            $committee = $this->getServiceCommittee();
            $committeeId = $committee ? $committee->id : 0;
            $now = now();
            $user = auth()->user();
            $query->where(function($q) use ($committeeId, $now, $user) {
                if ($committeeId) {
                    $q->where('service_committee_id', $committeeId);
                }
                if ($this->isRestrictedConsumer($user)) {
                    $q->orWhere(function($sub) use ($now) {
                        $sub->where('status', 'approved');
                        $threshold = $now->day >= 10 ? $now : $now->copy()->subMonth();
                        $sub->where(function($dateQ) use ($threshold) {
                            $dateQ->whereYear('meeting_date', '<', $threshold->year)
                                  ->orWhere(function($inner) use ($threshold) {
                                      $inner->whereYear('meeting_date', $threshold->year)
                                            ->whereMonth('meeting_date', '<', $threshold->month);
                                  });
                        });
                    });
                } else {
                    $q->orWhereIn('status', ['submitted', 'approved']);
                }
            });
        } else {
            if (Auth::user()->hasRole('super admin')) {
                $query->whereIn('status', ['draft', 'submitted', 'approved']);
            } else {
                $query->whereIn('status', ['submitted', 'approved']);
            }
        }

        $reports = $query->orderBy('meeting_date', 'desc')->get();

        if ($reports->isEmpty()) {
            return back()->with('error', __('messages.no_reports_selected') ?? 'No valid reports found for export.');
        }

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
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

        $html = view('reports.pdf', compact('reports'))->render();
        $mpdf->WriteHTML($html);

        $filename = 'reports_export_' . date('Y-m-d') . '.pdf';
        
        return response($mpdf->Output($filename, 'S'), 200)
               ->header('Content-Type', 'application/pdf')
               ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function send($id)
    {
        $report = CommitteeReport::findOrFail($id);

        $committee = $this->getServiceCommittee();
        if (!$this->isRsc() && (!$committee || $committee->id !== $report->service_committee_id)) {
            abort(403, 'Unauthorized');
        }

        if ($report->status !== 'draft') {
            return redirect()->back()->with('error', __('messages.report_already_submitted'));
        }

        $report->update(['status' => 'submitted']);
        $this->sendNotificationEmail($report);
        
        return redirect()->back()->with('success', __('messages.report_approved_sent_rsc'));
    }

    public function downloadAttachment($id)
    {
        $attachment = CommitteeReportAttachment::findOrFail($id);
        $report = $attachment->committeeReport;

        // Enforce same view permission checks as for the report
        if ($this->isRsc()) {
            if (!Auth::user()->hasRole('super admin') && !in_array($report->status, ['submitted', 'approved'])) {
                abort(403, 'Unauthorized');
            }
        } else {
            if (!$this->isReportVisibleToUser($report)) {
                abort(403, 'Unauthorized');
            }
        }

        if (!Storage::exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        $disposition = request()->query('disposition', 'attachment');
        if (!in_array($disposition, ['attachment', 'inline'])) {
            $disposition = 'attachment';
        }

        if ($disposition === 'inline') {
            return response()->file(Storage::path($attachment->file_path), [
                'Content-Type' => $attachment->mime_type,
                'Content-Disposition' => 'inline; filename="' . $attachment->original_name . '"',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        return Storage::download($attachment->file_path, $attachment->original_name, [
            'Content-Type' => $attachment->mime_type,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function deleteAttachment($id)
    {
        $attachment = CommitteeReportAttachment::findOrFail($id);
        $report = $attachment->committeeReport;

        // Only the owning committee can delete their draft attachments
        $committee = $this->getServiceCommittee();
        if (!$committee || $committee->id !== $report->service_committee_id) {
            abort(403, 'Unauthorized');
        }

        if ($report->status !== 'draft') {
            abort(403, 'Cannot delete attachments from a submitted report.');
        }

        if (Storage::exists($attachment->file_path)) {
            Storage::delete($attachment->file_path);
        }

        $attachment->delete();

        return redirect()->back()->with('success', __('messages.attachment_deleted_success'));
    }

    public function archive(Request $request)
    {
        // 1. Get database reports
        $query = CommitteeReport::with(['serviceCommittee', 'attachments']);
        $now = now();
        $user = auth()->user();

        if (!$this->isRsc()) {
            $committee = $this->getServiceCommittee();
            $committeeId = $committee ? $committee->id : null;
            $query->where(function($q) use ($committeeId, $now, $user) {
                if ($committeeId) {
                    $q->where('service_committee_id', $committeeId);
                }
                if ($this->isRestrictedConsumer($user)) {
                    $q->orWhere(function($sub) use ($now) {
                        $sub->where('status', 'approved');
                        $threshold = $now->day >= 10 ? $now : $now->copy()->subMonth();
                        $sub->where(function($dateQ) use ($threshold) {
                            $dateQ->whereYear('meeting_date', '<', $threshold->year)
                                  ->orWhere(function($inner) use ($threshold) {
                                      $inner->whereYear('meeting_date', $threshold->year)
                                            ->whereMonth('meeting_date', '<', $threshold->month);
                                  });
                        });
                    });
                } else {
                    $q->orWhereIn('status', ['submitted', 'approved']);
                }
            });
        } else {
            if ($user && $user->hasRole('super admin')) {
                $query->whereIn('status', ['draft', 'submitted', 'approved']);
            } else {
                $query->whereIn('status', ['submitted', 'approved']);
            }
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('meeting_day_description', 'like', "%$search%")
                  ->orWhere('body', 'like', "%$search%");
            });
        }

        if ($request->has('committee_id') && $request->committee_id != '') {
            $query->where('service_committee_id', $request->committee_id);
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->where('meeting_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->where('meeting_date', '<=', $request->end_date);
        }

        if ($request->has('exceptional') && $request->exceptional == '1') {
            $query->where('is_exceptional', true);
        }

        $dbReports = $query->orderBy('meeting_date', 'desc')->get();

        // 2. Build directories and files from storagebox disk under "Archives"
        $cacheKey = 'storagebox_archive_files_list';
        if ($request->query('refresh') == '1') {
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
        }

        $allStorageboxFiles = \Illuminate\Support\Facades\Cache::remember($cacheKey, 43200, function () {
            $list = [];
            try {
                if (\Illuminate\Support\Facades\Storage::disk('storagebox')->exists('')) {
                    $allFiles = \Illuminate\Support\Facades\Storage::disk('storagebox')->allFiles('');
                    foreach ($allFiles as $filePath) {
                        if (str_starts_with(basename($filePath), '.') || str_contains($filePath, '/.')) {
                            continue;
                        }
                        try {
                            $size = \Illuminate\Support\Facades\Storage::disk('storagebox')->size($filePath);
                        } catch (\Exception $e) {
                            $size = 0;
                        }

                        $list[] = [
                            'name' => basename($filePath),
                            'path' => $filePath,
                            'size' => $size,
                        ];
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to list files from storagebox Archives: " . $e->getMessage());
            }
            return $list;
        });

        $committees = ServiceCommittee::all();
        $filesAndDirs = [];
        foreach ($allStorageboxFiles as $fileInfo) {
            $filePath = $fileInfo['path'];

            // Committee filter for storagebox files
            if ($request->has('committee_id') && $request->committee_id != '') {
                $selectedCommittee = $committees->firstWhere('id', $request->committee_id);
                if ($selectedCommittee) {
                    $arName = $selectedCommittee->ar_name ? mb_strtolower($selectedCommittee->ar_name, 'UTF-8') : null;
                    $enName = $selectedCommittee->en_name ? mb_strtolower($selectedCommittee->en_name, 'UTF-8') : null;
                    $filePathLower = mb_strtolower($filePath, 'UTF-8');
                    $fileNameLower = mb_strtolower($fileInfo['name'], 'UTF-8');
                    
                    $match = false;
                    if ($arName && (str_contains($filePathLower, $arName) || str_contains($fileNameLower, $arName))) {
                        $match = true;
                    }
                    if ($enName && (str_contains($filePathLower, $enName) || str_contains($fileNameLower, $enName))) {
                        $match = true;
                    }
                    if (!$match) {
                        continue;
                    }
                }
            }

            // Search filter
            if ($request->has('search') && $request->search != '') {
                $search = mb_strtolower($request->search, 'UTF-8');
                $filename = mb_strtolower($fileInfo['name'], 'UTF-8');
                $pathLower = mb_strtolower($filePath, 'UTF-8');
                if (!str_contains($filename, $search) && !str_contains($pathLower, $search)) {
                    continue;
                }
            }

            // Extract year for filtering
            $fileYear = null;
            if (preg_match('/([12]\d{3})/', $filePath, $yearMatches)) {
                $fileYear = (int)$yearMatches[1];
            }

            if ($request->has('start_date') && $request->start_date != '') {
                $startDateYear = date('Y', strtotime($request->start_date));
                if ($fileYear && $fileYear < $startDateYear) {
                    continue;
                }
            }
            if ($request->has('end_date') && $request->end_date != '') {
                $endDateYear = date('Y', strtotime($request->end_date));
                if ($fileYear && $fileYear > $endDateYear) {
                    continue;
                }
            }

            $prefixedPath = str_starts_with($filePath, 'Archives/') ? $filePath : 'Archives/' . $filePath;

            $filesAndDirs[] = [
                'is_dir' => false,
                'name' => $fileInfo['name'],
                'path' => $prefixedPath,
                'encrypted_path' => \Illuminate\Support\Facades\Crypt::encryptString($filePath),
                'size' => $fileInfo['size'],
            ];
        }

        // Add database reports virtual files if they match filters
        $archiver = app(\App\Services\ReportArchiver::class);
        foreach ($dbReports as $report) {
            $period = $archiver->getTargetMeetingPeriod($report->meeting_date);
            $targetYear = $period['year'];
            $arabicMonth = $period['arabic_month'];
            $committeeName = $report->serviceCommittee ? $report->serviceCommittee->ar_name : '';
            $committeeName = str_replace(['/', '\\', "\0"], '', $committeeName);

            $dateStr = $report->meeting_date->format('Y-m-d');
            $filename = sprintf('report_%d_%s.pdf', $report->id, $dateStr);
            $baseFolder = "Archives/أجندة إجتماع لجنة خدمة الاقليم/{$targetYear}/أجندة {$arabicMonth} {$targetYear}/التقارير الشهرية حتى 10 {$arabicMonth} {$targetYear}/{$committeeName}";
            $virtualPath = "{$baseFolder}/{$filename}";

            // Filter virtual reports by search/date matches if requested
            if ($request->has('search') && $request->search != '') {
                $search = mb_strtolower($request->search, 'UTF-8');
                $title = mb_strtolower($report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? '', 'UTF-8');
                $desc = mb_strtolower($report->meeting_day_description ?? '', 'UTF-8');
                if (!str_contains($title, $search) && !str_contains($desc, $search)) {
                    continue;
                }
            }

            $filesAndDirs[] = [
                'is_dir' => false,
                'name' => ($report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? 'Committee Report') . ' - ' . $dateStr . ' (' . $report->meeting_day_description . ').pdf',
                'path' => $virtualPath,
                'db_report_id' => $report->id,
                'size' => 0, // dynamic
            ];

            // Add attachments
            foreach ($report->attachments as $attachment) {
                $originalName = basename($attachment->original_name);
                $attFilename = sprintf('attachment_%d_%s', $attachment->id, $originalName);
                $attVirtualPath = "{$baseFolder}/المرفقات/{$attFilename}";

                $filesAndDirs[] = [
                    'is_dir' => false,
                    'name' => $originalName,
                    'path' => $attVirtualPath,
                    'db_attachment_id' => $attachment->id,
                    'size' => $attachment->file_size,
                ];
            }
        }

        // 3. Build a structured tree directory matching Archives structure
        $tree = [];

        foreach ($filesAndDirs as $file) {
            $parts = explode('/', $file['path']);
            // Remove 'Archives' root folder from path parts to display children of 'Archives' at root level
            if (isset($parts[0]) && $parts[0] === 'Archives') {
                array_shift($parts);
            }

            if (empty($parts)) {
                continue;
            }

            $currentLevel = &$tree;
            $accumulatedPath = 'Archives';

            for ($i = 0; $i < count($parts) - 1; $i++) {
                $dirName = $parts[$i];
                $accumulatedPath .= '/' . $dirName;
                if (!isset($currentLevel[$dirName])) {
                    $currentLevel[$dirName] = [
                        'is_dir' => true,
                        'name' => $dirName,
                        'path' => $accumulatedPath,
                        'children' => [],
                    ];
                }
                $currentLevel = &$currentLevel[$dirName]['children'];
            }

            $fileName = end($parts);
            $currentLevel[$fileName] = [
                'is_dir' => false,
                'name' => $file['name'],
                'path' => $file['path'],
                'encrypted_path' => $file['encrypted_path'] ?? null,
                'db_report_id' => $file['db_report_id'] ?? null,
                'db_attachment_id' => $file['db_attachment_id'] ?? null,
                'size' => $file['size'],
            ];
        }

        // Helper function to recursively convert associative children arrays to indexed lists, sorted by folders first, then alphabetically
        $sanitizeTree = function ($tree) use (&$sanitizeTree) {
            $result = [];
            foreach ($tree as $key => $node) {
                if ($node['is_dir']) {
                    $node['children'] = $sanitizeTree($node['children']);
                }
                $result[] = $node;
            }

            usort($result, function ($a, $b) {
                if ($a['is_dir'] && !$b['is_dir']) return -1;
                if (!$a['is_dir'] && $b['is_dir']) return 1;
                return strcasecmp($a['name'], $b['name']);
            });

            return $result;
        };

        $archiveTree = $sanitizeTree($tree);

        return view('reports.archive', compact('archiveTree', 'committees'));
    }

    public function downloadStorageboxFile(Request $request)
    {
        $encryptedPath = $request->query('file');
        if (!$encryptedPath) {
            abort(400, 'Missing file parameter.');
        }

        try {
            $filePath = \Illuminate\Support\Facades\Crypt::decryptString($encryptedPath);
            
            // Check if file exists on storagebox disk
            if (!\Illuminate\Support\Facades\Storage::disk('storagebox')->exists($filePath)) {
                abort(404, 'File not found on storage box.');
            }

            // Path & File System Security check: ensure path is safe
            $safePath = basename($filePath);
            
            $disposition = request()->query('disposition', 'attachment');
            if (!in_array($disposition, ['attachment', 'inline'])) {
                $disposition = 'attachment';
            }

            if ($disposition === 'inline') {
                return response()->file(\Illuminate\Support\Facades\Storage::disk('storagebox')->path($filePath), [
                    'Content-Type' => \Illuminate\Support\Facades\Storage::disk('storagebox')->mimeType($filePath) ?? 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $safePath . '"',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            }

            return \Illuminate\Support\Facades\Storage::disk('storagebox')->download(
                $filePath,
                $safePath,
                [
                    'Content-Type' => \Illuminate\Support\Facades\Storage::disk('storagebox')->mimeType($filePath) ?? 'application/octet-stream',
                    'X-Content-Type-Options' => 'nosniff',
                ]
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to decrypt or download file from storagebox: " . $e->getMessage());
            abort(403, 'Invalid download request.');
        }
    }

    public function approveAndSend($id)
    {
        if (!$this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $report = CommitteeReport::findOrFail($id);
        
        if ($report->status !== 'submitted') {
            return redirect()->back()->with('error', __('messages.only_submitted_reports_approved'));
        }

        $report->update([
            'status' => 'approved',
            'review_notes' => null, // clear notes upon approval
        ]);

        app(\App\Services\ReportArchiver::class)->archive($report);

        return redirect()->route('committee-reports.index')->with('success', __('messages.report_approved_published'));
    }

    public function returnToDraft(Request $request, $id)
    {
        if (!$this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'review_notes' => 'required|string|max:1000',
        ]);

        $report = CommitteeReport::findOrFail($id);

        if ($report->status !== 'submitted') {
            return redirect()->back()->with('error', __('messages.only_submitted_reports_returned_draft'));
        }

        $report->update([
            'status' => 'draft',
            'review_notes' => $request->review_notes,
        ]);

        return redirect()->route('committee-reports.index')->with('success', __('messages.report_returned_draft_notes'));
    }

    protected function sendNotificationEmail($report)
    {
        try {
            $committeeName = $report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? 'Unknown Committee';
            $meetingDate = $report->meeting_date->format('Y-m-d');
            
            Mail::send([], [], function ($message) use ($committeeName, $meetingDate) {
                $message->to(['rsc@naegypt.org', 'arsc@naegypt.org'])
                        ->subject("New Committee Report: {$committeeName} ({$meetingDate})")
                        ->html("
                            <p>Dear RSC,</p>
                            <p>A new committee report has been submitted by the committee <strong>{$committeeName}</strong> for the meeting date <strong>{$meetingDate}</strong>.</p>
                            <p>You can view and review this report in the management dashboard or archive.</p>
                            <p>Regards,<br>NA Egypt System</p>
                        ");
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send committee report email alert: " . $e->getMessage());
        }
    }
}
