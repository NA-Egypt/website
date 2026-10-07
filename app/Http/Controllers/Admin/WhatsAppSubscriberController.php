<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsAppBroadcast;
use App\Jobs\SendWhatsAppBulkCsvBroadcast;
use App\Models\WhatsAppBroadcastLog;
use App\Models\WhatsAppSubscriber;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\WhatsApp\WhatsAppClient;
use Carbon\Carbon;
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

        $conventionAttendeesLog = WhatsAppBroadcastLog::where('channel', 'convention_attendees')->latest()->first()
            ?: WhatsAppBroadcastLog::find((int) config('whatsapp.campaign_11_log_id', 11));

        return view('whatsapp.subscribers', compact('subscribers', 'broadcastLogs', 'stats', 'devices', 'activeDeviceId', 'isDev', 'conventionAttendeesLog'));
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
            'anti_ban_profile' => 'required|in:warmup,ultra_safe,safe,fast',
            'session_cap' => 'nullable|integer|min:5|max:500',
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

        // Filter out contacts who already received a bulk message recently if enabled
        $excludeRecent = $request->boolean('exclude_recent', true);
        $excludeHours = (int) $request->input('exclude_hours', 48);
        $recentlySentPhones = [];

        if ($excludeRecent) {
            $recentlySentConvs = WhatsAppMessage::where('category', 'bulk_csv')
                ->where('status', 'sent')
                ->where('created_at', '>=', Carbon::now()->subHours($excludeHours))
                ->pluck('conversation_id')
                ->unique();

            if ($recentlySentConvs->isNotEmpty()) {
                $recentlySentPhones = WhatsAppConversation::whereIn('id', $recentlySentConvs)
                    ->pluck('phone')
                    ->map(fn($p) => $client->normalizePhone($p))
                    ->flip()
                    ->toArray();
            }
        }

        $recipients = [];
        $seenPhones = [];
        $skippedCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $rawPhone = $row[$headerMap['phone']] ?? '';
            $phone = $client->normalizePhone($rawPhone);

            if (empty($phone) || strlen($phone) < 8) {
                continue;
            }

            // Deduplication within the CSV file
            if (isset($seenPhones[$phone])) {
                continue;
            }
            $seenPhones[$phone] = true;

            // Exclude if already sent in the last 48 hours
            if ($excludeRecent && isset($recentlySentPhones[$phone])) {
                $skippedCount++;
                continue;
            }

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
            if ($skippedCount > 0) {
                return back()->with('error', __('messages.whatsapp_csv_all_already_sent', ['count' => $skippedCount]));
            }
            return back()->with('error', __('messages.whatsapp_csv_no_valid_recipients'));
        }

        $deviceId = $validated['device_id'] ?: config('whatsapp.device_id', 'default');
        $antiBanProfile = $validated['anti_ban_profile'];
        $sessionCap = $request->filled('session_cap')
            ? (int) $request->input('session_cap')
            : match ($antiBanProfile) {
                'warmup' => 20,
                'ultra_safe' => 35,
                'safe' => 60,
                default => 100,
            };
        $enableCooldown = $request->boolean('enable_cooldown', true);
        $appendOptout = $request->boolean('append_optout', false);

        $initLogMsg = "[00:00:00] Initialized campaign for " . count($recipients) . " recipients (safety batch cap: {$sessionCap})";
        if ($skippedCount > 0) {
            $initLogMsg .= " (automatically excluded {$skippedCount} contacts who already received this campaign in past {$excludeHours}h).";
        } else {
            $initLogMsg .= ".";
        }

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
                'recipients' => $recipients,
                'current_index' => 0,
                'cooldown_counter' => 0,
                'session_cap' => $sessionCap,
                'session_sent_count' => 0,
                'unregistered_count' => 0,
                'skipped_count' => $skippedCount,
                'exclude_recent' => $excludeRecent,
                'send_mode' => $validated['send_mode'],
                'default_message' => $validated['default_message'] ?? null,
                'enable_cooldown' => $enableCooldown,
                'append_optout' => $appendOptout,
                'device_id' => $deviceId,
                'logs' => [$initLogMsg],
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
            $deviceId,
            $sessionCap
        );

        $flashMsg = __('messages.whatsapp_bulk_dispatched_successfully');
        if ($skippedCount > 0) {
            $flashMsg .= " (" . __('messages.whatsapp_excluded_already_sent_notice', ['count' => $skippedCount]) . ")";
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'broadcast_id' => $log->id,
                'total' => count($recipients),
                'skipped' => $skippedCount,
                'message' => $flashMsg,
            ]);
        }

        return redirect()->route('whatsapp.subscribers.index')
            ->with('success', $flashMsg)
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
            'title' => $broadcast->title ?: ('Bulk Campaign #' . $broadcast->id),
            'status' => $broadcast->status,
            'total' => $broadcast->total_recipients,
            'successful' => $broadcast->successful_count,
            'failed' => $broadcast->failed_count,
            'skipped' => $metadata['skipped_count'] ?? 0,
            'percent' => $percent,
            'logs' => $metadata['logs'] ?? [],
            'is_paused' => $broadcast->status === 'paused',
            'is_finished' => in_array($broadcast->status, ['completed', 'failed', 'cancelled']),
        ]);
    }

    /**
     * Cancel an ongoing bulk broadcast.
     */
    public function cancelBroadcast(WhatsAppBroadcastLog $broadcast): JsonResponse|RedirectResponse
    {
        if (in_array($broadcast->status, ['processing', 'paused'])) {
            $broadcast->update(['status' => 'cancelled']);
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'status' => 'cancelled']);
        }

        return back()->with('success', __('messages.whatsapp_broadcast_cancelled'));
    }

    /**
     * Resume a paused bulk broadcast.
     */
    public function resumeBroadcast(WhatsAppBroadcastLog $broadcast, WhatsAppClient $client): JsonResponse|RedirectResponse
    {
        if ($broadcast->status !== 'paused') {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => __('messages.whatsapp_broadcast_not_paused'),
                ], 422);
            }
            return back()->with('error', __('messages.whatsapp_broadcast_not_paused'));
        }

        $deviceId = $broadcast->device_id ?: config('whatsapp.device_id', 'default');
        $status = $client->getDeviceStatus($deviceId);

        // If the original device is disconnected, automatically fallback to any currently logged-in device
        if (!($status['connected'] ?? false)) {
            $devicesList = $client->listDevices();
            $connectedFallback = null;
            foreach ($devicesList['devices'] ?? [] as $dev) {
                if (($dev['state'] ?? '') === 'logged_in') {
                    $connectedFallback = $dev['id'];
                    break;
                }
            }

            if ($connectedFallback) {
                $deviceId = $connectedFallback;
                $broadcast->update(['device_id' => $deviceId]);
                $status = ['connected' => true];
            } else {
                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => __('messages.whatsapp_cannot_resume_device_disconnected'),
                    ], 422);
                }
                return back()->with('error', __('messages.whatsapp_cannot_resume_device_disconnected'));
            }
        }

        $meta = $broadcast->metadata ?? [];
        $meta['session_sent_count'] = 0;
        $meta['device_id'] = $deviceId;
        $sessionCap = (int) ($meta['session_cap'] ?? 35);
        $timeStr = Carbon::now()->format('H:i:s');
        $meta['logs'] = $meta['logs'] ?? [];
        $meta['logs'][] = "[{$timeStr}] ▶️ Campaign resumed by administrator using device '{$deviceId}'. Starting fresh safety batch (up to {$sessionCap} messages).";
        $meta['logs'] = array_slice($meta['logs'], -200);

        $broadcast->update([
            'status' => 'processing',
            'device_id' => $deviceId,
            'metadata' => $meta,
        ]);

        SendWhatsAppBulkCsvBroadcast::dispatch($broadcast->id);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'status' => 'processing', 'device_id' => $deviceId]);
        }

        return back()->with('success', __('messages.whatsapp_broadcast_resumed'));
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

    /**
     * Upload / Update the Convention Attendees CSV dataset for on-demand WhatsApp Bot retrieval (Option 9).
     * This creates or updates a convention_attendees broadcast record without triggering outbound blast jobs.
     */
    public function uploadConventionAttendeesCsv(Request $request, WhatsAppClient $client): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'csv_file' => 'required|file|max:10240',
            'title' => 'nullable|string|max:255',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            return back()->with('error', __('messages.whatsapp_csv_file_read_error'));
        }

        // BOM removal
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->with('error', __('messages.whatsapp_csv_empty_or_invalid'));
        }

        // Detect column indices
        $headerMap = [];
        foreach ($header as $idx => $col) {
            $clean = mb_strtolower(trim((string) $col));
            $clean = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $clean);

            if (in_array($clean, ['phone', 'mobile', 'telephone', 'هاتف', 'الهاتف', 'موبايل', 'رقم الهاتف', 'رقم الموبايل', 'الجوال', 'رقم'])) {
                $headerMap['phone'] = $idx;
            } elseif (in_array($clean, ['name', 'الاسم', 'اسم', 'contact_name', 'full_name'])) {
                $headerMap['name'] = $idx;
            } elseif (in_array($clean, ['message', 'الرسالة', 'نص الرسالة', 'msg', 'text', 'content', 'دعوة', 'الدعوة'])) {
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

            // Deduplication within the CSV
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

        $title = !empty($validated['title']) ? $validated['title'] : 'كشف حضور مؤتمر مسار يجمعنا (' . count($recipients) . ' مشارك)';

        // Find existing convention_attendees log or create new one
        $log = WhatsAppBroadcastLog::where('channel', 'convention_attendees')->latest()->first();

        if ($log) {
            $meta = $log->metadata ?? [];
            $meta['recipients'] = $recipients;
            $meta['uploaded_at'] = Carbon::now()->toIso8601String();
            $meta['file_name'] = $file->getClientOriginalName();
            $log->update([
                'title' => $title,
                'total_recipients' => count($recipients),
                'status' => 'completed',
                'metadata' => $meta,
            ]);
        } else {
            $log = WhatsAppBroadcastLog::create([
                'channel' => 'convention_attendees',
                'device_id' => config('whatsapp.device_id', 'default'),
                'title' => $title,
                'total_recipients' => count($recipients),
                'successful_count' => 0,
                'failed_count' => 0,
                'status' => 'completed',
                'anti_ban_profile' => 'safe',
                'is_dev_broadcast' => (bool) config('whatsapp.dev_mode', false),
                'dispatched_by' => auth()->id(),
                'metadata' => [
                    'recipients' => $recipients,
                    'uploaded_at' => Carbon::now()->toIso8601String(),
                    'file_name' => $file->getClientOriginalName(),
                    'claimed_invitations' => [],
                ],
            ]);
        }

        $msg = __('messages.whatsapp_convention_attendees_uploaded_success', ['count' => count($recipients)]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'total' => count($recipients),
                'log_id' => $log->id,
                'message' => $msg,
            ]);
        }

        return redirect()->route('whatsapp.subscribers.index')->with('success', $msg);
    }
}
