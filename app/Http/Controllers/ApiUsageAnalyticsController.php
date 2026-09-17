<?php

namespace App\Http\Controllers;

use App\Models\ApiLog;
use App\Models\ApiDailyStat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiUsageAnalyticsController extends Controller
{
    /**
     * Display the API & Mobile App Analytics dashboard.
     */
    public function index(Request $request)
    {
        $preset = $request->input('preset', 'today');
        $platform = $request->input('platform', 'all');
        $statusFilter = $request->input('status', 'all');
        $search = trim((string) $request->input('search', ''));

        // Determine date bounds
        $now = now();
        switch ($preset) {
            case '7d':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $isHourly = false;
                break;
            case '30d':
                $startDate = $now->copy()->subDays(29)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $isHourly = false;
                break;
            case 'custom':
                $startDate = $request->input('start_date') 
                    ? Carbon::parse($request->input('start_date'))->startOfDay() 
                    : $now->copy()->startOfDay();
                $endDate = $request->input('end_date') 
                    ? Carbon::parse($request->input('end_date'))->endOfDay() 
                    : $now->copy()->endOfDay();
                $isHourly = $startDate->diffInDays($endDate) <= 1;
                break;
            case 'today':
            default:
                $preset = 'today';
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $isHourly = true;
                break;
        }

        // Base query for the time range
        $baseQuery = ApiLog::whereBetween('created_at', [$startDate, $endDate]);

        if ($platform !== 'all') {
            $baseQuery->where('platform', $platform);
        }

        if ($statusFilter === 'success') {
            $baseQuery->whereBetween('status_code', [200, 299]);
        } elseif ($statusFilter === 'errors') {
            $baseQuery->where('status_code', '>=', 400);
        }

        // 1. KPI Calculations
        $kpiQuery = ApiLog::whereBetween('created_at', [$startDate, $endDate]);
        $totalRequests = (clone $kpiQuery)->count();

        $androidCount = (clone $kpiQuery)->where('platform', 'android')->count();
        $iosCount = (clone $kpiQuery)->where('platform', 'ios')->count();
        $webCount = (clone $kpiQuery)->where('platform', 'web')->count();
        $otherCount = (clone $kpiQuery)->where('platform', 'other')->count();

        $mobileCount = $androidCount + $iosCount;
        $mobilePercentage = $totalRequests > 0 ? round(($mobileCount / $totalRequests) * 100, 1) : 0;

        $successfulCount = (clone $kpiQuery)->whereBetween('status_code', [200, 299])->count();
        $clientErrorCount = (clone $kpiQuery)->whereBetween('status_code', [400, 499])->count();
        $serverErrorCount = (clone $kpiQuery)->whereBetween('status_code', [500, 599])->count();
        $totalErrors = $clientErrorCount + $serverErrorCount;
        $errorRate = $totalRequests > 0 ? round(($totalErrors / $totalRequests) * 100, 1) : 0;

        $avgLatency = (float) ((clone $kpiQuery)->avg('response_time_ms') ?? 0);
        $avgLatency = round($avgLatency, 0);

        $uniqueIps = (clone $kpiQuery)->whereNotNull('ip_address')->distinct('ip_address')->count('ip_address');
        $uniqueUsers = (clone $kpiQuery)->whereNotNull('user_id')->distinct('user_id')->count('user_id');

        // 2. Timeline Chart Data
        $timelineLabels = [];
        $timelineTotalData = [];
        $timelineMobileData = [];
        $timelineWebData = [];

        if ($isHourly) {
            $isSqlite = DB::connection()->getDriverName() === 'sqlite';
            $hourExpr = $isSqlite ? "CAST(strftime('%H', created_at) AS INTEGER)" : "HOUR(created_at)";

            // Group by hour (0 to 23)
            $hourlyData = ApiLog::whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    DB::raw("{$hourExpr} as hour_num"),
                    DB::raw('count(*) as total'),
                    DB::raw("sum(case when platform in ('android', 'ios') then 1 else 0 end) as mobile"),
                    DB::raw("sum(case when platform = 'web' then 1 else 0 end) as web")
                )
                ->groupBy('hour_num')
                ->get()
                ->keyBy('hour_num');

            for ($h = 0; $h < 24; $h++) {
                $label = sprintf('%02d:00', $h);
                $timelineLabels[] = $label;
                $timelineTotalData[] = (int) ($hourlyData[$h]->total ?? 0);
                $timelineMobileData[] = (int) ($hourlyData[$h]->mobile ?? 0);
                $timelineWebData[] = (int) ($hourlyData[$h]->web ?? 0);
            }
        } else {
            // Group by date
            $dailyData = ApiLog::whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    DB::raw('DATE(created_at) as log_date'),
                    DB::raw('count(*) as total'),
                    DB::raw("sum(case when platform in ('android', 'ios') then 1 else 0 end) as mobile"),
                    DB::raw("sum(case when platform = 'web' then 1 else 0 end) as web")
                )
                ->groupBy('log_date')
                ->orderBy('log_date')
                ->get()
                ->keyBy('log_date');

            $currentDay = $startDate->copy();
            while ($currentDay->lte($endDate)) {
                $dateKey = $currentDay->toDateString();
                $timelineLabels[] = $currentDay->format('M d');
                $timelineTotalData[] = (int) ($dailyData[$dateKey]->total ?? 0);
                $timelineMobileData[] = (int) ($dailyData[$dateKey]->mobile ?? 0);
                $timelineWebData[] = (int) ($dailyData[$dateKey]->web ?? 0);
                $currentDay->addDay();
            }
        }

        // 3. Top 10 API Endpoints
        $topEndpoints = ApiLog::whereBetween('created_at', [$startDate, $endDate])
            ->when($platform !== 'all', fn($q) => $q->where('platform', $platform))
            ->select('endpoint', 'method', DB::raw('count(*) as count'), DB::raw('avg(response_time_ms) as avg_latency'))
            ->groupBy('endpoint', 'method')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // 4. Status Codes Distribution
        $statusDistribution = [
            '2xx' => $successfulCount,
            '3xx' => (clone $kpiQuery)->whereBetween('status_code', [300, 399])->count(),
            '4xx' => $clientErrorCount,
            '5xx' => $serverErrorCount,
        ];

        // 5. Paginated Request Logs Table
        $logsQuery = ApiLog::with('user')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($platform !== 'all') {
            $logsQuery->where('platform', $platform);
        }

        if ($statusFilter === 'success') {
            $logsQuery->whereBetween('status_code', [200, 299]);
        } elseif ($statusFilter === 'errors') {
            $logsQuery->where('status_code', '>=', 400);
        }

        if ($search !== '') {
            $logsQuery->where(function ($q) use ($search) {
                $q->where('endpoint', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('app_version', 'like', "%{$search}%")
                    ->orWhere('device_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $logs = $logsQuery->orderByDesc('created_at')->paginate(30)->withQueryString();

        return view('admin.api_usage.index', compact(
            'preset',
            'startDate',
            'endDate',
            'platform',
            'statusFilter',
            'search',
            'totalRequests',
            'mobileCount',
            'mobilePercentage',
            'androidCount',
            'iosCount',
            'webCount',
            'otherCount',
            'successfulCount',
            'totalErrors',
            'errorRate',
            'avgLatency',
            'uniqueIps',
            'uniqueUsers',
            'timelineLabels',
            'timelineTotalData',
            'timelineMobileData',
            'timelineWebData',
            'topEndpoints',
            'statusDistribution',
            'logs'
        ));
    }

    /**
     * Stream CSV export of filtered logs.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $preset = $request->input('preset', 'today');
        $platform = $request->input('platform', 'all');
        $statusFilter = $request->input('status', 'all');
        $search = trim((string) $request->input('search', ''));

        $now = now();
        switch ($preset) {
            case '7d':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case '30d':
                $startDate = $now->copy()->subDays(29)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'custom':
                $startDate = $request->input('start_date') 
                    ? Carbon::parse($request->input('start_date'))->startOfDay() 
                    : $now->copy()->startOfDay();
                $endDate = $request->input('end_date') 
                    ? Carbon::parse($request->input('end_date'))->endOfDay() 
                    : $now->copy()->endOfDay();
                break;
            case 'today':
            default:
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
        }

        $filename = 'api-usage-logs-' . $startDate->format('Ymd') . '-to-' . $endDate->format('Ymd') . '.csv';

        $query = ApiLog::with('user')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($platform !== 'all') {
            $query->where('platform', $platform);
        }

        if ($statusFilter === 'success') {
            $query->whereBetween('status_code', [200, 299]);
        } elseif ($statusFilter === 'errors') {
            $query->where('status_code', '>=', 400);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('endpoint', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('app_version', 'like', "%{$search}%")
                    ->orWhere('device_id', 'like', "%{$search}%");
            });
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'ID',
                'Timestamp',
                'Method',
                'Endpoint',
                'Status Code',
                'Response Time (ms)',
                'Platform',
                'App Version',
                'Device ID',
                'IP Address',
                'User Email',
                'User Agent',
            ]);

            $query->orderByDesc('created_at')->chunk(1000, function ($rows) use ($file) {
                foreach ($rows as $row) {
                    fputcsv($file, [
                        $row->id,
                        $row->created_at->toDateTimeString(),
                        $row->method,
                        $row->endpoint,
                        $row->status_code,
                        $row->response_time_ms,
                        $row->platform,
                        $row->app_version ?? '',
                        $row->device_id ?? '',
                        $row->ip_address ?? '',
                        $row->user?->email ?? '',
                        $row->user_agent ?? '',
                    ]);
                }
            });

            fclose($file);
        }, 200, $headers);
    }
}
