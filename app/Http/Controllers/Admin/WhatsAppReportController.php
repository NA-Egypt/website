<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsApp\WhatsAppReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WhatsAppReportController extends Controller
{
    /**
     * Display comprehensive WhatsApp analytics dashboard.
     */
    public function index(Request $request, WhatsAppReportService $reportService): View
    {
        $preset = $request->query('preset', '7d');
        $includeDev = (bool) $request->query('include_dev', false);

        switch ($preset) {
            case 'today':
                $start = Carbon::now('Africa/Cairo')->startOfDay();
                $end = Carbon::now('Africa/Cairo')->endOfDay();
                break;
            case '30d':
                $start = Carbon::now('Africa/Cairo')->subDays(29)->startOfDay();
                $end = Carbon::now('Africa/Cairo')->endOfDay();
                break;
            case 'custom':
                $start = $request->filled('start_date')
                    ? Carbon::parse($request->query('start_date'), 'Africa/Cairo')->startOfDay()
                    : Carbon::now('Africa/Cairo')->subDays(6)->startOfDay();
                $end = $request->filled('end_date')
                    ? Carbon::parse($request->query('end_date'), 'Africa/Cairo')->endOfDay()
                    : Carbon::now('Africa/Cairo')->endOfDay();
                break;
            case '7d':
            default:
                $preset = '7d';
                $start = Carbon::now('Africa/Cairo')->subDays(6)->startOfDay();
                $end = Carbon::now('Africa/Cairo')->endOfDay();
                break;
        }

        $kpis = $reportService->getKpis($start, $end, $includeDev);
        $volumeTrends = $reportService->getMessageVolumeTrend($start, $end, $includeDev);
        $categoryBreakdown = $reportService->getServiceCategoryBreakdown($start, $end, $includeDev);
        $volunteerActivity = $reportService->getVolunteerActivitySummary($start, $end);

        $isDev = config('whatsapp.dev_mode', false) || (request()->getHost() === 'egyptna.org' || str_ends_with(request()->getHost(), '.egyptna.org'));

        return view('whatsapp.reports', compact(
            'preset',
            'start',
            'end',
            'kpis',
            'volumeTrends',
            'categoryBreakdown',
            'volunteerActivity',
            'includeDev',
            'isDev'
        ));
    }

    /**
     * Export messages and activity to CSV.
     */
    public function export(Request $request, WhatsAppReportService $reportService): StreamedResponse
    {
        $preset = $request->query('preset', '30d');
        $includeDev = (bool) $request->query('include_dev', false);

        if ($preset === 'today') {
            $start = Carbon::now('Africa/Cairo')->startOfDay();
            $end = Carbon::now('Africa/Cairo')->endOfDay();
        } elseif ($preset === '7d') {
            $start = Carbon::now('Africa/Cairo')->subDays(6)->startOfDay();
            $end = Carbon::now('Africa/Cairo')->endOfDay();
        } elseif ($preset === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->query('start_date'), 'Africa/Cairo')->startOfDay();
            $end = Carbon::parse($request->query('end_date'), 'Africa/Cairo')->endOfDay();
        } else {
            $start = Carbon::now('Africa/Cairo')->subDays(29)->startOfDay();
            $end = Carbon::now('Africa/Cairo')->endOfDay();
        }

        return $reportService->exportToCsv($start, $end, $includeDev);
    }
}
