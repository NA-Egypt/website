<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessIncomingWhatsAppMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Handle incoming webhook requests from the WhatsApp microservice.
     */
    public function handle(Request $request): JsonResponse
    {
        $secret = config('whatsapp.webhook_secret');
        if (!empty($secret)) {
            $headerSecret = $request->header('X-Webhook-Secret') ?? $request->query('token');
            if ($headerSecret !== $secret) {
                Log::warning('[WhatsAppWebhook] Unauthorized webhook attempt: secret mismatch.');
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        $payload = $request->all();

        // Ensure payload has basic data
        if (empty($payload)) {
            return response()->json(['status' => 'ignored', 'message' => 'Empty payload'], 200);
        }

        $event = $payload['event'] ?? 'message';

        // 1. Handle delivery/read receipts (message.ack)
        if ($event === 'message.ack') {
            $inner = $payload['payload'] ?? $payload['data'] ?? $payload;
            $msgId = $inner['id'] ?? $inner['message_id'] ?? null;
            $ackStatus = $inner['status'] ?? null;

            if ($msgId && $ackStatus) {
                $statusMap = [
                    1 => 'sent',
                    2 => 'delivered',
                    3 => 'read',
                    4 => 'played',
                ];
                $newStatus = $statusMap[$ackStatus] ?? null;

                if ($newStatus) {
                    \App\Models\WhatsAppMessage::where('message_id', $msgId)->update(['status' => $newStatus]);
                }
            }

            return response()->json(['status' => 'acknowledged', 'event' => 'message.ack'], 200);
        }

        // 2. Ignore non-message noisy events (e.g., presence pulses, chat notifications)
        if ($event !== 'message') {
            return response()->json(['status' => 'ignored', 'event' => $event], 200);
        }

        // 3. Dispatch message processing to queue (< 50ms webhook response)
        ProcessIncomingWhatsAppMessage::dispatch($payload);

        return response()->json([
            'status' => 'queued',
            'message' => 'Incoming message queued successfully',
        ], 200);
    }
}
