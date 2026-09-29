<?php

namespace App\Jobs;

use App\Services\WhatsApp\WhatsAppBotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessIncomingWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $payload;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppBotService $botService): void
    {
        try {
            $botService->handleIncomingMessage($this->payload);
        } catch (\Throwable $e) {
            Log::error('[ProcessIncomingWhatsAppMessage] Execution failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'payload' => $this->payload,
            ]);
            throw $e;
        }
    }
}
