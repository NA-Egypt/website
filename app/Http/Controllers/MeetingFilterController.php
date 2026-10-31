<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use App\Services\BotDetectionService;
use App\Services\MeetingFilterService;
use App\Models\Day;
use App\Models\ServiceBody;
use App\Models\Group;
use App\Models\Neighborhood;
use App\Models\Meeting;
use App\Services\MpdfService;

class MeetingFilterController extends Controller
{
    protected $meetingFilterService;

    public function __construct(MeetingFilterService $meetingFilterService)
    {
        $this->meetingFilterService = $meetingFilterService;
    }

    public function filterMeetings(Request $request)
    {
        return view('frontend.meetings');
    }

    /**
     * Reveal meeting group GSR name and phone on-demand (5-click gesture) with strict bot/AI blocking.
     */
    public function revealMeetingContact(Request $request, Meeting $meeting)
    {
        if (BotDetectionService::isBotOrAi($request)) {
            abort(403, 'Bots and AI agents are not allowed to access contact data.');
        }

        $group = $meeting->groupOrDirect;
        if (!$group) {
            abort(404, 'Meeting has no associated group.');
        }

        $locale = app()->getLocale();
        $gsrName = $locale === 'ar'
            ? ($group->ar_gsr_name ?: $group->en_gsr_name)
            : ($group->en_gsr_name ?: $group->ar_gsr_name);

        return response()->json([
            'gsr_name'    => $gsrName,
            'ar_gsr_name' => $group->ar_gsr_name,
            'en_gsr_name' => $group->en_gsr_name,
            'phone'       => $group->phone,
        ], 200, array_merge(BotDetectionService::noIndexAiHeaders(), [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]));
    }

    /**
     * Reveal group GSR name and phone on-demand (5-click gesture) with strict bot/AI blocking.
     */
    public function revealGroupContact(Request $request, Group $group)
    {
        if (BotDetectionService::isBotOrAi($request)) {
            abort(403, 'Bots and AI agents are not allowed to access contact data.');
        }

        $locale = app()->getLocale();
        $gsrName = $locale === 'ar'
            ? ($group->ar_gsr_name ?: $group->en_gsr_name)
            : ($group->en_gsr_name ?: $group->ar_gsr_name);

        return response()->json([
            'gsr_name'    => $gsrName,
            'ar_gsr_name' => $group->ar_gsr_name,
            'en_gsr_name' => $group->en_gsr_name,
            'phone'       => $group->phone,
        ], 200, array_merge(BotDetectionService::noIndexAiHeaders(), [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]));
    }
    
    public function exportMeetingsToPDF()
    {
        if (BotDetectionService::isBotOrAi()) {
            abort(403, 'Bots are not allowed to export data.');
        }

        $filters = request()->only([
            'day', 'serviceBody', 'group', 'neighborhood', 'type', 'city', 'search',
            'recurrence', 'businessMeetingsOnly', 'virtualOnly', 'englishOnly'
        ]);
        $meetings = $this->meetingFilterService->filterMeetings($filters);

        // Register custom fonts "Amiri" and "Cairo" from resources/fonts
        $mpdf = MpdfService::create([
            'directionality' => 'rtl',
        ]);

        $html = view('pdf.meetings', compact('meetings'))->render();
        $mpdf->WriteHTML($html);

        try {
            \App\Models\Transaction::create([
                'model' => 'PDF',
                'operation' => 'download',
                'details' => ['type' => 'meetings_pdf'],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'url' => request()->fullUrl(),
                'user_id' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to log PDF download: ' . $e->getMessage());
        }

        return response($mpdf->Output('meetings.pdf', 'S'), 200)
               ->header('Content-Type', 'application/pdf')
               ->header('Content-Disposition', 'attachment; filename="meetings.pdf"')
               ->header('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet, noai, noimageai');
    }
    public function exportMeetingsToCSV()
    {
        if (BotDetectionService::isBotOrAi()) {
            abort(403, 'Bots are not allowed to export data.');
        }

        $filters = request()->only([
            'day', 'serviceBody', 'group', 'neighborhood', 'type', 'city', 'search',
            'recurrence', 'businessMeetingsOnly', 'virtualOnly', 'englishOnly'
        ]);
        $meetings = $this->meetingFilterService->filterMeetings($filters);
        $isAuthenticated = auth()->check();

        $headers = array_merge([
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="meetings.csv"',
        ], BotDetectionService::noIndexAiHeaders());

        $callback = function() use ($meetings, $isAuthenticated) {
            $handle = fopen('php://output', 'w');
            if (app()->getLocale() == 'en') {
                $csvHeader = [
                    'Day',
                    'Group',
                    'Time',
                    'Topic',
                    'Language',
                    'Neighborhood',
                    'City',
                    'Type',
                    'Options',
                    'Status',
                    'Address',
                ];
                if ($isAuthenticated) {
                    $csvHeader[] = 'GSR';
                    $csvHeader[] = 'Phone';
                }
                $csvHeader[] = 'Notes';
                fputcsv($handle, $csvHeader);

                foreach ($meetings as $meeting) {
                    $row = [
                        $meeting->day->en_name ?? '',
                        $meeting->group->en_name ?? '',
                        $meeting->formatted_start_time . ' - ' . $meeting->formatted_end_time ?? '',
                        $meeting->topic->en_name ?? '',
                        $meeting->lang ?? '',
                        $meeting->group->neighborhood->en_name ?? '',
                        $meeting->group->neighborhood->city->en_name ?? '',
                        $meeting->type ?? '',
                        $meeting->options()->pluck('en_name')->implode(', ') ?? '',
                        $meeting->status ?? '',
                        $meeting->group->en_address ?? '',
                    ];
                    if ($isAuthenticated) {
                        $row[] = $meeting->group->en_gsr_name ?? '';
                        $row[] = $meeting->group->phone ?? '';
                    }
                    $row[] = $meeting->notes ?? '';
                    fputcsv($handle, $row);
                }
            } else {
                $csvHeader = [
                    'اليوم',
                    'المجموعة',
                    'الوقت',
                    'الموضوع',
                    'اللغة',
                    'الحي',
                    'المدينة',
                    'النوع',
                    'خصائص',
                    'الحالة',
                    'العنوان',
                ];
                if ($isAuthenticated) {
                    $csvHeader[] = 'المسؤول';
                    $csvHeader[] = 'الهاتف';
                }
                $csvHeader[] = 'الملاحظات';
                fputcsv($handle, $csvHeader);

                foreach ($meetings as $meeting) {
                    $row = [
                        $meeting->day->ar_name ?? '',
                        $meeting->group->ar_name ?? '',
                        $meeting->formatted_start_time . ' - ' . $meeting->formatted_end_time ?? '',
                        $meeting->topic->ar_name ?? '',
                        $meeting->lang ?? '',
                        $meeting->group->neighborhood->ar_name ?? '',
                        $meeting->group->neighborhood->city->ar_name ?? '',
                        $meeting->type ?? '',
                        $meeting->options()->pluck('ar_name')->implode(', ') ?? '',
                        $meeting->status ?? '',
                        $meeting->group->ar_address ?? '',
                    ];
                    if ($isAuthenticated) {
                        $row[] = $meeting->group->ar_gsr_name ?? '';
                        $row[] = $meeting->group->phone ?? '';
                    }
                    $row[] = $meeting->notes ?? '';
                    fputcsv($handle, $row);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
