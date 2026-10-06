<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppClient
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout;
    protected bool $devMode;
    protected string $deviceId;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('whatsapp.api_url', 'http://127.0.0.1:3000'), '/');
        $this->apiKey = config('whatsapp.api_key', '');
        $this->deviceId = config('whatsapp.device_id', 'default');
        $this->timeout = config('whatsapp.timeout', 15);
        $this->devMode = (bool) config('whatsapp.dev_mode', false);
        $this->devWhitelist = config('whatsapp.dev_whitelist', []);
    }

    /**
     * Build an authenticated HTTP client instance.
     */
    protected function client(?string $deviceId = null)
    {
        $targetDevice = $deviceId ?: $this->deviceId;

        return Http::timeout($this->timeout)
            ->withBasicAuth('naegypt', $this->apiKey)
            ->withHeader('X-Device-Id', $targetDevice)
            ->acceptJson();
    }

    /**
     * List all registered devices in the microservice.
     */
    public function listDevices(): array
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/devices");
            if ($response->successful()) {
                $json = $response->json();
                return [
                    'success' => true,
                    'devices' => $json['results'] ?? [],
                ];
            }

            return [
                'success' => false,
                'devices' => [],
                'error' => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::warning('[WhatsAppClient] listDevices failed: ' . $e->getMessage());
            return [
                'success' => false,
                'devices' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a new device slot in the microservice registry.
     */
    public function createDevice(string $deviceId): array
    {
        try {
            $cleanId = preg_replace('/[^a-zA-Z0-9_-]/', '', trim($deviceId));
            if (empty($cleanId)) {
                return ['success' => false, 'error' => 'Invalid device ID.'];
            }

            $response = $this->client($cleanId)->post("{$this->baseUrl}/devices", [
                'device_id' => $cleanId,
            ]);

            if ($response->successful()) {
                // Ensure webhook is configured on the new device
                $webhookUrl = config('whatsapp.webhook_url');
                if (!empty($webhookUrl)) {
                    $this->client($cleanId)->patch("{$this->baseUrl}/devices/{$cleanId}/webhook", [
                        'webhook_url' => $webhookUrl,
                    ]);
                }

                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('[WhatsAppClient] createDevice exception: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Remove / delete a device slot from the microservice.
     */
    public function deleteDevice(string $deviceId): bool
    {
        try {
            $response = $this->client($deviceId)->delete("{$this->baseUrl}/devices/{$deviceId}");
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('[WhatsAppClient] deleteDevice exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ensure the target WhatsApp device exists in the microservice registry.
     */
    public function ensureDeviceExists(?string $deviceId = null): bool
    {
        $targetDevice = $deviceId ?: $this->deviceId;

        try {
            $statusCheck = $this->client($targetDevice)->get("{$this->baseUrl}/devices/{$targetDevice}/status");
            if (!$statusCheck->successful()) {
                // Create device slot if not found
                $create = $this->client($targetDevice)->post("{$this->baseUrl}/devices", [
                    'device_id' => $targetDevice,
                ]);

                if (!$create->successful()) {
                    return false;
                }
            }

            // Sync webhook callback URL to device
            $webhookUrl = config('whatsapp.webhook_url');
            if (!empty($webhookUrl)) {
                $this->client($targetDevice)->patch("{$this->baseUrl}/devices/{$targetDevice}/webhook", [
                    'webhook_url' => $webhookUrl,
                ]);
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning("[WhatsAppClient] ensureDeviceExists exception: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Normalize a phone number or JID to standard international digits.
     */
    public function normalizePhone(string $phoneOrJid): string
    {
        $clean = preg_replace('/@.*$/', '', $phoneOrJid);
        $clean = preg_replace('/[^0-9]/', '', $clean);
        return $clean;
    }

    /**
     * Format a phone number into a standard WhatsApp JID or phone param.
     */
    public function formatJid(string $phoneOrJid): string
    {
        if (str_contains($phoneOrJid, '@')) {
            return $phoneOrJid;
        }
        $digits = $this->normalizePhone($phoneOrJid);
        return $digits . '@s.whatsapp.net';
    }

    /**
     * Check if a recipient is allowed to receive outbound messages in Dev/Staging mode.
     */
    public function isRecipientAllowed(string $phoneOrJid): bool
    {
        if (!$this->devMode) {
            return true;
        }

        $phone = $this->normalizePhone($phoneOrJid);
        if (empty($this->devWhitelist)) {
            Log::warning("[WhatsApp Dev Mode] Outbound blocked to {$phone}: No dev whitelist numbers configured.");
            return false;
        }

        foreach ($this->devWhitelist as $allowed) {
            $allowedDigits = $this->normalizePhone($allowed);
            if ($phone === $allowedDigits) {
                return true;
            }
        }

        Log::info("[WhatsApp Dev Mode] Outbound skipped for {$phone}: Number not in dev whitelist.");
        return false;
    }

    /**
     * Get paired devices and connection health.
     */
    public function getDeviceStatus(?string $deviceId = null): array
    {
        $targetDevice = $deviceId ?: $this->deviceId;

        try {
            $this->ensureDeviceExists($targetDevice);
            $response = $this->client($targetDevice)->get("{$this->baseUrl}/devices/{$targetDevice}/status");
            if ($response->successful()) {
                $json = $response->json();
                $results = $json['results'] ?? [];
                $isConnected = !empty($results['is_connected']) || !empty($results['is_logged_in']);

                return [
                    'success' => true,
                    'connected' => $isConnected,
                    'data' => $json,
                ];
            }

            return [
                'success' => false,
                'connected' => false,
                'error' => $response->body(),
                'status' => $response->status(),
            ];
        } catch (\Throwable $e) {
            Log::warning('[WhatsAppClient] getDeviceStatus failed: ' . $e->getMessage());
            return [
                'success' => false,
                'connected' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get QR code for login / pairing.
     */
    public function getLoginQrCode(?string $deviceId = null): ?array
    {
        $targetDevice = $deviceId ?: $this->deviceId;

        try {
            $this->ensureDeviceExists($targetDevice);
            $response = $this->client($targetDevice)->get("{$this->baseUrl}/devices/{$targetDevice}/login");
            
            // Check if device is already logged in
            if ($response->status() === 400 && str_contains($response->body(), 'ALREADY_LOGGED_IN')) {
                return [
                    'success' => false,
                    'already_logged_in' => true,
                    'message' => 'Device is already paired and logged in to WhatsApp.',
                ];
            }

            if ($response->successful()) {
                $json = $response->json();
                $qrLink = $json['results']['qr_link'] ?? null;
                $qrImage = $json['results']['qr_image'] ?? null;

                // If a qr_link URL is returned on localhost, fetch and convert to base64 data URI for direct browser rendering
                if (!$qrImage && $qrLink) {
                    try {
                        $imgResponse = Http::timeout(5)->get($qrLink);
                        if ($imgResponse->successful()) {
                            $qrImage = 'data:image/png;base64,' . base64_encode($imgResponse->body());
                        }
                    } catch (\Throwable $imgEx) {
                        Log::warning("[WhatsAppClient] could not convert qr_link to base64: {$imgEx->getMessage()}");
                    }
                }

                return [
                    'success' => true,
                    'qr_image' => $qrImage ?: $qrLink,
                    'qr_duration' => $json['results']['qr_duration'] ?? 30,
                    'data' => $json,
                ];
            }

            return [
                'success' => false,
                'error' => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('[WhatsAppClient] getLoginQrCode failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Trigger session reconnect.
     */
    public function reconnectDevice(?string $deviceId = null): bool
    {
        $targetDevice = $deviceId ?: $this->deviceId;

        try {
            $this->ensureDeviceExists($targetDevice);
            $response = $this->client($targetDevice)->post("{$this->baseUrl}/devices/{$targetDevice}/reconnect");
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('[WhatsAppClient] reconnectDevice failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Disconnect / logout WhatsApp device session.
     */
    public function logoutDevice(?string $deviceId = null): bool
    {
        $targetDevice = $deviceId ?: $this->deviceId;

        try {
            $this->ensureDeviceExists($targetDevice);
            $response = $this->client($targetDevice)->post("{$this->baseUrl}/devices/{$targetDevice}/logout");
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('[WhatsAppClient] logoutDevice failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a text message to a WhatsApp user or group.
     */
    public function sendTextMessage(string $phoneOrJid, string $message, ?string $deviceId = null): array
    {
        if (!$this->isRecipientAllowed($phoneOrJid)) {
            return [
                'success' => true,
                'dev_skipped' => true,
                'message' => 'Skipped in dev mode (number not whitelisted)',
            ];
        }

        $phone = $this->normalizePhone($phoneOrJid);
        $targetDevice = $deviceId ?: $this->deviceId;

        try {
            $response = $this->client($targetDevice)->post("{$this->baseUrl}/send/message", [
                'phone' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error("[WhatsAppClient] sendTextMessage HTTP {$response->status()}: " . $response->body());
            return [
                'success' => false,
                'status' => $response->status(),
                'error' => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('[WhatsAppClient] sendTextMessage exception: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send an image with caption.
     */
    public function sendImageMessage(string $phoneOrJid, string $imageUrl, string $caption = ''): array
    {
        if (!$this->isRecipientAllowed($phoneOrJid)) {
            return [
                'success' => true,
                'dev_skipped' => true,
                'message' => 'Skipped in dev mode (number not whitelisted)',
            ];
        }

        $phone = $this->normalizePhone($phoneOrJid);

        try {
            $response = $this->client()->post("{$this->baseUrl}/send/image", [
                'phone' => $phone,
                'image' => $imageUrl,
                'caption' => $caption,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'status' => $response->status(),
                'error' => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('[WhatsAppClient] sendImageMessage exception: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
