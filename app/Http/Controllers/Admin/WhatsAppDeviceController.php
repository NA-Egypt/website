<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsApp\WhatsAppClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppDeviceController extends Controller
{
    /**
     * Display WhatsApp connection status and pairing interface.
     */
    public function status(WhatsAppClient $client): View
    {
        $status = $client->getDeviceStatus();
        $isDev = config('whatsapp.dev_mode', false) || (request()->getHost() === 'egyptna.org' || str_ends_with(request()->getHost(), '.egyptna.org'));
        $devWhitelist = config('whatsapp.dev_whitelist', []);

        return view('whatsapp.device', compact('status', 'isDev', 'devWhitelist'));
    }

    /**
     * Fetch real-time QR code data via JSON.
     */
    public function qr(WhatsAppClient $client): JsonResponse
    {
        $qrData = $client->getLoginQrCode();
        return response()->json($qrData);
    }

    /**
     * Reconnect the device session.
     */
    public function reconnect(WhatsAppClient $client): RedirectResponse
    {
        $client->reconnectDevice();
        return redirect()->route('whatsapp.device.status')->with('success', __('messages.whatsapp_reconnect'));
    }

    /**
     * Disconnect / logout device session.
     */
    public function logout(WhatsAppClient $client): RedirectResponse
    {
        $client->logoutDevice();
        return redirect()->route('whatsapp.device.status')->with('success', __('messages.whatsapp_logout'));
    }

    /**
     * Send test message to a specified number.
     */
    public function testMessage(Request $request, WhatsAppClient $client): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        $res = $client->sendTextMessage($validated['phone'], $validated['message']);

        if ($res['success'] ?? false) {
            $msg = ($res['dev_skipped'] ?? false)
                ? 'Test message skipped in Dev Mode (phone not in whitelist)'
                : __('messages.whatsapp_test_success');
            return redirect()->route('whatsapp.device.status')->with('success', $msg);
        }

        return redirect()->route('whatsapp.device.status')->with('error', $res['error'] ?? 'Failed to send message.');
    }
}
