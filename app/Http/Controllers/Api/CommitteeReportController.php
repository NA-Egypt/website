<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommitteeReportResource;
use App\Jobs\SendCommitteeReportSubmissionNotification;
use App\Models\CommitteeReport;
use App\Models\CommitteeReportLog;
use App\Models\ServiceCommittee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CommitteeReportController extends Controller
{
    protected function isRsc($user): bool
    {
        return $user && ($user->hasRole('super admin') || $user->hasRole('rsc'));
    }

    protected function getServiceCommittee($user): ?ServiceCommittee
    {
        if (! $user) {
            return null;
        }

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

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isRsc = $this->isRsc($user);
        $committee = $this->getServiceCommittee($user);

        if (! $isRsc && ! $committee) {
            abort(403, 'Unauthorized');
        }

        $query = CommitteeReport::query();

        if ($user->hasRole('super admin')) {
            // Super admin can view all reports
        } elseif ($isRsc) {
            $query->whereIn('status', ['submitted', 'approved']);
        } else {
            $allowedCommitteeIds = [$committee->id];
            if ($committee->isCommittee()) {
                $allowedCommitteeIds = array_merge($allowedCommitteeIds, $committee->workgroups()->pluck('id')->all());
            }
            $query->whereIn('service_committee_id', $allowedCommitteeIds);
        }

        return CommitteeReportResource::collection($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $isRsc = $this->isRsc($user);
        $committee = $this->getServiceCommittee($user);

        if (! $isRsc && ! $committee) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'service_committee_id' => 'required|exists:service_committees,id',
            'parent_report_id' => 'nullable|exists:committee_reports,id',
            'meeting_date' => 'nullable|date',
            'report_date' => 'nullable|date',
            'meeting_day_description' => 'nullable|string',
            'body' => 'nullable|string',
            'positions_status' => 'nullable|array',
            'status' => 'nullable|string|in:draft,submitted,approved,returned,embedded',
            'review_notes' => 'nullable|string',
            'is_exceptional' => 'nullable|boolean',
            'attended_members' => 'nullable|string',
            'footer' => 'nullable|string',
        ]);

        if (! $isRsc && (int) $validated['service_committee_id'] !== (int) $committee->id) {
            abort(403, 'Unauthorized');
        }

        $item = CommitteeReport::create($validated);

        if ($item->status === 'submitted') {
            try {
                SendCommitteeReportSubmissionNotification::dispatch($item->id);
            } catch (\Throwable $e) {
                Log::error('Failed to dispatch report notification from API: '.$e->getMessage());
            }
        }

        CommitteeReportLog::record(
            'created',
            $item,
            __('messages.report_created_log_summary', ['id' => $item->id, 'committee' => $item->serviceCommittee->ar_name ?? $item->serviceCommittee->en_name ?? 'N/A']),
            [
                'meeting_date' => $item->meeting_date ? $item->meeting_date->format('Y-m-d') : null,
                'status' => $item->status,
                'via' => 'api',
            ]
        );

        return (new CommitteeReportResource($item))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, CommitteeReport $committeeReport)
    {
        $user = $request->user();
        $isRsc = $this->isRsc($user);
        $committee = $this->getServiceCommittee($user);

        if ($user && $user->hasRole('super admin')) {
            return new CommitteeReportResource($committeeReport);
        }

        if ($isRsc) {
            if (! in_array($committeeReport->status, ['submitted', 'approved'])) {
                abort(403, 'Unauthorized');
            }

            return new CommitteeReportResource($committeeReport);
        }

        if ($committee) {
            $isOwn = (int) $committeeReport->service_committee_id === (int) $committee->id;
            $isChildWorkgroup = $committee->isCommittee()
                && $committeeReport->serviceCommittee
                && (int) $committeeReport->serviceCommittee->parent_id === (int) $committee->id;

            if ($isOwn || $isChildWorkgroup) {
                return new CommitteeReportResource($committeeReport);
            }
        }

        abort(403, 'Unauthorized');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CommitteeReport $committeeReport)
    {
        $user = $request->user();
        $isRsc = $this->isRsc($user);
        $committee = $this->getServiceCommittee($user);

        if (! $isRsc) {
            if (! $committee || (int) $committee->id !== (int) $committeeReport->service_committee_id || $committeeReport->status !== 'draft') {
                abort(403, 'Unauthorized');
            }
        }

        $validated = $request->validate([
            'service_committee_id' => 'sometimes|required|exists:service_committees,id',
            'parent_report_id' => 'nullable|exists:committee_reports,id',
            'meeting_date' => 'nullable|date',
            'report_date' => 'nullable|date',
            'meeting_day_description' => 'nullable|string',
            'body' => 'nullable|string',
            'positions_status' => 'nullable|array',
            'status' => 'nullable|string|in:draft,submitted,approved,returned,embedded',
            'review_notes' => 'nullable|string',
            'is_exceptional' => 'nullable|boolean',
            'attended_members' => 'nullable|string',
            'footer' => 'nullable|string',
        ]);

        if (! $isRsc && isset($validated['service_committee_id']) && (int) $validated['service_committee_id'] !== (int) $committee->id) {
            abort(403, 'Unauthorized');
        }

        $oldStatus = $committeeReport->status;
        $committeeReport->update($validated);

        if ($oldStatus === 'draft' && $committeeReport->status === 'submitted') {
            try {
                SendCommitteeReportSubmissionNotification::dispatch($committeeReport->id);
            } catch (\Throwable $e) {
                Log::error('Failed to dispatch report notification from API update: '.$e->getMessage());
            }
        }

        CommitteeReportLog::record(
            'updated',
            $committeeReport,
            __('messages.report_updated_log_summary', ['id' => $committeeReport->id, 'committee' => $committeeReport->serviceCommittee->ar_name ?? $committeeReport->serviceCommittee->en_name ?? 'N/A']),
            [
                'meeting_date' => $committeeReport->meeting_date ? $committeeReport->meeting_date->format('Y-m-d') : null,
                'status' => $committeeReport->status,
                'status_changed' => $oldStatus !== $committeeReport->status ? "{$oldStatus} -> {$committeeReport->status}" : null,
                'via' => 'api',
            ]
        );

        return new CommitteeReportResource($committeeReport);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, CommitteeReport $committeeReport)
    {
        $user = $request->user();
        if (! $user || ! $user->hasRole('super admin')) {
            return response()->json([
                'message' => 'Unauthorized. Only Super Admin can delete committee reports.',
            ], 403);
        }

        if ($committeeReport->status !== 'draft') {
            return response()->json([
                'message' => 'Only draft or returned reports can be deleted.',
            ], 422);
        }

        $reportId = $committeeReport->id;
        $committeeId = $committeeReport->service_committee_id;
        $committeeName = $committeeReport->serviceCommittee ? ($committeeReport->serviceCommittee->ar_name ?: $committeeReport->serviceCommittee->en_name) : null;
        $meetingDate = $committeeReport->meeting_date ? $committeeReport->meeting_date->format('Y-m-d') : null;
        $attachmentCount = $committeeReport->attachments()->count();

        // Release embedded workgroup reports
        $committeeReport->embeddedWorkgroupReports()->update([
            'parent_report_id' => null,
            'status' => 'draft',
        ]);

        // Delete physical files
        foreach ($committeeReport->attachments as $attachment) {
            if (Storage::exists($attachment->file_path)) {
                Storage::delete($attachment->file_path);
            }
            $attachment->delete();
        }

        $committeeReport->delete();

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
                'via' => 'api',
            ]
        );

        return response()->noContent();
    }
}
