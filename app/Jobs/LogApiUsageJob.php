<?php

namespace App\Jobs;

use App\Models\ApiLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LogApiUsageJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $logData)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            ApiLog::create($this->logData);
        } catch (\Throwable $e) {
            Log::warning('LogApiUsageJob failed to insert log: ' . $e->getMessage());
        }
    }
}
