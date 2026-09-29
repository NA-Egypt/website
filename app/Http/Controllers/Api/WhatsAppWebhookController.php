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

        // Ensure payload has basic message data
        if (empty($payload)) {
            return response()->json(['status' => 'ignored', 'message' => 'Empty payload'], 200);
        }

        // We offload parsing and response to background queue to ensure fast < 50ms webhook return
        ProcessIncomingWhatsAppMessage::dispatch($payload);

        return response()->json([
            'status' => 'queued',
            'message' => 'Incoming message queued successfully',
        ], 200);
    }
}
