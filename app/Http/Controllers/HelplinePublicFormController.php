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
            ->header('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
    }

    /**
     * Submit a helpline call response.
     */
    public function submit(Request $request)
    {
        // Verify reCAPTCHA if secret key is present in environment/config
        $recaptchaSecret = config('services.recaptcha.secret_key') ?: env('RECAPTCHA_SECRET_KEY');
        if (!empty($recaptchaSecret) && !app()->environment('testing')) {
            $request->validate([
                'g-recaptcha-response' => 'required',
            ], [
                'g-recaptcha-response.required' => 'يرجى إكمال التحقق الأمني لمنع الرسائل العشوائية.',
            ]);

            try {
                $recaptcha = new \ReCaptcha\ReCaptcha($recaptchaSecret);
                $resp = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());

                if (!$resp->isSuccess()) {
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json([
                            'message' => 'فشل التحقق الأمني reCAPTCHA. يرجى المحاولة مرة أخرى.',
                            'errors' => ['g-recaptcha-response' => ['فشل التحقق الأمني reCAPTCHA. يرجى المحاولة مرة أخرى.']]
                        ], 422);
                    }
                    return back()->withErrors(['g-recaptcha-response' => 'فشل التحقق الأمني reCAPTCHA. يرجى المحاولة مرة أخرى.'])->withInput();
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('reCAPTCHA verification error: ' . $e->getMessage());
            }
        }

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
            'is_step_12' => 'required|in:0,1,yes,no',
            'call_brief' => 'required|string|min:3',
            'discuss_in_meeting' => 'required|in:0,1,yes,no',
            'additional_info' => 'nullable|string',
        ], [
            'duration.required' => 'يرجى اختيار مدة المكالمة.',
            'call_date.required' => 'يرجى تحديد تاريخ المكالمة.',
            'call_time_shift.required' => 'يرجى اختيار الوردية / توقيت المكالمة.',
            'caller_type.required' => 'يرجى اختيار فئة المتصل.',
            'referral_source.required' => 'يرجى تحديد كيف عرف المتصل عن الزمالة.',
            'volunteer_name.required' => 'يرجى اختيار اسم متلقي المكالمة.',
            'call_brief.required' => 'يرجى كتابة نبذة أو ملخص عن المكالمة.',
        ]);

        // Conditional validation for "Other"
        if ($validated['caller_type'] === 'أخرى' && empty($validated['caller_type_other'])) {
            return back()->withErrors(['caller_type_other' => 'يرجى تحديد فئة المتصل في خانة أخرى.'])->withInput();
        }

        if ($validated['referral_source'] === 'أخرى' && empty($validated['referral_source_other'])) {
            return back()->withErrors(['referral_source_other' => 'يرجى توضيح مصدر المعرفة في خانة أخرى.'])->withInput();
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
            'volunteer_id' => $volunteerId,
            'volunteer_name' => $validated['volunteer_name'],
            'volunteer_name_other' => $validated['volunteer_name_other'] ?? null,
            'is_step_12' => in_array($validated['is_step_12'], ['1', 1, 'yes', true], true),
            'call_brief' => $validated['call_brief'],
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
