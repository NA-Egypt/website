<?php

namespace App\Jobs;

use App\Models\WhatsAppBroadcastLog;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppSubscriber;
use App\Services\WhatsApp\WhatsAppClient;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $channel;
    public string $title;
    public string $messageText;
    public ?int $dispatchedByUserId;

    public int $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(string $channel, string $title, string $messageText, ?int $dispatchedByUserId = null)
    {
        $this->channel = $channel;
        $this->title = $title;
        $this->messageText = $messageText;
        $this->dispatchedByUserId = $dispatchedByUserId;
    }

    /**
     * Execute the broadcast job with anti-ban throttling.
     */
    public function handle(WhatsAppClient $client): void
    {
        $isDev = (bool) config('whatsapp.dev_mode', false);

        $query = WhatsAppSubscriber::active()->channel($this->channel);
        if ($isDev) {
            $query->where('is_dev_subscriber', true);
        }

        $totalCount = $query->count();

        $log = WhatsAppBroadcastLog::create([
            'channel' => $this->channel,
            'title' => $this->title,
            'total_recipients' => $totalCount,
            'successful_count' => 0,
            'failed_count' => 0,
            'is_dev_broadcast' => $isDev,
            'dispatched_by' => $this->dispatchedByUserId,
            'completed_at' => null,
        ]);

        if ($totalCount === 0) {
            $log->update(['completed_at' => Carbon::now()]);
            Log::info("[SendWhatsAppBroadcast] No active subscribers found for channel: {$this->channel}");
            return;
        }

        $minDelay = config('whatsapp.broadcast_delay_min_seconds', 2);
        $maxDelay = config('whatsapp.broadcast_delay_max_seconds', 5);

        $successCount = 0;
        $failedCount = 0;

        $query->chunk(50, function ($subscribers) use ($client, &$successCount, &$failedCount, $minDelay, $maxDelay, $isDev) {
            foreach ($subscribers as $subscriber) {
                // Send message via client
                $res = $client->sendTextMessage($subscriber->jid, $this->messageText);

                $status = ($res['success'] ?? false) ? 'sent' : 'failed';
                if ($status === 'sent') {
                    $successCount++;
                } else {
                    $failedCount++;
                }

                // Log outbound broadcast message under the conversation
                $conversation = WhatsAppConversation::firstOrCreate(
                    ['jid' => $subscriber->jid],
                    [
                        'phone' => $subscriber->phone,
                        'name' => $subscriber->name,
                        'is_dev_test' => $isDev,
                        'last_interaction_at' => Carbon::now(),
                    ]
                );

                WhatsAppMessage::create([
                    'conversation_id' => $conversation->id,
                    'message_id' => $res['data']['results']['message_id'] ?? null,
                    'direction' => 'outgoing',
                    'sender_type' => 'bot',
                    'category' => 'broadcast',
                    'message_type' => 'text',
                    'body' => $this->messageText,
                    'status' => $status,
                    'user_id' => $this->dispatchedByUserId,
                    'raw_payload' => $res,
                ]);

                // Anti-spam jitter delay
                $jitterSeconds = random_int($minDelay, $maxDelay);
                sleep($jitterSeconds);
            }
        });

        $log->update([
            'successful_count' => $successCount,
            'failed_count' => $failedCount,
            'completed_at' => Carbon::now(),
        ]);

        Log::info("[SendWhatsAppBroadcast] Completed broadcast '{$this->title}': {$successCount} sent, {$failedCount} failed.");
    }
}
