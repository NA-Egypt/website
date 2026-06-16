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

        // Fetch meetings belonging to selected service bodies
        $meetings = Meeting::whereHas('group', function($q) use ($serviceBodyIds) {
            $q->whereIn('service_body_id', $serviceBodyIds);
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

        // Render view
        $html = view('exports.meetings-pdf', compact('meetingsByDay', 'groups', 'fields', 'exportDate'))->render();

        // Initialize mPDF
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
            'default_font' => 'cairo',
        ]);

        $mpdf->autoArabic = true;
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        // Set HTML Header programmatically
        $mpdf->SetHTMLHeader('
            <div style="text-align: center; font-size: 16px; font-weight: bold; color: #00698f; border-bottom: 2px solid #00698f; padding-bottom: 8px; font-family: cairo, sans-serif;">
                اجتماعات التعافي - ' . $exportDate . '
            </div>
        ');

        // Set HTML Footer programmatically
        $mpdf->SetHTMLFooter('
            <hr style="height: 1px; border: none; background-color: #ddd; margin: 0; padding: 0;">
            <table style="width: 100%; border: none; font-size: 9px; color: #666; font-family: cairo, sans-serif; margin-top: 5px;" dir="rtl">
                <tr>
                    <td width="55%" style="text-align: right; border: none;"><strong>خطوط المساعدة:</strong> +201006979198 / +201060933888 (الرئيسي) - +201503884411 (الإسكندرية) - +201003694690 (أهرام وجيزة)</td>
                    <td width="15%" style="text-align: center; direction: ltr; border: none;">{PAGENO} / {nbpg}</td>
                    <td width="30%" style="text-align: left; direction: ltr; border: none;">هذا الجدول تم تصديره من موقع زمالة المدمنين المجهولين بمصر https://naegypt.org</td>
                </tr>
            </table>
        ');

        $mpdf->WriteHTML($html);

        $filename = 'meetings_print_export_' . $exportDate . '.pdf';

        return response($mpdf->Output($filename, 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
