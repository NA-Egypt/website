<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Group;
use App\Http\Resources\AgendaResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AgendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return AgendaResource::collection(Agenda::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'meetings_per_week' => 'nullable|integer|min:0',
            'agenda_date' => 'required|date',
            'service_position' => 'required|string|in:Open Position,Alt. GSR,GSR',
            'submitter_name' => 'nullable|string|max:255',
            'alt_gsr_position' => 'nullable|string|in:Open Position,Alt. ...',
            'alt_gsr_position' => 'nullable|string|in:Open Position,Alt. GSR',
            'alt_gsr_name' => 'nullable|string|max:255',
            'new_comers' => 'nullable|integer|min:0',
            'open_positions' => 'nullable|string',
            'next_business_meeting' => 'nullable|date',
            'recovery_meetings_changes' => 'nullable|boolean',
            'recovery_atmosphere' => 'required|string|min:1',
            'trusted_servants' => 'required|string|min:1',
            'financial_issues' => 'required|string|min:1',
            'other_topics' => 'nullable|array',
            'other_topics.*.title' => 'required|string|max:255',
            'other_topics.*.content' => 'required|string',
        ]);

        if (!isset($validatedData['recovery_meetings_changes'])) {
            $validatedData['recovery_meetings_changes'] = false;
        }

        $otherTopics = [];
        if ($request->has('other_topics')) {
            foreach ($request->input('other_topics') as $item) {
                if (is_array($item) && !empty($item['title']) && !empty($item['content'])) {
                    $otherTopics[] = [
                        'title' => $item['title'],
                        'content' => $item['content']
                    ];
                }
            }
        }
        $validatedData['other_topics'] = $otherTopics;

        $group = Group::findOrFail($validatedData['group_id']);
        $agendaDate = Carbon::parse($validatedData['agenda_date']);
        $group->agendas()
            ->whereYear('agenda_date', $agendaDate->year)
            ->whereMonth('agenda_date', $agendaDate->month)
            ->delete();

        $item = Agenda::create($validatedData);
        return new AgendaResource($item);
    }

    /**
     * Display the specified resource.
     */
    public function show(Agenda $agenda)
    {
        return new AgendaResource($agenda);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Agenda $agenda)
    {
        $validatedData = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'meetings_per_week' => 'nullable|integer|min:0',
            'agenda_date' => 'required|date',
            'service_position' => 'required|string|in:Open Position,Alt. GSR,GSR',
            'submitter_name' => 'nullable|string|max:255',
            'alt_gsr_position' => 'nullable|string|in:Open Position,Alt. GSR',
            'alt_gsr_name' => 'nullable|string|max:255',
            'new_comers' => 'nullable|integer|min:0',
            'open_positions' => 'nullable|string',
            'next_business_meeting' => 'nullable|date',
            'recovery_meetings_changes' => 'nullable|boolean',
            'recovery_atmosphere' => 'required|string|min:1',
            'trusted_servants' => 'required|string|min:1',
            'financial_issues' => 'required|string|min:1',
            'other_topics' => 'nullable|array',
            'other_topics.*.title' => 'required|string|max:255',
            'other_topics.*.content' => 'required|string',
        ]);

        if (!isset($validatedData['recovery_meetings_changes'])) {
            $validatedData['recovery_meetings_changes'] = false;
        }

        $otherTopics = [];
        if ($request->has('other_topics')) {
            foreach ($request->input('other_topics') as $item) {
                if (is_array($item) && !empty($item['title']) && !empty($item['content'])) {
                    $otherTopics[] = [
                        'title' => $item['title'],
                        'content' => $item['content']
                    ];
                }
            }
        }
        $validatedData['other_topics'] = $otherTopics;

        $agenda->update($validatedData);
        return new AgendaResource($agenda);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agenda $agenda)
    {
        $agenda->delete();
        return response()->json(null, 204);
    }
}
