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
use Mpdf\Mpdf;

class MeetingFilterController extends Controller
{
    protected $meetingFilterService;

    public function __construct(MeetingFilterService $meetingFilterService)
    {
        $this->meetingFilterService = $meetingFilterService;
    }

    public function filterMeetings(Request $request)
    {

        // Fetch available filter options
        $days = Day::all();
        $serviceBodies = ServiceBody::all();
        $groups = Group::all();
        $neighborhoods = Neighborhood::all();
        $cities = City::all();

        // Apply filters
//        $filters = $request->only(['day', 'serviceBody', 'group', 'neighborhood', 'type', 'city']);
//        $meetings = $this->meetingFilterService->filterMeetings($filters);

        $filters = $request->only(['day', 'serviceBody', 'group', 'neighborhood', 'type', 'city']);
        // Add debug logging
        logger('Received filters:', [
            'raw' => $filters,
            'group_length' => isset($filters['group']) ? strlen($filters['group']) : null,
            'group_hex' => isset($filters['group']) ? bin2hex($filters['group']) : null
        ]);
        $meetings = $this->meetingFilterService->filterMeetings($filters);

        return view('frontend.meetings', compact('meetings', 'days', 'serviceBodies', 'groups', 'neighborhoods', 'cities'));
    }
    
    public function exportMeetingsToPDF()
    {
        $filters = request()->only(['day', 'serviceBody', 'group', 'neighborhood', 'type', 'city']);
        $meetings = $this->meetingFilterService->filterMeetings($filters);

        // Register custom fonts "Amiri" and "Cairo" from resources/fonts
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'directionality' => 'rtl',
            'fontDir' => array_merge($fontDirs, [resource_path('fonts')]),
            'fontdata' => $fontData + [
                'amiri' => [
                    'R' => 'Amiri-Regular.ttf',
                ],
                'cairo' => [
                    'R' => 'Cairo-Regular.ttf',
                ],
            ],
            'default_font' => 'xbriyaz',
        ]);
        $mpdf->autoArabic = true;
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $html = view('pdf.meetings', compact('meetings'))->render();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('meetings.pdf', 'S'), 200)
               ->header('Content-Type', 'application/pdf')
               ->header('Content-Disposition', 'attachment; filename="meetings.pdf"');
    }

    public function exportMeetingsToCSV()
    {
        $meetings = Meeting::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="meetings.csv"',
        ];

        $callback = function() use ($meetings) {
            $handle = fopen('php://output', 'w');

            // Add CSV header
            fputcsv($handle, [
                'ID',
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

            // Add meeting rows
            foreach ($meetings as $meeting) {
                fputcsv($handle, [
                    $meeting->id,
                    $meeting->day->ar_name ?? '',
                    $meeting->group->ar_name ?? '',
                    $meeting->formatted_start_time . ' - ' . $meeting->formatted_end_time ?? '',
                    $meeting->topic->ar_name ?? '',
                    $meeting->lang ?? '',
                    $meeting->group->neighborhood->ar_name ?? '',
                    $meeting->group->city->ar_name ?? '',
                    is_object($meeting->type) ? $meeting->type->ar_name ?? '' : $meeting->type ?? '',
                    $meeting->options()->pluck('ar_name')->implode(', ') ?? '',
                    $meeting->status ?? '',
                    $meeting->group->ar_address ?? '',
                    $meeting->group->ar_gsr_name ?? '',
                    $meeting->group->phone ?? '',
                    $meeting->notes ?? ''
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
