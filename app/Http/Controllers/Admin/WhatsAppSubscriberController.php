<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsAppBroadcast;
use App\Jobs\SendWhatsAppBulkCsvBroadcast;
use App\Models\WhatsAppBroadcastLog;
use App\Models\WhatsAppSubscriber;
use App\Services\WhatsApp\WhatsAppClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class WhatsAppSubscriberController extends Controller
{
    /**
     * Display broadcast subscribers, bulk sender, and transmission history.
     */
    public function index(Request $request, WhatsAppClient $client): View
    {
        $query = WhatsAppSubscriber::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('phone', 'like', "%{$s}%")
                    ->orWhere('name', 'like', "%{$s}%")
                    ->orWhere('jid', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $subscribers = $query->orderBy('id', 'desc')->paginate(25);

        $broadcastLogs = WhatsAppBroadcastLog::with('dispatcher')
            ->orderBy('id', 'desc')
            ->take(15)
            ->get();

        $stats = [
            'total' => WhatsAppSubscriber::count(),
            'active' => WhatsAppSubscriber::where('is_active', true)->count(),
            'inactive' => WhatsAppSubscriber::where('is_active', false)->count(),
        ];

        // Available connected devices for outbound bulk routing
        $devicesResult = $client->listDevices();
        $devices = $devicesResult['devices'] ?? [];
        $activeDeviceId = config('whatsapp.device_id', 'default');

        $isDev = config('whatsapp.dev_mode', false) || (request()->getHost() === 'egyptna.org' || str_ends_with(request()->getHost(), '.egyptna.org'));

        return view('whatsapp.subscribers', compact('subscribers', 'broadcastLogs', 'stats', 'devices', 'activeDeviceId', 'isDev'));
    }

    /**
     * Download an educational, UTF-8 encoded sample CSV template with sample rows and instructions.
     */
    public function sampleCsv(): Response
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="whatsapp_bulk_contacts_sample.csv"',
        ];

        // UTF-8 BOM for flawless rendering in Microsoft Excel & Sheets with Arabic
        $bom = "\xEF\xBB\xBF";

        $rows = [
            ['phone', 'name', 'message'],
            ['201012345678', 'أحمد محمد', 'أهلاً يا {name}، نود تذكيرك بموعد اجتماع زمالة المدمنين المجهولين اليوم في تمام الساعة 7:00 مساءً.'],
            ['201123456789', 'سارة محمود', 'مرحباً {name}، يسعدنا مشاركة رابط الأدبيات الجديد لزمالة NA مصر: https://naegypt.org'],
            ['201234567890', 'محمود علي', ''], // Empty message to demonstrate fallback to default campaign template
            ['201555667788', 'John Doe', 'Hi {name}, reminder for upcoming service committee meeting.'],
        ];

        $output = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return response($bom . $csvContent, 200, $headers);
    }

    /**
     * Dispatch bulk messages parsed from an uploaded CSV file with anti-ban safeguards.
     */
    public function bulkCsv(Request $request, WhatsAppClient $client): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'csv_file' => 'required|file|max:10240',
            'send_mode' => 'required|in:template_to_all,custom_per_row',
            'default_message' => 'nullable|string|max:4000',
            'device_id' => 'nullable|string',
            'anti_ban_profile' => 'required|in:ultra_safe,safe,fast',
            'enable_cooldown' => 'nullable|boolean',
            'append_optout' => 'nullable|boolean',
        ]);

        if ($validated['send_mode'] === 'template_to_all' && empty(trim($validated['default_message'] ?? ''))) {
            return back()->withErrors(['default_message' => __('messages.whatsapp_default_message_required')]);
        }

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return back()->with('error', __('messages.whatsapp_csv_read_failed'));
        }

        // Detect BOM or skip header
        $rawHeader = fgetcsv($handle);
        if (!$rawHeader) {
            fclose($handle);
            return back()->with('error', __('messages.whatsapp_csv_empty'));
        }

        // Normalize header keys
        $headerMap = [];
        foreach ($rawHeader as $idx => $col) {
            $clean = strtolower(trim(preg_replace('/[\xEF\xBB\xBF]/', '', $col)));
            if (in_array($clean, ['phone', 'mobile', 'رقم الهاتف', 'هاتف', 'تليفون', 'mobile_number', 'number'])) {
                $headerMap['phone'] = $idx;
            } elseif (in_array($clean, ['name', 'الاسم', 'اسم', 'contact_name', 'full_name'])) {
                $headerMap['name'] = $idx;
            } elseif (in_array($clean, ['message', 'الرسالة', 'نص الرسالة', 'msg', 'text', 'content'])) {
                $headerMap['message'] = $idx;
            }
        }

        if (!isset($headerMap['phone'])) {
            fclose($handle);
            return back()->with('error', __('messages.whatsapp_csv_missing_phone_column'));
        }

        $recipients = [];
        $seenPhones = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $rawPhone = $row[$headerMap['phone']] ?? '';
            $phone = $client->normalizePhone($rawPhone);

            if (empty($phone) || strlen($phone) < 8) {
                continue;
            }

            // Deduplication
            if (isset($seenPhones[$phone])) {
                continue;
            }
            $seenPhones[$phone] = true;

            $name = isset($headerMap['name']) ? trim($row[$headerMap['name']] ?? '') : '';
            $message = isset($headerMap['message']) ? trim($row[$headerMap['message']] ?? '') : '';

            $recipients[] = [
                'phone' => $phone,
                'name' => $name,
                'message' => $message,
            ];
        }
        fclose($handle);

        if (empty($recipients)) {
            return back()->with('error', __('messages.whatsapp_csv_no_valid_recipients'));
        }

        $deviceId = $validated['device_id'] ?: config('whatsapp.device_id', 'default');
        $antiBanProfile = $validated['anti_ban_profile'];
        $enableCooldown = $request->boolean('enable_cooldown', true);
        $appendOptout = $request->boolean('append_optout', false);

        // Record Broadcast Log
        $log = WhatsAppBroadcastLog::create([
            'channel' => 'bulk_csv',
            'device_id' => $deviceId,
            'title' => 'Bulk CSV Campaign (' . count($recipients) . ' ' . __('messages.contacts') . ')',
            'total_recipients' => count($recipients),
            'successful_count' => 0,
            'failed_count' => 0,
            'status' => 'processing',
            'anti_ban_profile' => $antiBanProfile,
            'is_dev_broadcast' => (bool) config('whatsapp.dev_mode', false),
            'dispatched_by' => auth()->id(),
            'metadata' => [
                'send_mode' => $validated['send_mode'],
                'default_message' => $validated['default_message'] ?? null,
                'enable_cooldown' => $enableCooldown,
                'append_optout' => $appendOptout,
                'logs' => ["[00:00:00] Initialized bulk campaign for " . count($recipients) . " recipients."],
            ],
        ]);

        SendWhatsAppBulkCsvBroadcast::dispatch(
            $log->id,
            $recipients,
            $validated['send_mode'],
            $validated['default_message'] ?? null,
            $antiBanProfile,
            $enableCooldown,
            $appendOptout,
            $deviceId
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'broadcast_id' => $log->id,
                'total' => count($recipients),
                'message' => __('messages.whatsapp_bulk_dispatched_successfully'),
            ]);
        }

        return redirect()->route('whatsapp.subscribers.index')
            ->with('success', __('messages.whatsapp_bulk_dispatched_successfully'))
            ->with('active_broadcast_id', $log->id);
    }

    /**
     * Poll real-time progress of a bulk broadcast log.
     */
    public function broadcastProgress(WhatsAppBroadcastLog $broadcast): JsonResponse
    {
        $metadata = $broadcast->metadata ?? [];
        $total = $broadcast->total_recipients ?: 1;
        $done = $broadcast->successful_count + $broadcast->failed_count;
        $percent = min(100, round(($done / $total) * 100));

        return response()->json([
            'id' => $broadcast->id,
            'status' => $broadcast->status,
            'total' => $broadcast->total_recipients,
            'successful' => $broadcast->successful_count,
            'failed' => $broadcast->failed_count,
            'percent' => $percent,
            'logs' => $metadata['logs'] ?? [],
            'is_finished' => in_array($broadcast->status, ['completed', 'failed', 'cancelled']),
        ]);
    }

    /**
     * Cancel an ongoing bulk broadcast.
     */
    public function cancelBroadcast(WhatsAppBroadcastLog $broadcast): JsonResponse|RedirectResponse
    {
        if ($broadcast->status === 'processing') {
            $broadcast->update(['status' => 'cancelled']);
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'status' => 'cancelled']);
        }

        return back()->with('success', __('messages.whatsapp_broadcast_cancelled'));
    }

    /**
     * Dispatch standard subscriber broadcast.
     */
    public function triggerBroadcast(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'channel' => 'required|string',
            'title' => 'required|string|max:150',
            'message' => 'required|string|max:4000',
        ]);

        SendWhatsAppBroadcast::dispatch(
            $validated['channel'],
            $validated['title'],
            $validated['message'],
            auth()->id()
        );

        return redirect()->route('whatsapp.subscribers.index')
            ->with('success', __('messages.whatsapp_broadcast_queued'));
    }
}
