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
     * Display WhatsApp connection status and multi-device hub.
     */
    public function status(WhatsAppClient $client): View
    {
        $devicesResult = $client->listDevices();
        $devices = $devicesResult['devices'] ?? [];
        $activeDeviceId = config('whatsapp.device_id', 'default');
        $status = $client->getDeviceStatus($activeDeviceId);

        $isDev = config('whatsapp.dev_mode', false) || (request()->getHost() === 'egyptna.org' || str_ends_with(request()->getHost(), '.egyptna.org'));
        $devWhitelist = config('whatsapp.dev_whitelist', []);

        return view('whatsapp.device', compact('status', 'devices', 'activeDeviceId', 'isDev', 'devWhitelist'));
    }

    /**
     * Create a new device slot.
     */
    public function store(Request $request, WhatsAppClient $client): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'device_id' => 'required|string|alpha_dash|max:50',
        ]);

        $res = $client->createDevice($validated['device_id']);

        if ($request->wantsJson()) {
            return response()->json($res, $res['success'] ? 200 : 422);
        }

        if ($res['success']) {
            return redirect()->route('whatsapp.device.status')->with('success', __('messages.whatsapp_device_created', ['id' => $validated['device_id']]));
        }

        return redirect()->route('whatsapp.device.status')->with('error', $res['error'] ?? __('messages.whatsapp_device_create_failed'));
    }

    /**
     * Delete a device slot from the microservice.
     */
    public function destroy(string $deviceId, WhatsAppClient $client): JsonResponse|RedirectResponse
    {
        $deleted = $client->deleteDevice($deviceId);

        if (request()->wantsJson()) {
            return response()->json(['success' => $deleted]);
        }

        if ($deleted) {
            return redirect()->route('whatsapp.device.status')->with('success', __('messages.whatsapp_device_deleted'));
        }

        return redirect()->route('whatsapp.device.status')->with('error', __('messages.whatsapp_device_delete_failed'));
    }

    /**
     * Fetch real-time QR code data via JSON for a given device.
     */
    public function qr(Request $request, WhatsAppClient $client): JsonResponse
    {
        $deviceId = $request->query('device_id') ?: config('whatsapp.device_id', 'default');
        $qrData = $client->getLoginQrCode($deviceId);

        return response()->json($qrData);
    }

    /**
     * Real-time live status check for polling (called every 2.5s while QR modal is open).
     */
    public function check(Request $request, WhatsAppClient $client): JsonResponse
    {
        $deviceId = $request->query('device_id') ?: config('whatsapp.device_id', 'default');
        $status = $client->getDeviceStatus($deviceId);

        return response()->json($status);
    }

    /**
     * Reconnect the device session.
     */
    public function reconnect(Request $request, WhatsAppClient $client): RedirectResponse
    {
        $deviceId = $request->input('device_id') ?: config('whatsapp.device_id', 'default');
        $client->reconnectDevice($deviceId);

        return redirect()->route('whatsapp.device.status')->with('success', __('messages.whatsapp_reconnect'));
    }

    /**
     * Disconnect / logout device session.
     */
    public function logout(Request $request, WhatsAppClient $client): RedirectResponse
    {
        $deviceId = $request->input('device_id') ?: config('whatsapp.device_id', 'default');
        $client->logoutDevice($deviceId);

        return redirect()->route('whatsapp.device.status')->with('success', __('messages.whatsapp_logout'));
    }

    /**
     * Send test message to a specified number using chosen device.
     */
    public function testMessage(Request $request, WhatsAppClient $client): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:1000',
            'device_id' => 'nullable|string',
        ]);

        $deviceId = $validated['device_id'] ?: config('whatsapp.device_id', 'default');
        $res = $client->sendTextMessage($validated['phone'], $validated['message'], $deviceId);

        if ($res['success'] ?? false) {
            $msg = ($res['dev_skipped'] ?? false)
                ? 'Test message skipped in Dev Mode (phone not in whitelist)'
                : __('messages.whatsapp_test_success');
            return redirect()->route('whatsapp.device.status')->with('success', $msg);
        }

        return redirect()->route('whatsapp.device.status')->with('error', $res['error'] ?? 'Failed to send message.');
    }
}
