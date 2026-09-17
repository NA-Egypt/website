<?php

namespace App\Console\Commands;

use App\Models\ApiDailyStat;
use App\Models\ApiLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregateApiDailyStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-logs:aggregate {--date= : The date to aggregate in Y-m-d format (defaults to yesterday)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregate raw API logs into daily summary statistics';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dateStr = $this->option('date') ?: now()->subDay()->toDateString();

        try {
            $targetDate = Carbon::parse($dateStr)->toDateString();
        } catch (\Exception $e) {
            $this->error("Invalid date format provided: {$dateStr}. Use Y-m-d.");
            return Command::FAILURE;
        }

        $startOfDay = Carbon::parse($targetDate)->startOfDay();
        $endOfDay = Carbon::parse($targetDate)->endOfDay();

        $this->info("Aggregating API logs for date: {$targetDate}...");

        $platforms = ['all', 'android', 'ios', 'web', 'other'];

        foreach ($platforms as $platform) {
            $query = ApiLog::whereBetween('created_at', [$startOfDay, $endOfDay]);

            if ($platform !== 'all') {
                $query->where('platform', $platform);
            }

            $total = (clone $query)->count();

            if ($total === 0 && $platform !== 'all') {
                continue;
            }

            $successful = (clone $query)->whereBetween('status_code', [200, 299])->count();
            $clientErrors = (clone $query)->whereBetween('status_code', [400, 499])->count();
            $serverErrors = (clone $query)->whereBetween('status_code', [500, 599])->count();
            $avgLatency = (float) ((clone $query)->avg('response_time_ms') ?? 0);
            $uniqueIps = (clone $query)->whereNotNull('ip_address')->distinct('ip_address')->count('ip_address');
            $uniqueUsers = (clone $query)->whereNotNull('user_id')->distinct('user_id')->count('user_id');

            // Top 10 endpoints for this platform/day
            $topEndpoints = (clone $query)
                ->select('endpoint', DB::raw('count(*) as count'))
                ->groupBy('endpoint')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('count', 'endpoint')
                ->toArray();

            ApiDailyStat::updateOrCreate(
                [
                    'date' => $targetDate,
                    'platform' => $platform,
                ],
                [
                    'total_requests' => $total,
                    'successful_requests' => $successful,
                    'client_error_requests' => $clientErrors,
                    'server_error_requests' => $serverErrors,
                    'avg_response_time_ms' => round($avgLatency, 2),
                    'unique_ips' => $uniqueIps,
                    'unique_users' => $uniqueUsers,
                    'top_endpoints_json' => $topEndpoints,
                ]
            );

            $this->line("  -> Platform [{$platform}]: {$total} requests (Success: {$successful}, Errors: " . ($clientErrors + $serverErrors) . ", Latency: " . round($avgLatency, 2) . "ms)");
        }

        $this->info("Aggregation completed for {$targetDate}.");

        return Command::SUCCESS;
    }
}
