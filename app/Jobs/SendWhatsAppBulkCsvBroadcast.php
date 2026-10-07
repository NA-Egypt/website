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
        ?string $deviceId = null,
        ?int $sessionCap = null
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
                    $meta['session_cap'] = $sessionCap ?? match ($antiBanProfile) {
                        'warmup' => 20,
                        'ultra_safe' => 35,
                        'safe' => 60,
                        default => 100,
                    };
                    $meta['session_sent_count'] = 0;
                    $meta['unregistered_count'] = 0;
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

        // Session Safety Cap Guardrail: Prevent WhatsApp 24h restrictions by pacing campaigns in batches
        $sessionCap = (int) ($meta['session_cap'] ?? match ($meta['anti_ban_profile'] ?? 'safe') {
            'warmup' => 20,
            'ultra_safe' => 35,
            'safe' => 60,
            default => 100,
        });
        $sessionSentCount = (int) ($meta['session_sent_count'] ?? 0);

        if ($sessionCap > 0 && $sessionSentCount >= $sessionCap) {
            $timeStr = Carbon::now()->format('H:i:s');
            $meta['logs'] = $meta['logs'] ?? [];
            $meta['logs'][] = "[{$timeStr}] 🛑 Safety Session Limit Reached: {$sessionSentCount}/{$sessionCap} messages sent in this batch. Campaign automatically PAUSED to protect account from 24h restrictions. You can resume safely after a rest period.";
            $meta['logs'] = array_slice($meta['logs'], -200);

            $log->update([
                'status' => 'paused',
                'metadata' => $meta,
            ]);
            Log::info("[BulkCsvBroadcast] Broadcast #{$log->id} paused: Session cap ({$sessionCap}) reached.");
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

        // Pre-flight check: Verify if number is registered on WhatsApp to prevent INVALID_JID ban trigger
        $userCheck = $client->checkUser($phone, $deviceId);
        if (($userCheck['success'] ?? false) && ($userCheck['is_on_whatsapp'] === false)) {
            $meta['logs'] = $meta['logs'] ?? [];
            $meta['logs'][] = "[{$timeStr}] #{$stepNum}/{$total} ⏭️ Skipped {$phone} ({$name}) - Number is NOT registered on WhatsApp (prevented INVALID_JID ban trigger)";
            $meta['unregistered_count'] = ($meta['unregistered_count'] ?? 0) + 1;
            $meta['current_index'] = $currentIndex + 1;
            $meta['logs'] = array_slice($meta['logs'], -200);
            $log->update(['metadata' => $meta]);

            // Dispatch next recipient after 1 second without waiting full delay
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

        // Render Spintax dynamic variation {phrase1|phrase2} for non-fingerprinted messaging
        $text = $client->renderSpintax($text);

        if (!empty($meta['append_optout'])) {
            $text .= "\n\n(للإلغاء أرسل: قف)";
        }

        // Realistic human typing simulation ("typing..." presence)
        $client->sendChatPresence($phone, 'start', $deviceId);
        $typingPauseSeconds = min(5, max(2, (int) round(mb_strlen($text) / 60)));
        sleep($typingPauseSeconds);

        // Send message via client
        $res = $client->sendTextMessage($phone, $text, $deviceId);

        // Stop typing presence
        $client->sendChatPresence($phone, 'stop', $deviceId);

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
            $sessionSentCount++;
            $meta['session_sent_count'] = $sessionSentCount;
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
            'warmup' => [60, 120, 10, 360],    // Conservative 1-2m jitter, 6m break after 10 messages
            'ultra_safe' => [45, 90, 15, 300],  // Strict 45-90s jitter, 5m break after 15 messages
            'fast' => [6, 12, 30, 45],          // Fast for opted-in lists
            default => [20, 40, 20, 120],       // Safe: 20-40s jitter, 2m break after 20 messages
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
