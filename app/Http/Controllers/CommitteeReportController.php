<?php

namespace App\Http\Controllers;

use App\Jobs\SendCommitteeReportSubmissionNotification;
use App\Models\CommitteeReport;
use App\Models\CommitteeReportAttachment;
use App\Models\CommitteeReportLog;
use App\Models\ServiceCommittee;
use App\Models\User;
use App\Notifications\CommitteeReportStatusNotification;
use App\Services\MpdfService;
use App\Services\ReportArchiver;
use App\Traits\ResolvesCommitteeReportAccess;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class CommitteeReportController extends Controller
{
    use AuthorizesRequests;
    use ResolvesCommitteeReportAccess;

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
            $query->where(function ($q) use ($threshold) {
                $q->whereYear('meeting_date', '<', $threshold->year)
                    ->orWhere(function ($sub) use ($threshold) {
                        $sub->whereYear('meeting_date', $threshold->year)
                            ->whereMonth('meeting_date', '<', $threshold->month);
                    });
            });
        } elseif (! $isRsc) {
            $committee = $this->getServiceCommittee();
            if (! $committee) {
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
                        $query->where('service_committee_id', (int) $request->workgroup_id);
                    } else {
                        $query->whereIn('service_committee_id', $childWorkgroups->pluck('id'));
                    }
                } else {
                    $query->where('service_committee_id', $committee->id);
                }
            }
        } elseif ($isSuperAdmin) {
            // Super Admin can see drafts, submitted, approved, embedded reports across all committees and workgroups
            $unembeddedDraftsCount = CommitteeReport::whereHas('serviceCommittee', fn ($q) => $q->whereNotNull('parent_id'))
                ->unembeddedDrafts()
                ->count();

            $committeeDraftsCount = CommitteeReport::whereHas('serviceCommittee', fn ($q) => $q->whereNull('parent_id'))
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
                $query->whereHas('serviceCommittee', fn ($q) => $q->whereNotNull('parent_id'));
                if ($request->filled('workgroup_id')) {
                    $query->where('service_committee_id', (int) $request->workgroup_id);
                } elseif ($request->filled('committee_id')) {
                    $query->whereHas('serviceCommittee', fn ($q) => $q->where('parent_id', (int) $request->committee_id));
                }
            } else {
                $query->whereHas('serviceCommittee', fn ($q) => $q->whereNull('parent_id'));
                if ($request->filled('committee_id')) {
                    $query->where('service_committee_id', (int) $request->committee_id);
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
            $query->where(function ($q) use ($search) {
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
        } elseif (! $committee) {
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

        if (! $isRsc && ! $committee) {
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
            app(ReportArchiver::class)->archive($report, true);

            if ($report->serviceCommittee?->user) {
                $report->serviceCommittee->user->notify(new CommitteeReportStatusNotification($report, 'approved'));
            }
        }

        CommitteeReportLog::record(
            'created',
            $report,
            __('messages.report_created_log_summary', ['id' => $report->id, 'committee' => $targetCommittee->ar_name ?? $targetCommittee->en_name ?? 'N/A']),
            [
                'meeting_date' => $report->meeting_date ? $report->meeting_date->format('Y-m-d') : null,
                'status' => $report->status,
                'is_exceptional' => (bool) $report->is_exceptional,
                'attachment_count' => $request->hasFile('attachments') ? count($request->file('attachments')) : 0,
            ]
        );

        return redirect()->route('committee-reports.index')->with('success', __('messages.report_created_success'));
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
            $publishDate = Carbon::parse($meetingDate)->addMonth()->day(10)->startOfDay();
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
            if (! Auth::user()->hasRole('super admin') && ! in_array($report->status, ['submitted', 'approved'])) {
                abort(403, 'Unauthorized');
            }
        } else {
            if (! $this->isReportVisibleToUser($report)) {
                abort(403, 'Unauthorized');
            }
        }

        return view('reports.show', compact('report'));
    }

    public function edit($id)
    {
        $report = CommitteeReport::with(['serviceCommittee', 'attachments'])->findOrFail($id);
        $isRsc = $this->isRsc();

        if (! $isRsc) {
            $committee = $this->getServiceCommittee();
            if (! $committee || $committee->id !== $report->service_committee_id) {
                abort(403, 'Unauthorized');
            }
        }

        // Only drafts can be edited by committee members
        if (! $isRsc && $report->status !== 'draft') {
            return redirect()->route('committee-reports.show', $report->id)
                ->with('error', __('messages.submitted_reports_cannot_be_edited'));
        }

        $committees = $isRsc ? ServiceCommittee::committeesOnly()->orderBy('ar_name')->get() : [];
        $committee = ! $isRsc ? $this->getServiceCommittee() : null;

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

        if (! $this->isRsc()) {
            $committee = $this->getServiceCommittee();
            if (! $committee || $committee->id !== $report->service_committee_id) {
                abort(403, 'Unauthorized');
            }
        }

        if (! $this->isRsc() && $report->status !== 'draft') {
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
            $validFiles = array_filter($request->file('attachments'), function ($file) {
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
        if (! is_array($newEmbeddedIds)) {
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
        if (! empty($newEmbeddedIds)) {
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
            app(ReportArchiver::class)->archive($report->fresh(), true);
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
                'is_exceptional' => (bool) $report->is_exceptional,
                'attachments_added' => $request->hasFile('attachments') ? count($request->file('attachments')) : 0,
            ]
        );

        return redirect()->route('committee-reports.index')->with('success', __('messages.report_updated_success'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (! $user || ! $user->hasRole('super admin')) {
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
        $wasReturnedDraft = ! empty($report->review_notes);

        // Release any embedded workgroup reports back to available drafts
        $report->embeddedWorkgroupReports()->update([
            'parent_report_id' => null,
            'status' => 'draft',
        ]);

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

        return redirect()->route('committee-reports.index')
            ->with('success', __('messages.report_deleted_success'))
            ->with('undo_url', route('committee-reports.restore', $reportId));
    }

    public function restore($id)
    {
        $user = Auth::user();
        if (! $user || ! $user->hasRole('super admin')) {
            abort(403, 'Unauthorized. Only Super Admin can restore committee reports.');
        }

        $report = CommitteeReport::withTrashed()->findOrFail($id);
        $report->restore();

        return redirect()->route('committee-reports.index')
            ->with('success', __('messages.flash.undo.restored_success'));
    }

    public function pdf($id)
    {
        $report = CommitteeReport::with('serviceCommittee')->findOrFail($id);

        if ($this->isRsc()) {
            if (! Auth::user()->hasRole('super admin') && ! in_array($report->status, ['submitted', 'approved'])) {
                abort(403, 'Unauthorized');
            }
        } else {
            if (! $this->isReportVisibleToUser($report)) {
                abort(403, 'Unauthorized');
            }
        }

        $mpdf = MpdfService::create();

        $reports = collect([$report]);
        $html = view('reports.pdf', compact('reports'))->render();
        $mpdf->WriteHTML($html);

        $filename = 'report_'.$report->id.'_'.($report->report_date ? $report->report_date->format('Y-m-d') : $report->created_at->format('Y-m-d')).'.pdf';

        $disposition = request()->query('disposition', 'attachment');
        if (! in_array($disposition, ['attachment', 'inline'])) {
            $disposition = 'attachment';
        }

        return response($mpdf->Output($filename, 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $disposition.'; filename="'.$filename.'"');
    }

    public function exportReportsPdf(Request $request)
    {
        $reportIds = $request->input('report_ids', []);

        if (empty($reportIds)) {
            return back()->with('error', __('messages.no_reports_selected') ?? 'No reports selected for export.');
        }

        $query = CommitteeReport::whereIn('id', $reportIds)->with('serviceCommittee');

        if (! $this->isRsc()) {
            $committee = $this->getServiceCommittee();
            $committeeId = $committee ? $committee->id : 0;
            $now = now();
            $user = auth()->user();
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

        $filename = 'reports_export_'.date('Y-m-d').'.pdf';

        return response($mpdf->Output($filename, 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function send($id)
    {
        $report = CommitteeReport::findOrFail($id);

        $committee = $this->getServiceCommittee();
        if (! $this->isRsc() && (! $committee || $committee->id !== $report->service_committee_id)) {
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

        $rscUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'rsc'))->get();
        if ($rscUsers->isNotEmpty()) {
            Notification::send($rscUsers, new CommitteeReportStatusNotification($report, 'submitted'));
        }

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
            if (! Auth::user()->hasRole('super admin') && ! in_array($report->status, ['submitted', 'approved'])) {
                abort(403, 'Unauthorized');
            }
        } else {
            if (! $this->isReportVisibleToUser($report)) {
                abort(403, 'Unauthorized');
            }
        }

        if (! Storage::exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        $disposition = request()->query('disposition', 'attachment');
        if (! in_array($disposition, ['attachment', 'inline'])) {
            $disposition = 'attachment';
        }

        if ($disposition === 'inline') {
            return response()->file(Storage::path($attachment->file_path), [
                'Content-Type' => $attachment->mime_type,
                'Content-Disposition' => 'inline; filename="'.$attachment->original_name.'"',
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
        if (! $committee || $committee->id !== $report->service_committee_id) {
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

    public function approveAndSend($id)
    {
        if (! $this->isRsc()) {
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

        app(ReportArchiver::class)->archive($report, true);

        if ($report->serviceCommittee?->user) {
            $report->serviceCommittee->user->notify(new CommitteeReportStatusNotification($report, 'approved'));
        }

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
        if (! $this->isRsc()) {
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

        if ($report->serviceCommittee?->user) {
            $report->serviceCommittee->user->notify(new CommitteeReportStatusNotification($report, 'returned'));
        }

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
        if (! $user || ! $user->hasRole('super admin')) {
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
            $query->where(function ($q) use ($search) {
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
            SendCommitteeReportSubmissionNotification::dispatch($report->id);
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch committee report submission notification: '.$e->getMessage());
        }
    }
}
