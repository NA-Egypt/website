<?php

namespace App\Jobs;

use App\Models\WhatsAppBroadcastLog;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\WhatsApp\WhatsAppClient;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppBulkCsvBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $broadcastLogId;
    public array $recipients;
    public string $sendMode;
    public ?string $defaultMessage;
    public string $antiBanProfile;
    public bool $enableCooldown;
    public bool $appendOptout;
    public ?string $deviceId;

    /**
     * Timeout for long-running bulk broadcasts.
     */
    public int $timeout = 7200;

    /**
     * Create a new job instance.
     */
    public function __construct(
        int $broadcastLogId,
        array $recipients,
        string $sendMode,
        ?string $defaultMessage = null,
        string $antiBanProfile = 'safe',
        bool $enableCooldown = true,
        bool $appendOptout = false,
        ?string $deviceId = null
    ) {
        $this->broadcastLogId = $broadcastLogId;
        $this->recipients = $recipients;
        $this->sendMode = $sendMode;
        $this->defaultMessage = $defaultMessage;
        $this->antiBanProfile = $antiBanProfile;
        $this->enableCooldown = $enableCooldown;
        $this->appendOptout = $appendOptout;
        $this->deviceId = $deviceId;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppClient $client): void
    {
        $log = WhatsAppBroadcastLog::find($this->broadcastLogId);
        if (!$log) {
            Log::error("[BulkCsvBroadcast] Broadcast log #{$this->broadcastLogId} not found.");
            return;
        }

        $log->update(['status' => 'processing']);

        $sentCount = 0;
        $failedCount = 0;
        $liveLogs = [];
        $total = count($this->recipients);

        // Anti-ban jitter delay ranges in seconds
        [$minDelay, $maxDelay] = match ($this->antiBanProfile) {
            'ultra_safe' => [15, 30],
            'fast' => [4, 8],
            default => [8, 15], // safe
        };

        $batchCounter = 0;

        foreach ($this->recipients as $index => $recipient) {
            // Re-fetch to check if broadcast was cancelled
            $log->refresh();
            if ($log->status === 'cancelled') {
                $this->appendLog($liveLogs, "Broadcast cancelled by administrator at recipient #{$index}.");
                break;
            }

            $phone = $recipient['phone'] ?? '';
            $name = $recipient['name'] ?? '';
            $customMessage = trim($recipient['message'] ?? '');

            // Determine final text
            if ($this->sendMode === 'custom_per_row' && !empty($customMessage)) {
                $text = $customMessage;
            } else {
                $text = $this->defaultMessage ?: $customMessage;
            }

            // Variable replacement
            $text = str_replace(
                ['{name}', '{phone}', '{الاسم}', '{الهاتف}'],
                [$name ?: __('messages.fellowship_member'), $phone, $name ?: __('messages.fellowship_member'), $phone],
                $text
            );

            // Optional opt-out notice
            if ($this->appendOptout) {
                $text .= "\n\n(للإلغاء أرسل: قف)";
            }

            $timeStr = Carbon::now()->format('H:i:s');

            // Send via client using chosen device
            $res = $client->sendTextMessage($phone, $text, $this->deviceId);

            if ($res['success'] ?? false) {
                $sentCount++;

                // Record outgoing message in database
                $jid = $client->formatJid($phone);
                $conversation = WhatsAppConversation::firstOrCreate(
                    ['jid' => $jid],
                    [
                        'phone' => $phone,
                        'name' => $name,
                        'last_interaction_at' => Carbon::now(),
                    ]
                );

                WhatsAppMessage::create([
                    'conversation_id' => $conversation->id,
                    'message_id' => $res['data']['results']['message_id'] ?? null,
                    'direction' => 'outgoing',
                    'sender_type' => 'agent',
                    'category' => 'bulk_csv',
                    'message_type' => 'text',
                    'body' => $text,
                    'status' => ($res['dev_skipped'] ?? false) ? 'dev_skipped' : 'sent',
                    'user_id' => $log->dispatched_by,
                    'raw_payload' => $res,
                ]);

                $statusNote = ($res['dev_skipped'] ?? false) ? '(Dev mode simulated)' : 'Delivered';
                $this->appendLog($liveLogs, "[{$timeStr}] #{$sentCount}/{$total} Sent to {$phone} ({$name}) - {$statusNote}");
            } else {
                $failedCount++;
                $errorMsg = $res['error'] ?? 'Unknown error';
                $this->appendLog($liveLogs, "[{$timeStr}] #{$failedCount} Failed for {$phone}: " . substr($errorMsg, 0, 80));
            }

            $batchCounter++;

            // Update progress in database every iteration for real-time polling
            $metadata = $log->metadata ?? [];
            $metadata['logs'] = array_slice($liveLogs, -40); // keep last 40 entries
            $log->update([
                'successful_count' => $sentCount,
                'failed_count' => $failedCount,
                'metadata' => $metadata,
            ]);

            // Don't delay after the last item
            if ($index < $total - 1) {
                // Batch cooling break (Anti-ban: pause 60s every 25 messages)
                if ($this->enableCooldown && $batchCounter >= 25) {
                    $batchCounter = 0;
                    $this->appendLog($liveLogs, "[Anti-Ban Cooldown] Pausing 60 seconds after 25 messages to protect account from rate-limiting...");
                    $metadata['logs'] = array_slice($liveLogs, -40);
                    $log->update(['metadata' => $metadata]);
                    sleep(60);
                } else {
                    $delay = rand($minDelay, $maxDelay);
                    sleep($delay);
                }
            }
        }

        $log->refresh();
        $finalStatus = ($log->status === 'cancelled') ? 'cancelled' : 'completed';
        $metadata = $log->metadata ?? [];
        $metadata['logs'] = array_slice($liveLogs, -50);

        $log->update([
            'successful_count' => $sentCount,
            'failed_count' => $failedCount,
            'status' => $finalStatus,
            'completed_at' => Carbon::now(),
            'metadata' => $metadata,
        ]);

        Log::info("[BulkCsvBroadcast] Finished broadcast #{$log->id}: {$sentCount} sent, {$failedCount} failed.");
    }

    protected function appendLog(array &$logs, string $entry): void
    {
        $logs[] = $entry;
    }
}
