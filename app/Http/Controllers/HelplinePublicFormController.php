<?php

namespace App\Http\Controllers;

use App\Models\HelplineCall;
use App\Models\HelplineVolunteer;
use App\Services\HelplineReportService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HelplinePublicFormController extends Controller
{
    protected HelplineReportService $reportService;

    public function __construct(HelplineReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display the public Helpline Call registration form.
     */
    public function show()
    {
        $volunteers = HelplineVolunteer::active()->get();
        $shifts = HelplineReportService::SHIFTS;
        $callerTypes = HelplineReportService::CALLER_TYPES;
        $referralSources = HelplineReportService::REFERRAL_SOURCES;
        $today = Carbon::today()->format('Y-m-d');

        return response()
            ->view('forms.helpline', compact('volunteers', 'shifts', 'callerTypes', 'referralSources', 'today'))
            ->header('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet')
            ->header('Permissions-Policy', 'unload=*');
    }

    /**
     * Submit a helpline call response.
     */
    public function submit(Request $request)
    {
        // Verify Cloudflare Turnstile if configured
        $turnstileSecret = config('services.turnstile.secret_key');
        if (!empty($turnstileSecret) && (!app()->environment('testing') || !empty($request->header('X-Test-Turnstile-Verification')))) {
            $request->validate([
                'cf-turnstile-response' => ['required', new \App\Rules\Turnstile],
            ], [
                'cf-turnstile-response.required' => __('messages.turnstile_required'),
            ]);
        }


        $validated = $request->validate([
            'duration' => 'required|in:less_than_5,more_than_5',
            'call_date' => 'required|date',
            'call_time_shift' => 'required|string|max:100',
            'caller_type' => 'required|string|max:100',
            'caller_type_other' => 'nullable|string|max:255',
            'referral_source' => 'required|string|max:100',
            'referral_source_other' => 'nullable|string|max:255',
            'hospital_name' => 'required_if:referral_source,لجنة المستشفيات|nullable|string|max:255',
            'poster_location' => 'required_if:referral_source,ملصقات الزمالة|nullable|string|max:255',
            'volunteer_name' => 'required|string|max:150',
            'volunteer_name_other' => 'nullable|string|max:150',
            'is_step_12' => 'required|in:0,1,yes,no',
            'call_brief' => 'required_unless:caller_type,عضو حالي|nullable|string',
            'discuss_in_meeting' => 'required|in:0,1,yes,no',
            'additional_info' => 'nullable|string',
        ], [
            'duration.required' => 'يرجى اختيار مدة المكالمة.',
            'call_date.required' => 'يرجى تحديد تاريخ المكالمة.',
            'call_time_shift.required' => 'يرجى اختيار الوردية / توقيت المكالمة.',
            'caller_type.required' => 'يرجى اختيار فئة المتصل.',
            'referral_source.required' => 'يرجى تحديد كيف عرف المتصل عن الزمالة.',
            'hospital_name.required_if' => 'يرجى كتابة اسم المستشفى.',
            'hospital_name.required' => 'يرجى كتابة اسم المستشفى.',
            'poster_location.required_if' => 'يرجى كتابة مكان الملصق.',
            'poster_location.required' => 'يرجى كتابة مكان الملصق.',
            'volunteer_name.required' => 'يرجى اختيار اسم متلقي المكالمة.',
            'call_brief.required' => 'يرجى كتابة نبذة أو ملخص عن المكالمة.',
            'call_brief.required_unless' => 'يرجى كتابة نبذة أو ملخص عن المكالمة.',
        ]);

        // Conditional validation for "Other"
        if ($validated['caller_type'] === 'أخرى' && empty($validated['caller_type_other'])) {
            return back()->withErrors(['caller_type_other' => 'يرجى تحديد فئة المتصل في خانة أخرى.'])->withInput();
        }

        if ($validated['referral_source'] === 'أخرى' && empty($validated['referral_source_other'])) {
            return back()->withErrors(['referral_source_other' => 'يرجى توضيح مصدر المعرفة في خانة أخرى.'])->withInput();
        }

        if ($validated['referral_source'] === 'لجنة المستشفيات' && empty($validated['hospital_name'])) {
            return back()->withErrors(['hospital_name' => 'يرجى كتابة اسم المستشفى.'])->withInput();
        }

        if ($validated['referral_source'] === 'ملصقات الزمالة' && empty($validated['poster_location'])) {
            return back()->withErrors(['poster_location' => 'يرجى كتابة مكان الملصق.'])->withInput();
        }

        if ($validated['volunteer_name'] === 'أخرى' && empty($validated['volunteer_name_other'])) {
            return back()->withErrors(['volunteer_name_other' => 'يرجى كتابة اسم المتطوع في خانة أخرى.'])->withInput();
        }

        // Match volunteer ID if existing volunteer
        $volunteerId = null;
        if ($validated['volunteer_name'] !== 'أخرى') {
            $volunteer = HelplineVolunteer::where('name', $validated['volunteer_name'])->first();
            if ($volunteer) {
                $volunteerId = $volunteer->id;
            }
        }

        // Create call record with exact entry timestamp
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
            'is_step_12' => in_array($validated['is_step_12'], ['1', 1, 'yes', true], true),
            'call_brief' => $validated['call_brief'] ?? null,
            'discuss_in_meeting' => in_array($validated['discuss_in_meeting'], ['1', 1, 'yes', true], true),
            'additional_info' => $validated['additional_info'] ?? null,
            'entry_time' => Carbon::now(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل استجابة المكالمة بنجاح.',
                'call_id' => $call->id,
            ], 201);
        }

        return redirect()->route('forms.helpline.show')->with('success_submitted', true);
    }
}
