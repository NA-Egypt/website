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
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class HelplineCallApiController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role_or_permission:super admin|Phoneline|manage helpline', only: ['index']),
        ];
    }

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
            'caller_type_options' => HelplineReportService::callerTypeOptions(),
            'referral_sources' => HelplineReportService::REFERRAL_SOURCES,
            'referral_source_options' => HelplineReportService::referralSourceOptions(),
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
                'caller_type_other',
                'referral_source',
                'referral_source_other',
                'hospital_name',
                'poster_location',
                'volunteer_name',
                'volunteer_name_other',
                'is_step_12',
                'call_brief',
                'discuss_in_meeting',
                'additional_info',
            ],
            'conditional_fields' => [
                'call_brief' => [
                    'rule' => 'optional_if',
                    'field' => 'caller_type',
                    'values' => ['عضو حالي', 'مكالمة بالخطأ'],
                    'value' => 'عضو حالي, مكالمة بالخطأ',
                    'description' => 'Call brief is optional when caller_type is "عضو حالي" or "مكالمة بالخطأ", required otherwise.',
                ],
                'hospital_name' => [
                    'rule' => 'required_if',
                    'field' => 'referral_source',
                    'value' => 'لجنة المستشفيات',
                    'description' => 'Hospital name is required when referral_source is "لجنة المستشفيات".',
                ],
                'poster_location' => [
                    'rule' => 'required_if',
                    'field' => 'referral_source',
                    'value' => 'ملصقات الزمالة',
                    'description' => 'Poster location is required when referral_source is "ملصقات الزمالة".',
                ],
            ],
        ], 200);
    }

    /**
     * Public submission of a helpline call via REST API.
     */
    public function store(Request $request): JsonResponse
    {
        $callerType = HelplineReportService::normalizeCallerType((string) $request->input('caller_type', ''));
        $referralSource = HelplineReportService::normalizeReferralSource((string) $request->input('referral_source', ''));
        $volunteerName = $request->input('volunteer_name') === 'other'
            ? 'أخرى'
            : (string) $request->input('volunteer_name', '');

        $validated = $request->validate([
            'duration' => 'required|in:less_than_5,more_than_5',
            'call_date' => 'required|date|before_or_equal:today',
            'call_time_shift' => 'required|string|max:100',
            'caller_type' => 'required|string|max:100',
            'caller_type_other' => [Rule::requiredIf($callerType === 'أخرى'), 'nullable', 'string', 'max:255'],
            'referral_source' => 'required|string|max:100',
            'referral_source_other' => [Rule::requiredIf($referralSource === 'أخرى'), 'nullable', 'string', 'max:255'],
            'hospital_name' => [Rule::requiredIf($referralSource === 'لجنة المستشفيات'), 'nullable', 'string', 'max:255'],
            'poster_location' => [Rule::requiredIf($referralSource === 'ملصقات الزمالة'), 'nullable', 'string', 'max:255'],
            'volunteer_name' => 'required|string|max:150',
            'volunteer_name_other' => [Rule::requiredIf($volunteerName === 'أخرى'), 'nullable', 'string', 'max:150'],
            'is_step_12' => 'required|boolean',
            'call_brief' => [Rule::requiredIf(! in_array($callerType, ['عضو حالي', 'مكالمة بالخطأ'], true)), 'nullable', 'string'],
            'discuss_in_meeting' => 'required|boolean',
            'additional_info' => 'nullable|string',
        ]);

        $validated['caller_type'] = $callerType;
        $validated['referral_source'] = $referralSource;
        $validated['volunteer_name'] = $volunteerName;

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
            'hospital_name' => $validated['hospital_name'] ?? null,
            'poster_location' => $validated['poster_location'] ?? null,
            'volunteer_id' => $volunteerId,
            'volunteer_name' => $validated['volunteer_name'],
            'volunteer_name_other' => $validated['volunteer_name_other'] ?? null,
            'is_step_12' => (bool) $validated['is_step_12'],
            'call_brief' => $validated['call_brief'] ?? null,
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
        $query = HelplineCall::query()->latest('call_date')->latest('id');

        if ($request->filled('start_date')) {
            $query->whereDate('call_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('call_date', '<=', $request->end_date);
        }

        return HelplineCallResource::collection($query->paginate(25));
    }
}
