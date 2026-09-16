<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HelplineCallResource;
use App\Models\HelplineCall;
use App\Models\HelplineVolunteer;
use App\Services\HelplineReportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HelplineCallApiController extends Controller
{
    /**
     * Get the public helpline form schema, options, and active volunteers.
     */
    public function schema(): JsonResponse
    {
        return response()->json([
            'title' => 'Helpline Call Response Form',
            'locale' => 'ar',
            'shifts' => HelplineReportService::SHIFTS,
            'caller_types' => HelplineReportService::CALLER_TYPES,
            'referral_sources' => HelplineReportService::REFERRAL_SOURCES,
            'durations' => [
                ['value' => 'less_than_5', 'label' => 'أقل من 5 دقائق'],
                ['value' => 'more_than_5', 'label' => 'أكثر من 5 دقائق'],
            ],
            'volunteers' => HelplineVolunteer::active()->get(['id', 'name', 'sort_order']),
            'fields_order' => [
                'duration',
                'call_date',
                'call_time_shift',
                'caller_type',
                'referral_source',
                'volunteer_name',
                'is_step_12',
                'call_brief',
                'discuss_in_meeting',
                'additional_info',
            ],
        ], 200);
    }

    /**
     * Public submission of a helpline call via REST API.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'duration' => 'required|in:less_than_5,more_than_5',
            'call_date' => 'required|date',
            'call_time_shift' => 'required|string|max:100',
            'caller_type' => 'required|string|max:100',
            'caller_type_other' => 'nullable|string|max:255',
            'referral_source' => 'required|string|max:100',
            'referral_source_other' => 'nullable|string|max:255',
            'volunteer_name' => 'required|string|max:150',
            'volunteer_name_other' => 'nullable|string|max:150',
            'is_step_12' => 'required|boolean',
            'call_brief' => 'required|string|min:3',
            'discuss_in_meeting' => 'required|boolean',
            'additional_info' => 'nullable|string',
        ]);

        if ($validated['caller_type'] === 'أخرى' && empty($validated['caller_type_other'])) {
            return response()->json([
                'message' => 'The caller_type_other field is required when caller_type is أخرى.',
                'errors' => ['caller_type_other' => ['Please specify caller type.']]
            ], 422);
        }

        if ($validated['referral_source'] === 'أخرى' && empty($validated['referral_source_other'])) {
            return response()->json([
                'message' => 'The referral_source_other field is required when referral_source is أخرى.',
                'errors' => ['referral_source_other' => ['Please specify referral source.']]
            ], 422);
        }

        if ($validated['volunteer_name'] === 'أخرى' && empty($validated['volunteer_name_other'])) {
            return response()->json([
                'message' => 'The volunteer_name_other field is required when volunteer_name is أخرى.',
                'errors' => ['volunteer_name_other' => ['Please specify volunteer name.']]
            ], 422);
        }

        $volunteerId = null;
        if ($validated['volunteer_name'] !== 'أخرى') {
            $volunteer = HelplineVolunteer::where('name', $validated['volunteer_name'])->first();
            if ($volunteer) {
                $volunteerId = $volunteer->id;
            }
        }

        $call = HelplineCall::create([
            'duration' => $validated['duration'],
            'call_date' => $validated['call_date'],
            'call_time_shift' => $validated['call_time_shift'],
            'caller_type' => $validated['caller_type'],
            'caller_type_other' => $validated['caller_type_other'] ?? null,
            'referral_source' => $validated['referral_source'],
            'referral_source_other' => $validated['referral_source_other'] ?? null,
            'volunteer_id' => $volunteerId,
            'volunteer_name' => $validated['volunteer_name'],
            'volunteer_name_other' => $validated['volunteer_name_other'] ?? null,
            'is_step_12' => (bool) $validated['is_step_12'],
            'call_brief' => $validated['call_brief'],
            'discuss_in_meeting' => (bool) $validated['discuss_in_meeting'],
            'additional_info' => $validated['additional_info'] ?? null,
            'entry_time' => Carbon::now(),
        ]);

        return (new HelplineCallResource($call))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * List calls (Authenticated API).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = HelplineCall::query()->latest('entry_time');

        if ($request->filled('start_date')) {
            $query->where('entry_time', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('entry_time', '<=', $request->end_date);
        }

        return HelplineCallResource::collection($query->paginate(25));
    }
}
