<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Models\Group;
use App\Models\Day;
use Mpdf\Mpdf;

class MeetingExportController extends Controller
{
    public function wizard()
    {
        // Simply return a view that mounts the Livewire component
        return view('exports.wizard-page');
    }

    public function download(Request $request)
    {
        $request->validate([
            'service_bodies' => 'required|array',
            'service_bodies.*' => 'exists:service_bodies,id',
            'fields' => 'required|array',
        ]);

        $serviceBodyIds = $request->input('service_bodies');
        $fields = $request->input('fields');

        // Fetch meetings belonging to selected service bodies, excluding online ones
        $meetings = Meeting::whereHas('group', function($q) use ($serviceBodyIds) {
            $q->whereIn('service_body_id', $serviceBodyIds)
              ->whereNotIn('group_type', ['اونلاين', 'اون لاين', 'online']);
        })
        ->where('status', 'available')
        ->notMonthlyRecurrent()
        ->with(['group', 'day', 'topic', 'topics'])
        ->get();

        // Group meetings by day name in Arabic
        $meetingsByDay = [];
        $dayOrderAr = [
            'Saturday' => 'السبت',
            'Sunday' => 'الأحد',
            'Monday' => 'الإثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
        ];

        foreach ($dayOrderAr as $engName => $arName) {
            $dayMeetings = $meetings->filter(function($meeting) use ($engName) {
                return $meeting->day && strtolower($meeting->day->en_name) === strtolower($engName);
            })->sortBy('start_time');

            if ($dayMeetings->isNotEmpty()) {
                $meetingsByDay[$arName] = $dayMeetings;
            }
        }

        // Get groups that have meetings in the list
        $groupIds = $meetings->pluck('group_id')->unique()->toArray();
        $groups = Group::whereIn('id', $groupIds)->orderBy('ar_name')->get();

        // Export Date formatted in Arabic style
        $exportDate = now()->format('Y-m-d');

        $pageSize = $request->input('page_size', 'A4');
        if (!in_array($pageSize, ['A4', 'A5'])) {
            $pageSize = 'A4';
        }

        // Render view
        $html = view('exports.meetings-pdf', compact('meetingsByDay', 'groups', 'fields', 'exportDate', 'pageSize'))->render();

        // Initialize mPDF
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        // Settings based on paper size
        if ($pageSize === 'A5') {
            $marginTop = 10;
            $marginBottom = 10;
            $marginHeader = 3;
            $marginFooter = 3;
            $headerFontSize = '11px';
            $headerPadding = '4px';
            $footerFontSize = '6.5px';
            $footerMarginTop = '2px';
        } else {
            $marginTop = 12;
            $marginBottom = 12;
            $marginHeader = 4;
            $marginFooter = 4;
            $headerFontSize = '14px';
            $headerPadding = '6px';
            $footerFontSize = '8px';
            $footerMarginTop = '4px';
        }

        $oldErrorReporting = error_reporting();
        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        try {
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => $pageSize,
                'directionality' => 'rtl',
                'margin_left' => 5,
                'margin_right' => 5,
                'margin_top' => $marginTop,
                'margin_bottom' => $marginBottom,
                'margin_header' => $marginHeader,
                'margin_footer' => $marginFooter,
                'fontDir' => array_merge($fontDirs, [resource_path('fonts')]),
                'fontdata' => $fontData + [
                    'amiri' => [
                        'R' => 'Amiri-Regular.ttf',
                    ],
                    'cairo' => [
                        'R' => 'Cairo-Regular.ttf',
                    ],
                ],
                'default_font' => 'cairo',
            ]);

            $mpdf->autoArabic = true;
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->keepColumns = true;

            // Set HTML Header programmatically
            $mpdf->SetHTMLHeader('
                <div style="text-align: center; font-size: ' . $headerFontSize . '; font-weight: bold; color: #00698f; border-bottom: 1px solid #00698f; padding-bottom: ' . $headerPadding . '; font-family: cairo, sans-serif;">
                    اجتماعات التعافي - ' . $exportDate . '
                </div>
            ');

            // Set HTML Footer programmatically
            $mpdf->SetHTMLFooter('
                <hr style="height: 0.5px; border: none; background-color: #ccc; margin: 0; padding: 0;">
                <table style="width: 100%; border: none; font-size: ' . $footerFontSize . '; color: #666; font-family: cairo, sans-serif; margin-top: ' . $footerMarginTop . ';" dir="rtl">
                    <tr>
                        <td width="55%" style="text-align: right; border: none;"><strong>خطوط المساعدة:</strong> <span dir="ltr">+201006979198</span> / <span dir="ltr">+201060933888</span> (الرئيسي) - <span dir="ltr">+201503884411</span> (الإسكندرية) - <span dir="ltr">+201003694690</span> (أهرام وجيزة)</td>
                        <td width="15%" style="text-align: center; direction: ltr; border: none;">{PAGENO} / {nbpg}</td>
                        <td width="30%" style="text-align: left; direction: ltr; border: none;">هذا الجدول تم تصديره من موقع زمالة المدمنين المجهولين بمصر https://naegypt.org</td>
                    </tr>
                </table>
            ');

            $mpdf->WriteHTML($html);

            $filename = 'meetings_print_export_' . $exportDate . '.pdf';
            $pdfContent = $mpdf->Output($filename, 'S');
        } finally {
            error_reporting($oldErrorReporting);
        }

        return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
