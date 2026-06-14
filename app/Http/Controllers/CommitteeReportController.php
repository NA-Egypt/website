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
            $now = now();
            if ($now->day < 10) {
                $query->where(function($dateQ) use ($now) {
                    $dateQ->whereYear('meeting_date', '<', $now->year)
                          ->orWhere(function($inner) use ($now) {
                              $inner->whereYear('meeting_date', $now->year)
                                    ->whereMonth('meeting_date', '<', $now->month);
                          });
                });
            }
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
            'attachments' => 'nullable|array|max:3',
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

        if ($report->status === 'submitted') {
            $this->sendNotificationEmail($report);
        }

        if ($report->status === 'approved') {
            app(\App\Services\ReportArchiver::class)->archive($report);
        }

        return redirect()->route('committee-reports.index')->with('success', 'Report created successfully.');
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

            // Available on the 10th of every month
            $meetingDate = $report->meeting_date;
            $now = now();
            if ($meetingDate->year === $now->year && $meetingDate->month === $now->month) {
                if ($now->day < 10) {
                    return false;
                }
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
                ->with('error', 'Submitted reports cannot be edited.');
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
                ->with('error', 'Submitted reports cannot be edited.');
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
            'attachments' => 'nullable|array|max:3',
            'attachments.*' => 'file|mimes:pdf,png,jpg,jpeg,docx,xlsx|max:5120',
            'is_exceptional' => 'nullable|boolean',
            'attended_members' => 'nullable|string',
            'footer' => 'nullable|string|max:1000',
        ]);

        $committeeId = $isRsc ? $request->service_committee_id : $report->service_committee_id;
        $positionsStatus = $request->positions;
        
        // Check if adding new attachments exceeds the limit
        if ($request->hasFile('attachments')) {
            $currentCount = $report->attachments()->count();
            $newCount = count($request->file('attachments'));
            if ($currentCount + $newCount > 3) {
                return redirect()->back()->withErrors(['attachments' => 'A report can have a maximum of 3 attachments.'])->withInput();
            }

            foreach ($request->file('attachments') as $file) {
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

        return redirect()->route('committee-reports.index')->with('success', 'Report updated successfully.');
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

        return redirect()->route('committee-reports.index')->with('success', 'Report deleted successfully.');
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

        $filename = 'committee_report_' . $report->meeting_date->format('Y-m-d') . '.pdf';
        
        return response($mpdf->Output($filename, 'S'), 200)
               ->header('Content-Type', 'application/pdf')
               ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
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
                        if ($now->day < 10) {
                            $sub->where(function($dateQ) use ($now) {
                                $dateQ->whereYear('meeting_date', '<', $now->year)
                                      ->orWhere(function($inner) use ($now) {
                                          $inner->whereYear('meeting_date', $now->year)
                                                ->whereMonth('meeting_date', '<', $now->month);
                                      });
                            });
                        }
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
            return redirect()->back()->with('error', 'Report is already submitted.');
        }

        $report->update(['status' => 'submitted']);
        $this->sendNotificationEmail($report);
        
        return redirect()->back()->with('success', 'Report approved and sent to Region Service Committee.');
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

        return redirect()->back()->with('success', 'Attachment deleted successfully.');
    }

    public function archive(Request $request)
    {
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
                        if ($now->day < 10) {
                            $sub->where(function($dateQ) use ($now) {
                                $dateQ->whereYear('meeting_date', '<', $now->year)
                                      ->orWhere(function($inner) use ($now) {
                                          $inner->whereYear('meeting_date', $now->year)
                                                ->whereMonth('meeting_date', '<', $now->month);
                                      });
                            });
                        }
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

        $reports = $query->orderBy('meeting_date', 'desc')->get();

        $legacyFiles = [];
        try {
            if (\Illuminate\Support\Facades\Storage::disk('storagebox')->exists('')) {
                $files = \Illuminate\Support\Facades\Storage::disk('storagebox')->allFiles();
                
                $arabicMonthsMap = [
                    'يناير' => '01', 'فبراير' => '02', 'مارس' => '03', 'أبريل' => '04', 'ابريل' => '04',
                    'مايو' => '05', 'يونيو' => '06', 'يوليو' => '07', 'أغسطس' => '08', 'اغسطس' => '08',
                    'سبتمبر' => '09', 'أكتوبر' => '10', 'اكتوبر' => '10', 'نوفمبر' => '11', 'ديسمبر' => '12'
                ];

                $englishMonthsMap = [
                    'january' => '01', 'february' => '02', 'march' => '03', 'april' => '04',
                    'may' => '05', 'june' => '06', 'july' => '07', 'august' => '08',
                    'september' => '09', 'october' => '10', 'november' => '11', 'december' => '12'
                ];

                foreach ($files as $filePath) {
                    // Skip hidden files
                    if (str_starts_with(basename($filePath), '.') || str_contains($filePath, '/.')) {
                        continue;
                    }

                    $year = null;
                    $month = null;

                    // 1. Try to extract 4-digit Year from the file path
                    if (preg_match('/([12]\d{3})/', $filePath, $yearMatches)) {
                        $year = $yearMatches[1];
                    }

                    // 2. Try to extract Month from the path (Arabic or English)
                    $lowerPath = strtolower($filePath);
                    foreach ($arabicMonthsMap as $monthName => $monthNum) {
                        if (str_contains($filePath, $monthName)) {
                            $month = $monthNum;
                            break;
                        }
                    }

                    if (!$month) {
                        foreach ($englishMonthsMap as $monthName => $monthNum) {
                            if (str_contains($lowerPath, $monthName)) {
                                $month = $monthNum;
                                break;
                            }
                        }
                    }

                    // 3. Fallback: use file modification time
                    if (!$year || !$month) {
                        try {
                            $mtime = \Illuminate\Support\Facades\Storage::disk('storagebox')->lastModified($filePath);
                            if (!$year) $year = date('Y', $mtime);
                            if (!$month) $month = date('m', $mtime);
                        } catch (\Exception $e) {
                            if (!$year) $year = date('Y');
                            if (!$month) $month = date('m');
                        }
                    }

                    // Search filter matching
                    if ($request->has('search') && $request->search != '') {
                        $search = strtolower($request->search);
                        if (!str_contains(strtolower($filePath), $search)) {
                            continue;
                        }
                    }

                    // Year filter
                    if ($request->has('start_date') && $request->start_date != '') {
                        $startDateYear = date('Y', strtotime($request->start_date));
                        if ($year < $startDateYear) {
                            continue;
                        }
                    }
                    if ($request->has('end_date') && $request->end_date != '') {
                        $endDateYear = date('Y', strtotime($request->end_date));
                        if ($year > $endDateYear) {
                            continue;
                        }
                    }

                    // Build standard representation
                    $legacyFiles[] = (object) [
                        'is_legacy' => true,
                        'title' => basename($filePath),
                        'relative_path' => $filePath,
                        'encrypted_path' => \Illuminate\Support\Facades\Crypt::encryptString($filePath),
                        'subtitle' => dirname($filePath),
                        'file_size' => \Illuminate\Support\Facades\Storage::disk('storagebox')->size($filePath),
                        'year' => $year,
                        'month' => $month,
                        'meeting_date' => \Carbon\Carbon::createFromDate($year, $month, 1),
                        'report_date' => \Carbon\Carbon::createFromDate($year, $month, 1),
                        'is_exceptional' => false,
                        'attachments' => collect(),
                    ];
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to list files from storagebox: " . $e->getMessage());
        }

        $unifiedArchive = collect();

        foreach ($reports as $report) {
            $unifiedArchive->push((object) [
                'is_legacy' => false,
                'id' => $report->id,
                'title' => $report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? 'Committee Report',
                'meeting_date' => $report->meeting_date,
                'report_date' => $report->report_date ?? $report->created_at,
                'is_exceptional' => $report->is_exceptional,
                'meeting_day_description' => $report->meeting_day_description,
                'attachments' => $report->attachments,
                'year' => $report->meeting_date->format('Y'),
                'month' => $report->meeting_date->format('m'),
            ]);
        }

        foreach ($legacyFiles as $file) {
            $unifiedArchive->push($file);
        }

        // Group by Year, then by Month, and sort keys/groups descending by meeting date
        $archive = $unifiedArchive->sortByDesc(function ($item) {
            return $item->meeting_date->timestamp;
        })->groupBy('year')->map(function ($yearGroup) {
            return $yearGroup->groupBy('month');
        });

        $committees = ServiceCommittee::all();

        return view('reports.archive', compact('archive', 'committees'));
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
            return redirect()->back()->with('error', 'Only submitted reports can be approved.');
        }

        $report->update([
            'status' => 'approved',
            'review_notes' => null, // clear notes upon approval
        ]);

        app(\App\Services\ReportArchiver::class)->archive($report);

        return redirect()->route('committee-reports.index')->with('success', 'Report approved and published successfully.');
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
            return redirect()->back()->with('error', 'Only submitted reports can be returned to draft.');
        }

        $report->update([
            'status' => 'draft',
            'review_notes' => $request->review_notes,
        ]);

        return redirect()->route('committee-reports.index')->with('success', 'Report returned to committee draft with review notes.');
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
