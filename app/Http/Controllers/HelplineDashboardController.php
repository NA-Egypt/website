<?php

namespace App\Http\Controllers;

use App\Models\HelplineCall;
use App\Models\HelplineVolunteer;
use App\Services\HelplineReportService;
use App\Services\MpdfService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HelplineDashboardController extends Controller
{
    protected HelplineReportService $reportService;

    public function __construct(HelplineReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Check if user is authorized to manage helpline
     */
    protected function authorizeHelpline(): void
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $isAuthorized = $user->can('manage helpline')
            || $user->hasRole('super admin')
            || $user->hasRole('Phoneline')
            || in_array($user->email, ['phone@naegypt.org', 'pr@naegypt.org']);

        if (!$isAuthorized) {
            abort(403, 'غير مصرح لك بالوصول إلى إدارة خطوط المساعدة.');
        }
    }

    /**
     * Resolve date range & period label from request
     */
    protected function resolvePeriod(Request $request): array
    {
        $availableCycles = $this->reportService->getAvailableCycles(12);
        $currentCycle = $this->reportService->getCurrentCycleWindow();

        $selectedPeriodType = $request->input('period_type', 'cycle');
        $selectedCycleKey = $request->input('cycle', $currentCycle['key']);

        $startDate = null;
        $endDate = null;
        $periodLabel = '';

        if ($selectedPeriodType === 'all_time') {
            $periodLabel = 'كافة المكالمات (منذ البداية)';
        } elseif ($selectedPeriodType === 'custom') {
            $from = $request->input('from_date');
            $to = $request->input('to_date');
            $startDate = $from ? Carbon::parse($from)->startOfDay() : null;
            $endDate = $to ? Carbon::parse($to)->endOfDay() : null;
            $periodLabel = 'فترة مخصصة' . ($startDate ? ' من ' . $startDate->format('Y-m-d') : '') . ($endDate ? ' إلى ' . $endDate->format('Y-m-d') : '');
        } else {
            $selectedPeriodType = 'cycle';
            if (isset($availableCycles[$selectedCycleKey])) {
                $cycle = $availableCycles[$selectedCycleKey];
                $startDate = $cycle['start'];
                $endDate = $cycle['end'];
                $periodLabel = $cycle['label'];
            } else {
                $startDate = $currentCycle['start'];
                $endDate = $currentCycle['end'];
                $selectedCycleKey = $currentCycle['key'];
                $periodLabel = $currentCycle['label'];
            }
        }

        return [
            'availableCycles' => $availableCycles,
            'currentCycle' => $currentCycle,
            'selectedPeriodType' => $selectedPeriodType,
            'selectedCycleKey' => $selectedCycleKey,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'periodLabel' => $periodLabel,
        ];
    }

    /**
     * Build filtered query for calls
     */
    protected function buildCallsQuery(Request $request, ?Carbon $startDate, ?Carbon $endDate)
    {
        $callsQuery = HelplineCall::query()->with('volunteer');

        if ($startDate) {
            $callsQuery->whereDate('call_date', '>=', $startDate->toDateString());
        }
        if ($endDate) {
            $callsQuery->whereDate('call_date', '<=', $endDate->toDateString());
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $callsQuery->where(function ($q) use ($search) {
                $q->where('call_brief', 'like', "%{$search}%")
                  ->orWhere('volunteer_name', 'like', "%{$search}%")
                  ->orWhere('volunteer_name_other', 'like', "%{$search}%")
                  ->orWhere('caller_type', 'like', "%{$search}%")
                  ->orWhere('caller_type_other', 'like', "%{$search}%")
                  ->orWhere('referral_source', 'like', "%{$search}%")
                  ->orWhere('referral_source_other', 'like', "%{$search}%")
                  ->orWhere('hospital_name', 'like', "%{$search}%")
                  ->orWhere('poster_location', 'like', "%{$search}%")
                  ->orWhere('additional_info', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter_duration')) {
            $callsQuery->where('duration', $request->filter_duration);
        }
        if ($request->filled('filter_shift')) {
            $callsQuery->where('call_time_shift', $request->filter_shift);
        }
        if ($request->filled('filter_step12')) {
            $val = $request->filter_step12;
            $callsQuery->where('is_step_12', $val === '1' || $val === 1 || $val === true || $val === 'true');
        }
        if ($request->filled('filter_discuss')) {
            $val = $request->filter_discuss;
            $callsQuery->where('discuss_in_meeting', $val === '1' || $val === 1 || $val === true || $val === 'true');
        }
        if ($request->filled('filter_volunteer')) {
            $callsQuery->where('volunteer_name', $request->filter_volunteer);
        }
        if ($request->filled('filter_caller_type')) {
            $callsQuery->where('caller_type', $request->filter_caller_type);
        }

        return $callsQuery;
    }

    /**
     * Helpline reporting dashboard and call log
     */
    public function index(Request $request)
    {
        $this->authorizeHelpline();

        $period = $this->resolvePeriod($request);
        $availableCycles = $period['availableCycles'];
        $currentCycle = $period['currentCycle'];
        $selectedPeriodType = $period['selectedPeriodType'];
        $selectedCycleKey = $period['selectedCycleKey'];
        $startDate = $period['startDate'];
        $endDate = $period['endDate'];
        $periodLabel = $period['periodLabel'];

        // Summary metrics
        $reportData = $this->reportService->getReportData($startDate, $endDate);

        // Filterable calls table query
        $callsQuery = $this->buildCallsQuery($request, $startDate, $endDate);
        $calls = $callsQuery->latest('call_date')->latest('id')->paginate(20)->withQueryString();

        $shifts = HelplineReportService::SHIFTS;
        $callerTypes = HelplineReportService::CALLER_TYPES;
        $referralSources = HelplineReportService::REFERRAL_SOURCES;
        $volunteersList = HelplineVolunteer::orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'is_active']);

        return view('helpline.index', compact(
            'availableCycles',
            'currentCycle',
            'selectedPeriodType',
            'selectedCycleKey',
            'startDate',
            'endDate',
            'periodLabel',
            'reportData',
            'calls',
            'shifts',
            'callerTypes',
            'referralSources',
            'volunteersList'
        ));
    }

    /**
     * JSON data endpoint for Vue component
     */
    public function data(Request $request)
    {
        $this->authorizeHelpline();

        $period = $this->resolvePeriod($request);
        $reportData = $this->reportService->getReportData($period['startDate'], $period['endDate']);

        $callsQuery = $this->buildCallsQuery($request, $period['startDate'], $period['endDate']);
        $perPage = (int) $request->input('per_page', 20);
        if ($perPage < 5 || $perPage > 100) $perPage = 20;

        $calls = $callsQuery->latest('call_date')->latest('id')->paginate($perPage);

        return response()->json([
            'calls' => $calls,
            'reportData' => $reportData,
            'period' => [
                'type' => $period['selectedPeriodType'],
                'cycle_key' => $period['selectedCycleKey'],
                'label' => $period['periodLabel'],
                'start_date' => $period['startDate'] ? $period['startDate']->toIso8601String() : null,
                'end_date' => $period['endDate'] ? $period['endDate']->toIso8601String() : null,
            ],
        ]);
    }

    /**
     * Instant toggle for 'Discuss in upcoming meeting' flag
     */
    public function toggleDiscuss(HelplineCall $call)
    {
        $this->authorizeHelpline();

        $call->discuss_in_meeting = !$call->discuss_in_meeting;
        $call->save();

        return response()->json([
            'success' => true,
            'discuss_in_meeting' => (bool) $call->discuss_in_meeting,
            'message' => $call->discuss_in_meeting ? 'تم تحديد المكالمة للمناقشة بالاجتماع' : 'تمت إزالة علامة المناقشة بالاجتماع',
        ]);
    }

    /**
     * Delete call response record
     */
    public function destroyCall(HelplineCall $call)
    {
        $this->authorizeHelpline();

        $call->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف سجل المكالمة بنجاح.',
        ]);
    }

    /**
     * Export calls in current filtered period to CSV/Excel
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $this->authorizeHelpline();

        $periodType = $request->input('period_type', 'cycle');
        $cycleKey = $request->input('cycle');
        $startDate = null;
        $endDate = null;

        if ($periodType === 'custom') {
            $from = $request->input('from_date');
            $to = $request->input('to_date');
            $startDate = $from ? Carbon::parse($from)->startOfDay() : null;
            $endDate = $to ? Carbon::parse($to)->endOfDay() : null;
        } elseif ($periodType === 'cycle' && $cycleKey) {
            $cycles = $this->reportService->getAvailableCycles(24);
            if (isset($cycles[$cycleKey])) {
                $startDate = $cycles[$cycleKey]['start'];
                $endDate = $cycles[$cycleKey]['end'];
            }
        } elseif ($periodType === 'cycle') {
            $curr = $this->reportService->getCurrentCycleWindow();
            $startDate = $curr['start'];
            $endDate = $curr['end'];
        }

        $query = HelplineCall::query()->with('volunteer')->latest('call_date')->latest('id');

        if ($request->filled('ids')) {
            $ids = is_array($request->input('ids')) ? $request->input('ids') : explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        } else {
            if ($startDate) {
                $query->whereDate('call_date', '>=', $startDate->toDateString());
            }
            if ($endDate) {
                $query->whereDate('call_date', '<=', $endDate->toDateString());
            }
        }

        $filename = 'helpline_calls_' . Carbon::now()->format('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $output = fopen('php://output', 'w');
            // Write UTF-8 BOM so Arabic displays properly in Microsoft Excel
            fputs($output, "\xEF\xBB\xBF");

            // CSV Header
            fputcsv($output, [
                'المعرف',
                'وقت الإدخال الفعلي',
                'تاريخ المكالمة',
                'مدة المكالمة',
                'الوردية / التوقيت',
                'فئة المتصل',
                'مصدر المعرفة بالزمالة',
                'اسم المستشفى',
                'مكان الملصق',
                'اسم المتطوع',
                'تحويل لخطوة 12',
                'مناقشة بالاجتماع القادم',
                'نبذة عن المكالمة',
                'معلومات إضافية'
            ]);

            $query->chunk(200, function ($calls) use ($output) {
                foreach ($calls as $call) {
                    fputcsv($output, [
                        $call->id,
                        $call->entry_time ? $call->entry_time->format('Y-m-d H:i:s') : '',
                        $call->call_date ? $call->call_date->format('Y-m-d') : '',
                        $call->duration === 'less_than_5' ? 'أقل من 5 دقائق' : 'أكثر من 5 دقائق',
                        $call->call_time_shift,
                        $call->effective_caller_type,
                        $call->effective_referral_source,
                        $call->hospital_name ?: '',
                        $call->poster_location ?: '',
                        $call->effective_volunteer_name,
                        $call->is_step_12 ? 'نعم' : 'لا',
                        $call->discuss_in_meeting ? 'نعم' : 'لا',
                        $call->call_brief,
                        $call->additional_info,
                    ]);
                }
            });

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Export branded PDF monthly summary report
     */
    public function exportPdf(Request $request)
    {
        $this->authorizeHelpline();

        $cycleKey = $request->input('cycle');
        $cycles = $this->reportService->getAvailableCycles(24);
        $current = $this->reportService->getCurrentCycleWindow();

        if ($cycleKey && isset($cycles[$cycleKey])) {
            $selected = $cycles[$cycleKey];
            $start = $selected['start'];
            $end = $selected['end'];
            $cycleTitle = $selected['label'];
        } else {
            $start = $current['start'];
            $end = $current['end'];
            $cycleTitle = $current['label'];
        }

        $reportData = $this->reportService->getReportData($start, $end);

        $html = view('helpline.report_pdf', [
            'reportData' => $reportData,
            'start' => $start,
            'end' => $end,
            'cycleTitle' => $cycleTitle,
        ])->render();

        $mpdf = MpdfService::create([
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_right' => 15,
        ]);

        $mpdf->WriteHTML($html);
        $filename = 'helpline_report_' . $end->format('Y_m') . '.pdf';
        $pdfContent = $mpdf->Output('', 'S');

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * One-click PR Committee Report integration
     */
    public function syncWorkgroupReport(Request $request)
    {
        $this->authorizeHelpline();

        $cycleKey = $request->input('cycle');
        $cycles = $this->reportService->getAvailableCycles(24);
        $current = $this->reportService->getCurrentCycleWindow();

        if ($cycleKey && isset($cycles[$cycleKey])) {
            $start = $cycles[$cycleKey]['start'];
            $end = $cycles[$cycleKey]['end'];
        } else {
            $start = $current['start'];
            $end = $current['end'];
        }

        try {
            $report = $this->reportService->syncToWorkgroupReport($start, $end, Auth::id());
            return redirect()->back()->with('success', 'تم إنشاء/تحديث مسودة تقرير مجموعة العمل بنجاح (المعرف: #' . $report->id . '). يمكن للجنة العلاقات العامة الآن مراجعتها وإدراجها ضمن تقريرها الرسمي.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء مسودة التقرير: ' . $e->getMessage());
        }
    }

    /**
     * Volunteer list management
     */
    public function volunteers()
    {
        $this->authorizeHelpline();

        $volunteers = HelplineVolunteer::withCount('calls')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('helpline.volunteers', compact('volunteers'));
    }

    /**
     * Store a new volunteer
     */
    public function storeVolunteer(Request $request)
    {
        $this->authorizeHelpline();

        $request->validate([
            'name' => 'required|string|max:150|unique:helpline_volunteers,name',
            'phone' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'يرجى إدخال اسم المتطوع.',
            'name.unique' => 'اسم المتطوع مسجل مسبقاً.',
        ]);

        HelplineVolunteer::create([
            'name' => trim($request->name),
            'phone' => $request->phone ? trim($request->phone) : null,
            'sort_order' => $request->sort_order ?: 0,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        return redirect()->route('helpline.volunteers')->with('success', 'تم إضافة المتطوع بنجاح إلى قائمة خط المساعدة.');
    }

    /**
     * Update an existing volunteer
     */
    public function updateVolunteer(Request $request, $id)
    {
        $this->authorizeHelpline();

        $volunteer = HelplineVolunteer::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:150|unique:helpline_volunteers,name,' . $volunteer->id,
            'phone' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $volunteer->update([
            'name' => trim($request->name),
            'phone' => $request->phone ? trim($request->phone) : null,
            'sort_order' => $request->sort_order ?: 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('helpline.volunteers')->with('success', 'تم تحديث بيانات المتطوع بنجاح.');
    }

    /**
     * Toggle active status
     */
    public function toggleVolunteerStatus($id)
    {
        $this->authorizeHelpline();

        $volunteer = HelplineVolunteer::findOrFail($id);
        $volunteer->is_active = !$volunteer->is_active;
        $volunteer->save();

        return redirect()->route('helpline.volunteers')->with('success', 'تم تعديل حالة تفعيل المتطوع بنجاح.');
    }

    /**
     * Delete volunteer
     */
    public function deleteVolunteer($id)
    {
        $this->authorizeHelpline();

        $volunteer = HelplineVolunteer::findOrFail($id);

        // If volunteer has linked calls, keep record and just deactivate to prevent data loss
        if ($volunteer->calls()->count() > 0) {
            $volunteer->is_active = false;
            $volunteer->save();
            return redirect()->route('helpline.volunteers')->with('warning', 'تم تعطيل المتطوع بدلاً من حذفه لوجود مكالمات مسجلة باسمه لحفظ السجلات.');
        }

        $volunteer->delete();
        return redirect()->route('helpline.volunteers')->with('success', 'تم حذف المتطوع بنجاح.');
    }
}
