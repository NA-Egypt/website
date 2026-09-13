<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommitteeReport;
use App\Http\Resources\CommitteeReportResource;
use Illuminate\Http\Request;

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

        $committeeReport->update($validated);
        return new CommitteeReportResource($committeeReport);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CommitteeReport $committeeReport)
    {
        $committeeReport->delete();
        return response()->noContent();
    }
}
