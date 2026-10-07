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

    /**
     * Set max attempts to 1 to prevent automatic re-queueing of the same step.
     */
    public int $tries = 1;

    /**
     * Timeout for each single-recipient step (well below the 90-second worker limit).
     */
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        int $broadcastLogId,
        ?array $recipients = null,
        ?string $sendMode = null,
        ?string $defaultMessage = null,
        string $antiBanProfile = 'safe',
        bool $enableCooldown = true,
        bool $appendOptout = false,
        ?string $deviceId = null
    ) {
        $this->broadcastLogId = $broadcastLogId;

        // If recipients are passed explicitly (e.g. from controller or test), store them in broadcast metadata
        if ($recipients !== null) {
            $log = WhatsAppBroadcastLog::find($broadcastLogId);
            if ($log) {
                $meta = $log->metadata ?? [];
                if (!isset($meta['recipients'])) {
                    $meta['recipients'] = $recipients;
                    $meta['send_mode'] = $sendMode ?? 'template_to_all';
                    $meta['default_message'] = $defaultMessage;
                    $meta['anti_ban_profile'] = $antiBanProfile;
                    $meta['enable_cooldown'] = $enableCooldown;
                    $meta['append_optout'] = $appendOptout;
                    $meta['device_id'] = $deviceId;
                    $meta['current_index'] = 0;
                    $meta['cooldown_counter'] = 0;
                    $log->update(['metadata' => $meta]);
                }
            }
        }
    }

    /**
     * Execute the single-recipient step.
     */
    public function handle(WhatsAppClient $client): void
    {
        $log = WhatsAppBroadcastLog::find($this->broadcastLogId);
        if (!$log) {
            Log::error("[BulkCsvBroadcast] Broadcast log #{$this->broadcastLogId} not found.");
            return;
        }

        // Halt if broadcast was cancelled, paused, or completed
        if (in_array($log->status, ['cancelled', 'completed', 'paused', 'failed'])) {
            return;
        }

        $meta = $log->metadata ?? [];
        $recipients = $meta['recipients'] ?? [];
        $currentIndex = (int) ($meta['current_index'] ?? 0);
        $total = count($recipients);

        // Check if all recipients have been processed
        if ($currentIndex >= $total) {
            $timeStr = Carbon::now()->format('H:i:s');
            $meta['logs'] = $meta['logs'] ?? [];
            $meta['logs'][] = "[{$timeStr}] 🎉 Campaign completed successfully. Total: {$total}, Sent: {$log->successful_count}, Failed: {$log->failed_count}.";
            $meta['logs'] = array_slice($meta['logs'], -200);

            $log->update([
                'status' => 'completed',
                'completed_at' => Carbon::now(),
                'metadata' => $meta,
            ]);
            Log::info("[BulkCsvBroadcast] Broadcast #{$log->id} completed successfully.");
            return;
        }

        $deviceId = $log->device_id ?: ($meta['device_id'] ?? config('whatsapp.device_id', 'default'));

        // Pre-send check: verify that device is active & logged in
        $deviceHealth = $client->getDeviceStatus($deviceId);
        if (!($deviceHealth['connected'] ?? false)) {
            $timeStr = Carbon::now()->format('H:i:s');
            $meta['logs'] = $meta['logs'] ?? [];
            $meta['logs'][] = "[{$timeStr}] ⚠️ Device '{$deviceId}' is disconnected or restricted. Campaign automatically PAUSED to protect account.";
            $meta['logs'] = array_slice($meta['logs'], -200);

            $log->update([
                'status' => 'paused',
                'metadata' => $meta,
            ]);
            Log::warning("[BulkCsvBroadcast] Broadcast #{$log->id} paused: Device '{$deviceId}' is disconnected.");
            return;
        }

        // Get current recipient data
        $recipient = $recipients[$currentIndex];
        $phone = $recipient['phone'] ?? '';
        $name = $recipient['name'] ?? '';
        $customMsg = trim($recipient['message'] ?? '');
        $timeStr = Carbon::now()->format('H:i:s');
        $stepNum = $currentIndex + 1;

        // Check for recent duplicate send (safety guardrail within 48h)
        $jid = $client->formatJid($phone);
        $alreadySent = WhatsAppMessage::where('direction', 'outgoing')
            ->where('category', 'bulk_csv')
            ->where('status', 'sent')
            ->whereHas('conversation', function ($q) use ($jid, $phone) {
                $q->where('jid', $jid)->orWhere('phone', $phone);
            })
            ->where('created_at', '>=', Carbon::now()->subHours(48))
            ->exists();

        if ($alreadySent && ($meta['exclude_recent'] ?? true)) {
            $meta['logs'] = $meta['logs'] ?? [];
            $meta['logs'][] = "[{$timeStr}] #{$stepNum}/{$total} ⏭️ Skipped {$phone} ({$name}) - Already messaged in recent campaign";
            $meta['skipped_count'] = ($meta['skipped_count'] ?? 0) + 1;
            $meta['current_index'] = $currentIndex + 1;
            $meta['logs'] = array_slice($meta['logs'], -200);
            $log->update(['metadata' => $meta]);

            // Dispatch next recipient after 1 second
            self::dispatch($log->id)->delay(Carbon::now()->addSeconds(1));
            return;
        }

        // Prepare message text
        $sendMode = $meta['send_mode'] ?? 'template_to_all';
        $defaultMsg = $meta['default_message'] ?? '';
        if ($sendMode === 'custom_per_row' && !empty($customMsg)) {
            $text = $customMsg;
        } else {
            $text = $defaultMsg ?: $customMsg;
        }

        $text = str_replace(
            ['{name}', '{phone}', '{الاسم}', '{الهاتف}'],
            [$name ?: __('messages.fellowship_member'), $phone, $name ?: __('messages.fellowship_member'), $phone],
            $text
        );

        if (!empty($meta['append_optout'])) {
            $text .= "\n\n(للإلغاء أرسل: قف)";
        }

        // Send message via client
        $res = $client->sendTextMessage($phone, $text, $deviceId);

        // Check if device disconnected during dispatch
        $errorMsg = $res['error'] ?? '';
        if (!($res['success'] ?? false) && (
            str_contains($errorMsg, 'INVALID_WA_CLI') ||
            str_contains($errorMsg, 'disconnected') ||
            str_contains($errorMsg, 'unlinked') ||
            ($res['status'] ?? 0) === 401
        )) {
            $meta['logs'] = $meta['logs'] ?? [];
            $meta['logs'][] = "[{$timeStr}] ⚠️ Device '{$deviceId}' disconnected during send. Campaign PAUSED at contact #{$stepNum}.";
            $meta['logs'] = array_slice($meta['logs'], -200);

            $log->update([
                'status' => 'paused',
                'metadata' => $meta,
            ]);
            Log::warning("[BulkCsvBroadcast] Broadcast #{$log->id} paused: Disconnected on send.");
            return;
        }

        $meta['logs'] = $meta['logs'] ?? [];

        if ($res['success'] ?? false) {
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

            $log->increment('successful_count');
            $statusNote = ($res['dev_skipped'] ?? false) ? '(Dev mode simulated)' : 'Delivered';
            $meta['logs'][] = "[{$timeStr}] #{$stepNum}/{$total} ✅ Sent to {$phone} ({$name}) - {$statusNote}";
        } else {
            $log->increment('failed_count');
            $shortErr = substr($errorMsg ?: 'Unknown error', 0, 70);
            $meta['logs'][] = "[{$timeStr}] #{$stepNum}/{$total} ❌ Failed for {$phone}: {$shortErr}";
        }

        // Advance to next index
        $nextIndex = $currentIndex + 1;
        $meta['current_index'] = $nextIndex;

        // If that was the last contact, complete the campaign
        if ($nextIndex >= $total) {
            $meta['logs'][] = "[{$timeStr}] 🎉 Campaign finished completely!";
            $meta['logs'] = array_slice($meta['logs'], -200);

            $log->update([
                'status' => 'completed',
                'completed_at' => Carbon::now(),
                'metadata' => $meta,
            ]);
            Log::info("[BulkCsvBroadcast] Broadcast #{$log->id} reached end of recipient list.");
            return;
        }

        // Calculate anti-ban delay for next contact
        $profile = $meta['anti_ban_profile'] ?? 'safe';
        [$minDelay, $maxDelay, $cooldownThreshold, $cooldownSeconds] = match ($profile) {
            'warmup' => [5, 10, 15, 120],
            'ultra_safe' => [15, 30, 20, 90],
            'fast' => [4, 8, 30, 45],
            default => [8, 15, 25, 60],
        };
        $delay = rand($minDelay, $maxDelay);

        // Check periodic cooldown
        $cooldownCounter = (int) ($meta['cooldown_counter'] ?? 0) + 1;
        $enableCooldown = $meta['enable_cooldown'] ?? true;
        if ($enableCooldown && $cooldownCounter >= $cooldownThreshold) {
            $delay += $cooldownSeconds;
            $cooldownCounter = 0;
            $meta['logs'][] = "[{$timeStr}] ☕ Anti-Ban Cooldown: pausing {$cooldownSeconds}s after {$cooldownThreshold} messages to protect account and mimic human behavior...";
        }
        $meta['cooldown_counter'] = $cooldownCounter;

        $meta['logs'] = array_slice($meta['logs'], -200);
        $log->update(['metadata' => $meta]);

        // Dispatch next recipient as a discrete job scheduled in Redis after $delay seconds
        self::dispatch($log->id)->delay(Carbon::now()->addSeconds($delay));
    }
}
