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
        return CommitteeReportResource::collection(CommitteeReport::where('status', 'submitted')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $item = CommitteeReport::create($request->all());
        return new CommitteeReportResource($item);
    }

    /**
     * Display the specified resource.
     */
    public function show(CommitteeReport $committeeReport)
    {
        if ($committeeReport->status !== 'submitted') {
            abort(403, 'Unauthorized');
        }
        return new CommitteeReportResource($committeeReport);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CommitteeReport $committeeReport)
    {
        $committeeReport->update($request->all());
        return new CommitteeReportResource($committeeReport);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CommitteeReport $committeeReport)
    {
        $committeeReport->delete();
        return response()->json(null, 204);
    }
}
