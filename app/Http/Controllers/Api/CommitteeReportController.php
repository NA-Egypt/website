<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommitteeReport;
use App\Models\CommitteeReportLog;
use App\Http\Resources\CommitteeReportResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommitteeReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return CommitteeReportResource::collection(CommitteeReport::whereIn('status', ['submitted', 'approved'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_committee_id'    => 'required|exists:service_committees,id',
            'parent_report_id'        => 'nullable|exists:committee_reports,id',
            'meeting_date'            => 'nullable|date',
            'report_date'             => 'nullable|date',
            'meeting_day_description' => 'nullable|string',
            'body'                    => 'nullable|string',
            'positions_status'        => 'nullable|array',
            'status'                  => 'nullable|string|in:draft,submitted,approved,returned,embedded',
            'review_notes'            => 'nullable|string',
            'is_exceptional'          => 'nullable|boolean',
            'attended_members'        => 'nullable|string',
            'footer'                  => 'nullable|string',
        ]);

        $item = CommitteeReport::create($validated);

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
    public function show(CommitteeReport $committeeReport)
    {
        if (!in_array($committeeReport->status, ['submitted', 'approved'])) {
            abort(403, 'Unauthorized');
        }
        return new CommitteeReportResource($committeeReport);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CommitteeReport $committeeReport)
    {
        $validated = $request->validate([
            'service_committee_id'    => 'sometimes|required|exists:service_committees,id',
            'parent_report_id'        => 'nullable|exists:committee_reports,id',
            'meeting_date'            => 'nullable|date',
            'report_date'             => 'nullable|date',
            'meeting_day_description' => 'nullable|string',
            'body'                    => 'nullable|string',
            'positions_status'        => 'nullable|array',
            'status'                  => 'nullable|string|in:draft,submitted,approved,returned,embedded',
            'review_notes'            => 'nullable|string',
            'is_exceptional'          => 'nullable|boolean',
            'attended_members'        => 'nullable|string',
            'footer'                  => 'nullable|string',
        ]);

        $oldStatus = $committeeReport->status;
        $committeeReport->update($validated);

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
        if (!$user || !$user->hasRole('super admin')) {
            return response()->json([
                'message' => 'Unauthorized. Only Super Admin can delete committee reports.'
            ], 403);
        }

        if ($committeeReport->status !== 'draft') {
            return response()->json([
                'message' => 'Only draft or returned reports can be deleted.'
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
