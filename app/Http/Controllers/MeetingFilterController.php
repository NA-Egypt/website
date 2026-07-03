<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
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
    
    public function exportMeetingsToPDF()
    {
        $userAgent = request()->header('User-Agent');
        if ($userAgent) {
            $bots = [
                'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
                'yandexbot', 'sogou', 'exabot', 'facebot', 'ia_archiver',
                'crawler', 'spider', 'bot'
            ];
            foreach ($bots as $bot) {
                if (stripos($userAgent, $bot) !== false) {
                    abort(403, 'Bots are not allowed to export data.');
                }
            }
        }

        $filters = request()->only(['day', 'serviceBody', 'group', 'neighborhood', 'type', 'city', 'search']);
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
               ->header('Content-Disposition', 'attachment; filename="meetings.pdf"');
    }
    public function exportMeetingsToCSV()
    {
        $userAgent = request()->header('User-Agent');
        if ($userAgent) {
            $bots = [
                'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
                'yandexbot', 'sogou', 'exabot', 'facebot', 'ia_archiver',
                'crawler', 'spider', 'bot'
            ];
            foreach ($bots as $bot) {
                if (stripos($userAgent, $bot) !== false) {
                    abort(403, 'Bots are not allowed to export data.');
                }
            }
        }

        $filters = request()->only(['day', 'serviceBody', 'group', 'neighborhood', 'type', 'city', 'search']);
        $meetings = $this->meetingFilterService->filterMeetings($filters);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="meetings.csv"',
        ];

        $callback = function() use ($meetings) {
            $handle = fopen('php://output', 'w');
            if (app()->getLocale() == 'en') {
                fputcsv($handle, [
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
                    'GSR',
                    'Phone',
                    'Notes'
                ]);
                foreach ($meetings as $meeting) {
                    fputcsv($handle, [
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
                        $meeting->group->en_gsr_name ?? '',
                        $meeting->group->phone ?? '',
                        $meeting->notes ?? ''
                    ]);
                }
            } else {
                fputcsv($handle, [
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
                    'المسؤول',
                    'الهاتف',
                    'الملاحظات'
                ]);
                foreach ($meetings as $meeting) {
                    fputcsv($handle, [
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
                        $meeting->group->ar_gsr_name ?? '',
                        $meeting->group->phone ?? '',
                        $meeting->notes ?? ''
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
