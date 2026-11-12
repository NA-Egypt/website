<?php

namespace App\Http\Controllers;

use App\Models\CommitteeReport;
use App\Models\ServiceCommittee;
use App\Models\CommitteeReportAttachment;
use App\Models\CommitteeReportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\MpdfService;
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
        
        if ($user->hasRole('rsc')) {
            $rscCommittee = ServiceCommittee::find(83) ?: ServiceCommittee::where('email', 'RSC@naegypt.org')->first();
            if ($rscCommittee) {
                return $rscCommittee;
            }
        }

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
        $isSuperAdmin = Auth::check() && Auth::user()->hasRole('super admin');
        $query = CommitteeReport::with(['serviceCommittee', 'parentReport', 'embeddedWorkgroupReports']);
        $user = Auth::user();
        $activeTab = $request->get('tab', 'committee');
        $childWorkgroups = collect();
        $allWorkgroups = collect();
        $unembeddedDraftsCount = 0;
        $committeeDraftsCount = 0;

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

            $childWorkgroups = $committee->workgroups;

            if ($childWorkgroups->isNotEmpty()) {
                $unembeddedDraftsCount = CommitteeReport::whereIn('service_committee_id', $childWorkgroups->pluck('id'))
                    ->unembeddedDrafts()
                    ->count();
            }

            if ($committee->isWorkgroup()) {
                // Strictly a workgroup: sees only their workgroup reports
                $query->where('service_committee_id', $committee->id);
            } else {
                // Parent committee
                if ($activeTab === 'workgroups' && $childWorkgroups->isNotEmpty()) {
                    if ($request->has('workgroup_id') && $request->filled('workgroup_id')) {
                        $query->where('service_committee_id', (int)$request->workgroup_id);
                    } else {
                        $query->whereIn('service_committee_id', $childWorkgroups->pluck('id'));
                    }
                } else {
                    $query->where('service_committee_id', $committee->id);
                }
            }
        } elseif ($isSuperAdmin) {
            // Super Admin can see drafts, submitted, approved, embedded reports across all committees and workgroups
            $unembeddedDraftsCount = CommitteeReport::whereHas('serviceCommittee', fn($q) => $q->whereNotNull('parent_id'))
                ->unembeddedDrafts()
                ->count();

            $committeeDraftsCount = CommitteeReport::whereHas('serviceCommittee', fn($q) => $q->whereNull('parent_id'))
                ->where('status', 'draft')
                ->count();

            $allWorkgroups = ServiceCommittee::whereNotNull('parent_id')->orderBy('ar_name')->get();

            // Status filter
            if ($request->filled('status') && in_array($request->status, ['draft', 'submitted', 'approved', 'embedded'])) {
                $query->where('status', $request->status);
            } else {
                $query->whereIn('status', ['draft', 'submitted', 'approved', 'embedded']);
            }

            // Tab separation
            if ($activeTab === 'workgroups') {
                $query->whereHas('serviceCommittee', fn($q) => $q->whereNotNull('parent_id'));
                if ($request->filled('workgroup_id')) {
                    $query->where('service_committee_id', (int)$request->workgroup_id);
                } elseif ($request->filled('committee_id')) {
                    $query->whereHas('serviceCommittee', fn($q) => $q->where('parent_id', (int)$request->committee_id));
                }
            } else {
                $query->whereHas('serviceCommittee', fn($q) => $q->whereNull('parent_id'));
                if ($request->filled('committee_id')) {
                    $query->where('service_committee_id', (int)$request->committee_id);
                }
            }
        } else {
            // RSC review queue should only show official committee reports (not standalone workgroup drafts)
            $query->committeesOnly()->whereIn('status', ['submitted', 'approved']);
            
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
        
        $committees = $isRsc ? ServiceCommittee::committeesOnly()->orderBy('ar_name')->get() : [];

        return view('reports.index', compact(
            'reports',
            'isRsc',
            'isSuperAdmin',
            'committees',
            'childWorkgroups',
            'allWorkgroups',
            'activeTab',
            'unembeddedDraftsCount',
            'committeeDraftsCount'
        ));
    }

    public function create()
    {
        $isRsc = $this->isRsc();
        $committee = $this->getServiceCommittee();
        $committees = [];

        if ($isRsc) {
            $committees = ServiceCommittee::committeesOnly()->orderBy('ar_name')->get();
        } elseif (!$committee) {
            abort(403, 'Only Committee members can create reports.');
        }

        $isWorkgroup = $committee && $committee->isWorkgroup();
        $childWorkgroups = ($committee && $committee->isCommittee()) ? $committee->workgroups : collect();
        $availableWorkgroupDrafts = collect();
        if ($childWorkgroups->isNotEmpty()) {
            $availableWorkgroupDrafts = CommitteeReport::with(['serviceCommittee', 'attachments'])
                ->whereIn('service_committee_id', $childWorkgroups->pluck('id'))
                ->unembeddedDrafts()
                ->latest('meeting_date')
                ->get();
        }

        return view('reports.create', compact('committee', 'committees', 'isRsc', 'isWorkgroup', 'availableWorkgroupDrafts'));
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
            'embedded_workgroup_report_ids' => 'nullable|array',
            'embedded_workgroup_report_ids.*' => 'exists:committee_reports,id',
        ]);

        $committeeId = $isRsc ? $request->service_committee_id : $committee->id;
        $targetCommittee = ServiceCommittee::find($committeeId);
        $positionsStatus = $request->positions;

        // Workgroup reports can only be saved as draft (internal to parent committee)
        $status = $request->status;
        if ($targetCommittee && $targetCommittee->isWorkgroup()) {
            $status = 'draft';
        }

        $report = CommitteeReport::create([
            'service_committee_id' => $committeeId,
            'meeting_date' => $request->meeting_date,
            'meeting_day_description' => $request->meeting_day_description,
            'body' => json_encode($request->sections),
            'positions_status' => $positionsStatus,
            'status' => $status,
            'report_date' => now()->toDateString(),
            'is_exceptional' => $request->boolean('is_exceptional'),
            'attended_members' => $request->attended_members,
            'footer' => $request->footer,
        ]);

        // Link embedded workgroup reports
        if ($request->filled('embedded_workgroup_report_ids') && is_array($request->embedded_workgroup_report_ids)) {
            CommitteeReport::whereIn('id', $request->embedded_workgroup_report_ids)
                ->update([
                    'parent_report_id' => $report->id,
                    'status' => 'embedded',
                ]);
        }

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
            app(\App\Services\ReportArchiver::class)->archive($report, true);
        }

        CommitteeReportLog::record(
            'created',
            $report,
            __('messages.report_created_log_summary', ['id' => $report->id, 'committee' => $targetCommittee->ar_name ?? $targetCommittee->en_name ?? 'N/A']),
            [
                'meeting_date' => $report->meeting_date ? $report->meeting_date->format('Y-m-d') : null,
                'status' => $report->status,
                'is_exceptional' => (bool)$report->is_exceptional,
                'attachment_count' => $request->hasFile('attachments') ? count($request->file('attachments')) : 0,
            ]
        );

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

        // Parent committee user can view child workgroups' reports
        if ($committee && $committee->workgroups()->where('id', $report->service_committee_id)->exists()) {
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
        $isRsc = $this->isRsc();

        if (!$isRsc) {
            $committee = $this->getServiceCommittee();
            if (!$committee || $committee->id !== $report->service_committee_id) {
                abort(403, 'Unauthorized');
            }
        }

        // Only drafts can be edited by committee members
        if (!$isRsc && $report->status !== 'draft') {
            return redirect()->route('committee-reports.show', $report->id)
                ->with('error', __('messages.submitted_reports_cannot_be_edited'));
        }

        $committees = $isRsc ? ServiceCommittee::committeesOnly()->orderBy('ar_name')->get() : [];
        $committee = !$isRsc ? $this->getServiceCommittee() : null;

        $isWorkgroup = ($committee && $committee->isWorkgroup()) || ($report->serviceCommittee && $report->serviceCommittee->isWorkgroup());
        $targetComm = $report->serviceCommittee ?: $committee;
        $childWorkgroups = ($targetComm && $targetComm->isCommittee()) ? $targetComm->workgroups : collect();
        $availableWorkgroupDrafts = collect();
        if ($childWorkgroups->isNotEmpty()) {
            $availableWorkgroupDrafts = CommitteeReport::with(['serviceCommittee', 'attachments'])
                ->whereIn('service_committee_id', $childWorkgroups->pluck('id'))
                ->where(function ($q) use ($report) {
                    $q->whereNull('parent_report_id')
                      ->orWhere('parent_report_id', $report->id);
                })
                ->whereIn('status', ['draft', 'embedded'])
                ->latest('meeting_date')
                ->get();
        }

        return view('reports.edit', compact('report', 'committee', 'committees', 'isRsc', 'isWorkgroup', 'availableWorkgroupDrafts'));
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
            'embedded_workgroup_report_ids' => 'nullable|array',
            'embedded_workgroup_report_ids.*' => 'exists:committee_reports,id',
        ]);

        $committeeId = $isRsc ? $request->service_committee_id : $report->service_committee_id;
        $targetCommittee = ServiceCommittee::find($committeeId);
        $positionsStatus = $request->positions;
        
        // Workgroup reports can only be saved as draft
        $status = $request->status;
        if ($targetCommittee && $targetCommittee->isWorkgroup()) {
            $status = 'draft';
        }

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
        $oldStatus = $report->status;
        $oldDate = $report->meeting_date ? $report->meeting_date->format('Y-m-d') : null;

        $report->update([
            'service_committee_id' => $committeeId,
            'meeting_date' => $request->meeting_date,
            'meeting_day_description' => $request->meeting_day_description,
            'body' => json_encode($request->sections),
            'positions_status' => $positionsStatus,
            'status' => $status,
            'is_exceptional' => $request->boolean('is_exceptional'),
            'attended_members' => $request->attended_members,
            'footer' => $request->footer,
        ]);

        // Two-way sync of embedded workgroup reports
        $newEmbeddedIds = $request->input('embedded_workgroup_report_ids', []);
        if (!is_array($newEmbeddedIds)) {
            $newEmbeddedIds = [];
        }
        
        // Detach any previously embedded workgroup reports that were un-embedded/removed
        CommitteeReport::where('parent_report_id', $report->id)
            ->whereNotIn('id', $newEmbeddedIds)
            ->update([
                'parent_report_id' => null,
                'status' => 'draft',
            ]);

        // Link currently embedded reports
        if (!empty($newEmbeddedIds)) {
            CommitteeReport::whereIn('id', $newEmbeddedIds)
                ->update([
                    'parent_report_id' => $report->id,
                    'status' => 'embedded',
                ]);
        }

        if ($wasDraft && $report->status === 'submitted') {
            $this->sendNotificationEmail($report);
        }

        if ($report->status === 'approved') {
            app(\App\Services\ReportArchiver::class)->archive($report->fresh(), true);
        }

        $newDate = $report->meeting_date ? $report->meeting_date->format('Y-m-d') : null;
        CommitteeReportLog::record(
            'updated',
            $report,
            __('messages.report_updated_log_summary', ['id' => $report->id, 'committee' => $report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? 'N/A']),
            [
                'meeting_date' => $newDate,
                'status' => $report->status,
                'status_changed' => $oldStatus !== $report->status ? "{$oldStatus} -> {$report->status}" : null,
                'date_changed' => $oldDate !== $newDate ? "{$oldDate} -> {$newDate}" : null,
                'is_exceptional' => (bool)$report->is_exceptional,
                'attachments_added' => $request->hasFile('attachments') ? count($request->file('attachments')) : 0,
            ]
        );

        return redirect()->route('committee-reports.index')->with('success', __('messages.report_updated_success'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('super admin')) {
            abort(403, 'Unauthorized. Only Super Admin can delete committee reports.');
        }

        $report = CommitteeReport::with(['serviceCommittee', 'attachments'])->findOrFail($id);

        if ($report->status !== 'draft') {
            return redirect()->back()->with('error', __('messages.only_draft_reports_can_be_deleted') ?? 'Only draft or returned reports can be deleted.');
        }

        $reportId = $report->id;
        $committeeId = $report->service_committee_id;
        $committeeName = $report->serviceCommittee ? ($report->serviceCommittee->ar_name ?: $report->serviceCommittee->en_name) : null;
        $meetingDate = $report->meeting_date ? $report->meeting_date->format('Y-m-d') : null;
        $attachmentCount = $report->attachments->count();
        $wasReturnedDraft = !empty($report->review_notes);

        // Release any embedded workgroup reports back to available drafts
        $report->embeddedWorkgroupReports()->update([
            'parent_report_id' => null,
            'status' => 'draft',
        ]);

        // Delete physical files
        foreach ($report->attachments as $attachment) {
            if (Storage::exists($attachment->file_path)) {
                Storage::delete($attachment->file_path);
            }
            $attachment->delete();
        }

        $report->delete();

        CommitteeReportLog::record(
            'deleted',
            [
                'id' => $reportId,
                'service_committee_id' => $committeeId,
            ],
            __('messages.report_deleted_log_summary', ['id' => $reportId, 'committee' => $committeeName ?? 'N/A']),
            [
                'meeting_date' => $meetingDate,
                'attachment_count' => $attachmentCount,
                'was_returned_draft' => $wasReturnedDraft,
            ]
        );

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

        $mpdf = MpdfService::create();

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

        $mpdf = MpdfService::create();

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

        if ($report->serviceCommittee && $report->serviceCommittee->isWorkgroup()) {
            return redirect()->back()->with('error', __('messages.Workgroup reports cannot be submitted directly to RSC'));
        }

        if ($report->status !== 'draft') {
            return redirect()->back()->with('error', __('messages.report_already_submitted'));
        }

        $report->update(['status' => 'submitted']);
        $this->sendNotificationEmail($report);

        CommitteeReportLog::record(
            'submitted',
            $report,
            __('messages.report_submitted_log_summary', ['id' => $report->id, 'committee' => $report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? 'N/A']),
            [
                'meeting_date' => $report->meeting_date ? $report->meeting_date->format('Y-m-d') : null,
            ]
        );
        
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
        $archiver = app(\App\Services\ReportArchiver::class);
        $syncService = app(\App\Services\OneDriveSyncService::class);
        $canManageArchive = $this->isRsc();

        // 1. Get database reports matching user visibility & filters
        $query = CommitteeReport::with(['serviceCommittee', 'attachments']);
        $now = now();
        $user = auth()->user();

        if (!$this->isRsc()) {
            $committee = $this->getServiceCommittee();
            $committeeId = $committee ? $committee->id : null;
            $query->where(function ($q) use ($committeeId, $now, $user) {
                if ($committeeId) {
                    $q->where('service_committee_id', $committeeId);
                }
                if ($this->isRestrictedConsumer($user)) {
                    $q->orWhere(function ($sub) use ($now) {
                        $sub->where('status', 'approved');
                        $threshold = $now->day >= 10 ? $now : $now->copy()->subMonth();
                        $sub->where(function ($dateQ) use ($threshold) {
                            $dateQ->whereYear('meeting_date', '<', $threshold->year)
                                  ->orWhere(function ($inner) use ($threshold) {
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

        $hasSearch = $request->filled('search');
        $hasCommittee = $request->filled('committee_id');
        $hasStartDate = $request->filled('start_date');
        $hasEndDate = $request->filled('end_date');
        $hasExceptional = $request->input('exceptional') == '1';
        $isFilterActive = $hasSearch || $hasCommittee || $hasStartDate || $hasEndDate || $hasExceptional;

        if ($hasSearch) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('meeting_day_description', 'like', "%$search%")
                  ->orWhere('body', 'like', "%$search%");
            });
        }

        if ($hasCommittee) {
            $query->where('service_committee_id', $request->committee_id);
        }

        if ($hasStartDate) {
            $query->where('meeting_date', '>=', $request->start_date);
        }

        if ($hasEndDate) {
            $query->where('meeting_date', '<=', $request->end_date);
        }

        if ($hasExceptional) {
            $query->where('is_exceptional', true);
        }

        $dbReports = $query->orderBy('meeting_date', 'desc')->get();

        // 2. Load indexed files and directories from StorageBox cache
        $cacheKey = 'storagebox_archive_files_list';
        $dirsCacheKey = 'storagebox_archive_dirs_list';

        if ($request->query('refresh') == '1') {
            exec('php ' . escapeshellarg(base_path('artisan')) . ' onedrive:sync-archives > /dev/null 2>&1 &');
            session()->flash('info', __('messages.archive_refresh_started'));
        }

        $allStorageboxFiles = \Illuminate\Support\Facades\Cache::get($cacheKey);
        if ($allStorageboxFiles === null) {
            exec('php ' . escapeshellarg(base_path('artisan')) . ' storagebox:cache-archives > /dev/null 2>&1 &');
            $allStorageboxFiles = [];
        }
        $allStorageboxDirs = \Illuminate\Support\Facades\Cache::get($dirsCacheKey, []);

        // Build lookup maps of all physical files before filtering so DB reports can deduplicate accurately
        $allPhysicalByTreePath = [];
        $allPhysicalByFilename = [];
        $allIndexedPaths = [];

        foreach ($allStorageboxDirs as $dirInfo) {
            $dPath = is_array($dirInfo) ? ($dirInfo['path'] ?? '') : (string) $dirInfo;
            if ($dPath !== '') {
                $allIndexedPaths[] = $dPath;
            }
        }

        foreach ($allStorageboxFiles as $fileInfo) {
            $rawPath = $fileInfo['path'] ?? '';
            if ($rawPath === '') {
                continue;
            }
            $allIndexedPaths[] = $rawPath;
            $treePath = $archiver->toTreePath($rawPath);
            $diskRelPath = $archiver->toDiskRelativePath($rawPath);

            $entry = [
                'is_dir' => false,
                'name' => $fileInfo['name'],
                'path' => $treePath,
                'encrypted_path' => \Illuminate\Support\Facades\Crypt::encryptString($diskRelPath),
                'size' => (int) ($fileInfo['size'] ?? 0),
                'mtime' => $fileInfo['mtime'] ?? null,
                'source' => 'storagebox',
            ];

            $allPhysicalByTreePath[$treePath] = $entry;
            $allPhysicalByFilename[$fileInfo['name']][] = $treePath;
        }

        // If user is a restricted consumer (ServiceBody / GSR), identify restricted current-month report filenames to hide
        $restrictedFilenames = [];
        if (!$this->isRsc() && $this->isRestrictedConsumer($user)) {
            $threshold = $now->day >= 10 ? $now : $now->copy()->subMonth();
            $restrictedReports = CommitteeReport::with('serviceCommittee')
                ->where('status', 'approved')
                ->where(function ($q) use ($threshold) {
                    $q->whereYear('meeting_date', '>', $threshold->year)
                      ->orWhere(function ($sub) use ($threshold) {
                          $sub->whereYear('meeting_date', $threshold->year)
                              ->whereMonth('meeting_date', '>=', $threshold->month);
                      });
                })
                ->get();

            foreach ($restrictedReports as $rReport) {
                $restrictedFilenames[$archiver->getExpectedPdfFilename($rReport)] = true;
            }
        }

        $committees = ServiceCommittee::orderBy('ar_name')->get();
        $filesByPath = [];

        // Filter standalone physical files (skip standalone physical files when filtering strictly by exceptional DB flag)
        if (!$hasExceptional) {
            foreach ($allPhysicalByTreePath as $treePath => $entry) {
                $fileName = $entry['name'];

                if (isset($restrictedFilenames[$fileName])) {
                    continue;
                }

                // Committee filter for storagebox files
                if ($hasCommittee) {
                    $selectedCommittee = $committees->firstWhere('id', $request->committee_id);
                    if ($selectedCommittee) {
                        $normAr = $selectedCommittee->ar_name ? $archiver->normalizeArabicForMatch($selectedCommittee->ar_name) : null;
                        $normEn = $selectedCommittee->en_name ? mb_strtolower($selectedCommittee->en_name, 'UTF-8') : null;
                        $normPath = $archiver->normalizeArabicForMatch($treePath);
                        $normName = $archiver->normalizeArabicForMatch($fileName);

                        $match = false;
                        if ($normAr && (str_contains($normPath, $normAr) || str_contains($normName, $normAr))) {
                            $match = true;
                        }
                        if ($normEn && (str_contains(mb_strtolower($treePath, 'UTF-8'), $normEn) || str_contains(mb_strtolower($fileName, 'UTF-8'), $normEn))) {
                            $match = true;
                        }
                        if (!$match) {
                            continue;
                        }
                    }
                }

                // Search filter
                if ($hasSearch) {
                    $normSearch = $archiver->normalizeArabicForMatch($request->search);
                    $normFile = $archiver->normalizeArabicForMatch($fileName);
                    $normPath = $archiver->normalizeArabicForMatch($treePath);
                    if (!str_contains($normFile, $normSearch) && !str_contains($normPath, $normSearch)) {
                        continue;
                    }
                }

                // Extract year for date filtering (supporting both Western and Eastern numerals)
                $westernPath = $archiver->toWesternNumerals($treePath);
                $fileYear = null;
                if (preg_match('/\b([12]\d{3})\b/', $westernPath, $yearMatches)) {
                    $fileYear = (int) $yearMatches[1];
                }

                if ($hasStartDate) {
                    $startDateYear = (int) date('Y', strtotime($request->start_date));
                    if ($fileYear && $fileYear < $startDateYear) {
                        continue;
                    }
                }
                if ($hasEndDate) {
                    $endDateYear = (int) date('Y', strtotime($request->end_date));
                    if ($fileYear && $fileYear > $endDateYear) {
                        continue;
                    }
                }

                $filesByPath[$treePath] = $entry;
            }
        }

        // 3. Merge & deduplicate database reports with physical files
        $reportsByCommitteeMonth = [];
        foreach ($dbReports as $rep) {
            if ($rep->status === 'approved' && $rep->meeting_date) {
                $ymKey = $rep->service_committee_id . '_' . $rep->meeting_date->format('Y_m');
                $reportsByCommitteeMonth[$ymKey][] = $rep->id;
            }
        }
        foreach ($reportsByCommitteeMonth as $ymKey => $ids) {
            sort($ids);
            $reportsByCommitteeMonth[$ymKey] = $ids;
        }

        foreach ($dbReports as $report) {
            $committeeName = $report->serviceCommittee ? ($report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? '') : '';
            $baseFolder = $archiver->resolveBaseFolder($report->meeting_date, $committeeName, $allIndexedPaths, false);
            $dateStr = $report->meeting_date ? $report->meeting_date->format('Y-m-d') : $report->created_at->format('Y-m-d');

            $ymKey = $report->service_committee_id . '_' . ($report->meeting_date ? $report->meeting_date->format('Y_m') : '');
            $monthIds = $reportsByCommitteeMonth[$ymKey] ?? null;
            $expectedPdfFilename = $archiver->getExpectedPdfFilename($report, $monthIds);
            $expectedTreePath = "{$baseFolder}/{$expectedPdfFilename}";

            // Check if physical file exists at expected path or by unique expected filename
            $matchedPhysicalPath = null;
            if (isset($allPhysicalByTreePath[$expectedTreePath])) {
                $matchedPhysicalPath = $expectedTreePath;
            } elseif (!empty($allPhysicalByFilename[$expectedPdfFilename])) {
                $matchedPhysicalPath = $allPhysicalByFilename[$expectedPdfFilename][0];
            }

            $reportDisplayTitle = ($report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? 'Committee Report')
                . ' - ' . $dateStr . ' (' . $report->meeting_day_description . ').pdf';

            if ($matchedPhysicalPath !== null) {
                $physicalEntry = $filesByPath[$matchedPhysicalPath] ?? $allPhysicalByTreePath[$matchedPhysicalPath];
                $physicalEntry['db_report_id'] = $report->id;
                $physicalEntry['report_status'] = $report->status;
                $physicalEntry['meeting_date'] = $dateStr;
                $physicalEntry['meeting_day_description'] = $report->meeting_day_description;
                $physicalEntry['report_title'] = $reportDisplayTitle;
                $physicalEntry['committee_name'] = $committeeName;
                $physicalEntry['is_exceptional'] = (bool) $report->is_exceptional;
                $physicalEntry['source'] = 'synced';
                $filesByPath[$matchedPhysicalPath] = $physicalEntry;
                $resolvedReportFolder = dirname($matchedPhysicalPath);
            } else {
                $virtualFilename = sprintf('report_%d_%s.pdf', $report->id, $dateStr);
                $virtualPath = "{$baseFolder}/{$virtualFilename}";

                $filesByPath[$virtualPath] = [
                    'is_dir' => false,
                    'name' => $reportDisplayTitle,
                    'path' => $virtualPath,
                    'db_report_id' => $report->id,
                    'report_status' => $report->status,
                    'meeting_date' => $dateStr,
                    'meeting_day_description' => $report->meeting_day_description,
                    'report_title' => $reportDisplayTitle,
                    'committee_name' => $committeeName,
                    'is_exceptional' => (bool) $report->is_exceptional,
                    'source' => 'db',
                    'size' => 0,
                    'mtime' => $report->updated_at ? $report->updated_at->timestamp : null,
                ];
                $resolvedReportFolder = $baseFolder;
            }

            // Merge & deduplicate report attachments
            foreach ($report->attachments as $attachment) {
                $originalName = basename($attachment->original_name);
                $attFilename = sprintf('attachment_%d_%s', $attachment->id, $originalName);
                $attExpectedPath = "{$resolvedReportFolder}/المرفقات/{$attFilename}";

                $matchedAttPath = null;
                if (isset($allPhysicalByTreePath[$attExpectedPath])) {
                    $matchedAttPath = $attExpectedPath;
                } elseif (!empty($allPhysicalByFilename[$attFilename])) {
                    $matchedAttPath = $allPhysicalByFilename[$attFilename][0];
                }

                if ($matchedAttPath !== null) {
                    $attEntry = $filesByPath[$matchedAttPath] ?? $allPhysicalByTreePath[$matchedAttPath];
                    $attEntry['db_attachment_id'] = $attachment->id;
                    $attEntry['db_report_id'] = $report->id;
                    $attEntry['source'] = 'synced';
                    $filesByPath[$matchedAttPath] = $attEntry;
                } else {
                    $filesByPath[$attExpectedPath] = [
                        'is_dir' => false,
                        'name' => $originalName,
                        'path' => $attExpectedPath,
                        'db_attachment_id' => $attachment->id,
                        'db_report_id' => $report->id,
                        'source' => 'db',
                        'size' => (int) $attachment->file_size,
                        'mtime' => $attachment->updated_at ? $attachment->updated_at->timestamp : null,
                    ];
                }
            }
        }

        // 4. Build structured directory tree matching Archives hierarchy
        $tree = [];

        // Include indexed directories (including empty folders) when no search filter is narrowing results
        if (!$isFilterActive) {
            foreach ($allStorageboxDirs as $dirInfo) {
                $rawDirPath = is_array($dirInfo) ? ($dirInfo['path'] ?? '') : (string) $dirInfo;
                $dirMtime = is_array($dirInfo) ? ($dirInfo['mtime'] ?? null) : null;
                if ($rawDirPath === '') {
                    continue;
                }

                $treeDirPath = $archiver->toTreePath($rawDirPath);
                $parts = explode('/', $treeDirPath);
                if (isset($parts[0]) && $parts[0] === 'Archives') {
                    array_shift($parts);
                }
                if (empty($parts)) {
                    continue;
                }

                $currentLevel = &$tree;
                $accumulatedPath = 'Archives';
                foreach ($parts as $dirName) {
                    if ($dirName === '') {
                        continue;
                    }
                    $accumulatedPath .= '/' . $dirName;
                    if (!isset($currentLevel[$dirName])) {
                        $currentLevel[$dirName] = [
                            'is_dir' => true,
                            'name' => $dirName,
                            'path' => $accumulatedPath,
                            'mtime' => $dirMtime,
                            'children' => [],
                        ];
                    } elseif ($dirMtime && empty($currentLevel[$dirName]['mtime'])) {
                        $currentLevel[$dirName]['mtime'] = $dirMtime;
                    }
                    $currentLevel = &$currentLevel[$dirName]['children'];
                }
                unset($currentLevel);
            }
        }

        foreach ($filesByPath as $file) {
            $parts = explode('/', $file['path']);
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
                if ($dirName === '') {
                    continue;
                }
                $accumulatedPath .= '/' . $dirName;
                if (!isset($currentLevel[$dirName])) {
                    $currentLevel[$dirName] = [
                        'is_dir' => true,
                        'name' => $dirName,
                        'path' => $accumulatedPath,
                        'mtime' => null,
                        'children' => [],
                    ];
                }
                $currentLevel = &$currentLevel[$dirName]['children'];
            }

            $fileKey = end($parts);
            $currentLevel[$fileKey] = [
                'is_dir' => false,
                'name' => $file['name'],
                'path' => $file['path'],
                'encrypted_path' => $file['encrypted_path'] ?? null,
                'db_report_id' => $file['db_report_id'] ?? null,
                'db_attachment_id' => $file['db_attachment_id'] ?? null,
                'report_status' => $file['report_status'] ?? null,
                'meeting_date' => $file['meeting_date'] ?? null,
                'meeting_day_description' => $file['meeting_day_description'] ?? null,
                'report_title' => $file['report_title'] ?? null,
                'committee_name' => $file['committee_name'] ?? null,
                'is_exceptional' => $file['is_exceptional'] ?? false,
                'source' => $file['source'] ?? 'storagebox',
                'size' => (int) ($file['size'] ?? 0),
                'mtime' => $file['mtime'] ?? null,
            ];
            unset($currentLevel);
        }

        // Recursively convert associative children arrays to indexed lists, compute recursive counts/sizes, and sort
        $sanitizeTree = function ($nodes) use (&$sanitizeTree) {
            $result = [];
            foreach ($nodes as $node) {
                if ($node['is_dir']) {
                    $children = $sanitizeTree($node['children']);
                    $node['children'] = $children;
                    $node['items_count'] = count($children);

                    $recFiles = 0;
                    $recSize = 0;
                    $maxMtime = $node['mtime'] ?? 0;

                    foreach ($children as $child) {
                        if ($child['is_dir']) {
                            $recFiles += (int) ($child['recursive_files_count'] ?? 0);
                            $recSize += (int) ($child['size'] ?? 0);
                        } else {
                            $recFiles += 1;
                            $recSize += (int) ($child['size'] ?? 0);
                        }
                        if (!empty($child['mtime']) && $child['mtime'] > $maxMtime) {
                            $maxMtime = $child['mtime'];
                        }
                    }

                    $node['recursive_files_count'] = $recFiles;
                    $node['size'] = $recSize;
                    $node['mtime'] = $maxMtime > 0 ? $maxMtime : null;
                }
                $result[] = $node;
            }

            usort($result, function ($a, $b) {
                if ($a['is_dir'] && !$b['is_dir']) {
                    return -1;
                }
                if (!$a['is_dir'] && $b['is_dir']) {
                    return 1;
                }
                // Sort numeric year folders descending (newest years first)
                if ($a['is_dir'] && $b['is_dir'] && preg_match('/^\d{4}$/', $a['name']) && preg_match('/^\d{4}$/', $b['name'])) {
                    return strcmp($b['name'], $a['name']);
                }
                return strcasecmp($a['name'], $b['name']);
            });

            return $result;
        };

        $archiveTree = $sanitizeTree($tree);
        $syncState = $syncService->getSyncState();

        return view('reports.archive', compact('archiveTree', 'committees', 'syncState', 'canManageArchive'));
    }

    public function archiveSyncStatus()
    {
        $syncService = app(\App\Services\OneDriveSyncService::class);
        return response()->json($syncService->getSyncState());
    }

    public function triggerArchiveSync(Request $request)
    {
        if (!$this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $syncService = app(\App\Services\OneDriveSyncService::class);
        $subPath = $request->input('sub_path');
        $forceResync = $request->boolean('force_resync');

        if ($request->boolean('inline') || app()->environment('testing')) {
            $state = $syncService->syncAndIndex(true, $subPath, $forceResync);
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $state['message'] ?? __('messages.sync_completed_success'),
                    'state' => $state,
                ]);
            }
            return redirect()->back()->with('success', $state['message'] ?? __('messages.sync_completed_success'));
        }

        $syncService->updateSyncState([
            'status' => 'syncing',
            'stage' => 'starting',
            'message' => __('messages.sync_started_background'),
            'error' => null,
        ]);

        $cmd = 'php ' . escapeshellarg(base_path('artisan')) . ' onedrive:sync-archives';
        if ($subPath) {
            $cmd .= ' --path=' . escapeshellarg($subPath);
        }
        if ($forceResync) {
            $cmd .= ' --force-resync';
        }
        exec($cmd . ' > /dev/null 2>&1 &');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.sync_started_background'),
                'state' => $syncService->getSyncState(),
            ]);
        }

        return redirect()->back()->with('info', __('messages.sync_started_background'));
    }

    public function downloadArchiveZip(Request $request)
    {
        $request->validate([
            'folder_path' => 'nullable|string|max:1000',
            'paths' => 'nullable|array|max:200',
            'paths.*' => 'string|max:1000',
        ]);

        $archiver = app(\App\Services\ReportArchiver::class);
        $folderPath = $request->input('folder_path');
        $paths = $request->input('paths', []);

        if (empty($folderPath) && empty($paths)) {
            abort(400, 'No folder or file paths specified for zip download.');
        }

        $zipDir = storage_path('app/private/tmp');
        if (!is_dir($zipDir)) {
            @mkdir($zipDir, 0775, true);
        }

        $zipFilename = 'archive_export_' . date('Y-m-d_His') . '.zip';
        $zipFilePath = $zipDir . '/' . uniqid('archive_', true) . '.zip';

        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Could not create ZIP archive.');
        }

        $addedCount = 0;
        $disk = Storage::disk('storagebox');

        $addDiskDirectoryToZip = function (string $diskRelDir, string $zipPrefix) use (&$addDiskDirectoryToZip, $disk, $zip, &$addedCount) {
            $files = $disk->allFiles($diskRelDir);
            foreach ($files as $f) {
                $relInside = ltrim(substr($f, strlen($diskRelDir)), '/');
                $entryName = trim($zipPrefix . '/' . $relInside, '/');
                $fullPath = $disk->path($f);
                if (is_file($fullPath) && is_readable($fullPath)) {
                    $zip->addFile($fullPath, $entryName);
                    $addedCount++;
                }
            }
        };

        if (!empty($folderPath)) {
            if (str_contains($folderPath, '..')) {
                abort(403, 'Invalid folder path.');
            }
            $diskRel = $archiver->toDiskRelativePath($folderPath);
            $folderBase = basename($folderPath) ?: 'Archives';
            $zipFilename = $folderBase . '_' . date('Y-m-d') . '.zip';
            $addDiskDirectoryToZip($diskRel, $folderBase);
        } else {
            foreach ($paths as $rawPath) {
                if (str_contains($rawPath, '..')) {
                    continue;
                }
                $diskRel = $archiver->toDiskRelativePath($rawPath);
                if ($disk->exists($diskRel)) {
                    $fullPath = $disk->path($diskRel);
                    if (is_dir($fullPath)) {
                        $addDiskDirectoryToZip($diskRel, basename($diskRel));
                    } elseif (is_file($fullPath) && is_readable($fullPath)) {
                        $zip->addFile($fullPath, basename($diskRel));
                        $addedCount++;
                    }
                }
            }
        }

        if ($addedCount === 0) {
            $zip->addFromString('README.txt', 'No physical files found in the selected archive path.');
        }

        $zip->close();

        return response()->download($zipFilePath, $zipFilename, [
            'Content-Type' => 'application/zip',
            'X-Content-Type-Options' => 'nosniff',
        ])->deleteFileAfterSend(true);
    }

    public function uploadArchiveFiles(Request $request)
    {
        if (!$this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'target_path' => 'required|string|max:1000',
            'files' => 'required|array|min:1|max:20',
            'files.*' => 'required|file|mimes:pdf,png,jpg,jpeg,gif,webp,doc,docx,xls,xlsx,csv,ppt,pptx,txt,zip|max:25600',
        ]);

        $targetPath = trim(str_replace('\\', '/', $request->input('target_path')), '/');
        if (str_contains($targetPath, '..')) {
            abort(403, 'Invalid target path.');
        }

        $archiver = app(\App\Services\ReportArchiver::class);
        $syncService = app(\App\Services\OneDriveSyncService::class);
        $diskRelFolder = $archiver->toDiskRelativePath($targetPath);
        $treePath = $archiver->toTreePath($targetPath);

        $uploadedFiles = [];
        foreach ($request->file('files') as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            $safeName = str_replace(['/', '\\', "\0", '..'], '', basename($file->getClientOriginalName()));
            if ($safeName === '') {
                continue;
            }

            $storedPath = ($diskRelFolder !== '' ? "{$diskRelFolder}/{$safeName}" : $safeName);
            $stream = fopen($file->getRealPath(), 'rb');
            Storage::disk('storagebox')->writeStream($storedPath, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }

            $syncService->pushPathToOneDrive($storedPath);
            $uploadedFiles[] = $safeName;
        }

        try {
            \Illuminate\Support\Facades\Artisan::call('storagebox:cache-archives');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Could not re-index archives after upload: ' . $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.archive_files_uploaded_success'),
                'uploaded' => $uploadedFiles,
                'path' => $treePath,
            ]);
        }

        return redirect()->route('committee-reports.archive', ['path' => $treePath])
            ->with('success', __('messages.archive_files_uploaded_success'));
    }

    public function createArchiveFolder(Request $request)
    {
        if (!$this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'parent_path' => 'required|string|max:1000',
            'folder_name' => 'required|string|max:255',
        ]);

        $parentPath = trim(str_replace('\\', '/', $request->input('parent_path')), '/');
        $folderName = trim(str_replace(['/', '\\', "\0", '..'], '', $request->input('folder_name')));

        if (str_contains($parentPath, '..') || $folderName === '') {
            abort(422, 'Invalid folder name or parent path.');
        }

        $archiver = app(\App\Services\ReportArchiver::class);
        $syncService = app(\App\Services\OneDriveSyncService::class);

        $newTreePath = $archiver->toTreePath($parentPath . '/' . $folderName);
        $diskRelPath = $archiver->toDiskRelativePath($newTreePath);

        Storage::disk('storagebox')->makeDirectory($diskRelPath);
        $syncService->pushPathToOneDrive($diskRelPath);

        try {
            \Illuminate\Support\Facades\Artisan::call('storagebox:cache-archives');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Could not re-index archives after folder creation: ' . $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.archive_folder_created_success'),
                'path' => $newTreePath,
            ]);
        }

        return redirect()->route('committee-reports.archive', ['path' => $newTreePath])
            ->with('success', __('messages.archive_folder_created_success'));
    }

    public function renameArchiveItem(Request $request)
    {
        if (!$this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'path' => 'required|string|max:1000',
            'new_name' => 'required|string|max:255',
        ]);

        $rawPath = trim(str_replace('\\', '/', $request->input('path')), '/');
        $newName = trim(str_replace(['/', '\\', "\0", '..'], '', $request->input('new_name')));

        if (str_contains($rawPath, '..') || $rawPath === '' || $rawPath === 'Archives' || $newName === '') {
            abort(422, 'Invalid path or new name.');
        }

        $archiver = app(\App\Services\ReportArchiver::class);
        $oldTreePath = $archiver->toTreePath($rawPath);
        $parentTreePath = dirname($oldTreePath);
        if ($parentTreePath === '.' || $parentTreePath === '') {
            $parentTreePath = 'Archives';
        }
        $newTreePath = $parentTreePath . '/' . $newName;

        $oldDiskRel = $archiver->toDiskRelativePath($oldTreePath);
        $newDiskRel = $archiver->toDiskRelativePath($newTreePath);

        if (!Storage::disk('storagebox')->exists($oldDiskRel)) {
            abort(404, 'Archive item not found on StorageBox.');
        }

        Storage::disk('storagebox')->move($oldDiskRel, $newDiskRel);

        try {
            \Illuminate\Support\Facades\Artisan::call('storagebox:cache-archives');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Could not re-index archives after rename: ' . $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.archive_item_renamed_success'),
                'path' => $parentTreePath,
                'new_item_path' => $newTreePath,
            ]);
        }

        return redirect()->route('committee-reports.archive', ['path' => $parentTreePath])
            ->with('success', __('messages.archive_item_renamed_success'));
    }

    public function deleteArchiveItem(Request $request)
    {
        if (!$this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'path' => 'required|string|max:1000',
            'is_dir' => 'nullable|boolean',
        ]);

        $rawPath = trim(str_replace('\\', '/', $request->input('path')), '/');
        if (str_contains($rawPath, '..') || $rawPath === '' || $rawPath === 'Archives') {
            abort(422, 'Cannot delete root archive folder.');
        }

        $archiver = app(\App\Services\ReportArchiver::class);
        $syncService = app(\App\Services\OneDriveSyncService::class);

        $treePath = $archiver->toTreePath($rawPath);
        $parentTreePath = dirname($treePath);
        if ($parentTreePath === '.' || $parentTreePath === '') {
            $parentTreePath = 'Archives';
        }

        $diskRel = $archiver->toDiskRelativePath($treePath);
        $disk = Storage::disk('storagebox');

        if (!$disk->exists($diskRel)) {
            abort(404, 'Archive item not found on StorageBox.');
        }

        $fullPath = $disk->path($diskRel);
        $isDir = is_dir($fullPath) || $request->boolean('is_dir');

        if ($isDir) {
            $disk->deleteDirectory($diskRel);
        } else {
            $disk->delete($diskRel);
        }

        $syncService->deletePathFromOneDrive($diskRel, $isDir);

        try {
            \Illuminate\Support\Facades\Artisan::call('storagebox:cache-archives');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Could not re-index archives after delete: ' . $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.archive_item_deleted_success'),
                'path' => $parentTreePath,
            ]);
        }

        return redirect()->route('committee-reports.archive', ['path' => $parentTreePath])
            ->with('success', __('messages.archive_item_deleted_success'));
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

        app(\App\Services\ReportArchiver::class)->archive($report, true);

        CommitteeReportLog::record(
            'approved',
            $report,
            __('messages.report_approved_log_summary', ['id' => $report->id, 'committee' => $report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? 'N/A']),
            [
                'meeting_date' => $report->meeting_date ? $report->meeting_date->format('Y-m-d') : null,
            ]
        );

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

        CommitteeReportLog::record(
            'returned_to_draft',
            $report,
            __('messages.report_returned_log_summary', ['id' => $report->id, 'committee' => $report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? 'N/A']),
            [
                'meeting_date' => $report->meeting_date ? $report->meeting_date->format('Y-m-d') : null,
                'review_notes' => $request->review_notes,
            ]
        );

        return redirect()->route('committee-reports.index')->with('success', __('messages.report_returned_draft_notes'));
    }

    public function logs(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('super admin')) {
            abort(403, 'Unauthorized. Only Super Admin can view committee report logs.');
        }

        $query = CommitteeReportLog::with('user')->latest('created_at');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('committee_id')) {
            $query->where('service_committee_id', $request->committee_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('summary', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%")
                  ->orWhere('committee_name', 'like', "%{$search}%")
                  ->orWhere('committee_report_id', $search);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();
        $committees = ServiceCommittee::orderBy('ar_name')->get();

        return view('reports.logs', compact('logs', 'committees'));
    }

    protected function sendNotificationEmail($report)
    {
        try {
            \App\Jobs\SendCommitteeReportSubmissionNotification::dispatch($report->id);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed to dispatch committee report submission notification: " . $e->getMessage());
        }
    }
}
