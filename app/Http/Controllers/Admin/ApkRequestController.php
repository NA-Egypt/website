<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ApkDownloadLinkMail;
use App\Models\ApkDownloadRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApkRequestController extends Controller
{
    /**
     * Display a listing of APK download requests with KPI stats and filters.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', 'all');

        $totalRequests = ApkDownloadRequest::count();
        $uniqueEmails = ApkDownloadRequest::distinct('email')->count('email');
        $totalDownloads = (int) ApkDownloadRequest::sum('download_count');
        $activeLinks = ApkDownloadRequest::where('expires_at', '>', now())->count();
        $downloadRate = $totalRequests > 0 ? round(($totalDownloads / $totalRequests) * 100, 1) . '%' : '0%';

        $kpiStats = [
            'total_requests' => $totalRequests,
            'unique_emails' => $uniqueEmails,
            'total_downloads' => $totalDownloads,
            'active_links' => $activeLinks,
            'download_rate' => $downloadRate,
        ];

        $query = ApkDownloadRequest::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($status === 'downloaded') {
            $query->where('download_count', '>', 0);
        } elseif ($status === 'pending') {
            $query->where('download_count', 0)->where('expires_at', '>', now());
        } elseif ($status === 'expired') {
            $query->where('expires_at', '<=', now());
        }

        $apkRequests = $query->latest('id')->paginate(20)->withQueryString();

        return view('admin.apk_requests.index', compact('apkRequests', 'kpiStats', 'search', 'status'));
    }

    /**
     * Regenerate token, extend expiration, and resend the download link email.
     */
    public function resend(ApkDownloadRequest $apkRequest): RedirectResponse
    {
        $newToken = Str::random(64);
        $expiryHours = (int) config('services.apk.token_expiry_hours', 24);

        $apkRequest->update([
            'token' => $newToken,
            'expires_at' => now()->addHours($expiryHours),
        ]);

        try {
            Mail::to($apkRequest->email)->send(new ApkDownloadLinkMail($apkRequest));
        } catch (\Throwable $e) {
            Log::error('Failed to resend APK download email from admin: ' . $e->getMessage(), [
                'request_id' => $apkRequest->id,
                'email' => $apkRequest->email,
            ]);

            return back()->with('error', __('messages.apk_email_send_failed'));
        }

        return back()->with('success', __('messages.apk_link_resent_success'));
    }

    /**
     * Remove a single download request from storage.
     */
    public function destroy(ApkDownloadRequest $apkRequest): RedirectResponse
    {
        $apkRequest->delete();

        return back()->with('success', __('messages.apk_requests_deleted_success'));
    }

    /**
     * Bulk delete selected download requests.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:apk_download_requests,id',
        ]);

        $count = ApkDownloadRequest::whereIn('id', $validated['ids'])->delete();

        return back()->with('success', __('messages.apk_requests_deleted_success') . " ({$count})");
    }

    /**
     * Export APK download requests matching current filters as a CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', 'all');

        $query = ApkDownloadRequest::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($status === 'downloaded') {
            $query->where('download_count', '>', 0);
        } elseif ($status === 'pending') {
            $query->where('download_count', 0)->where('expires_at', '>', now());
        } elseif ($status === 'expired') {
            $query->where('expires_at', '<=', now());
        }

        $filename = 'apk_download_requests_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Insert UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'ID',
                'Email',
                'Downloads Count',
                'Status',
                'IP Address',
                'User Agent',
                'Created At',
                'Expires At',
                'Last Downloaded At',
            ]);

            $query->latest('id')->chunk(200, function ($requests) use ($handle) {
                foreach ($requests as $item) {
                    $itemStatus = 'Pending';
                    if ($item->download_count > 0) {
                        $itemStatus = 'Downloaded';
                    } elseif ($item->isExpired()) {
                        $itemStatus = 'Expired';
                    }

                    fputcsv($handle, [
                        $item->id,
                        $item->email,
                        $item->download_count,
                        $itemStatus,
                        $item->ip_address ?? '',
                        $item->user_agent ?? '',
                        $item->created_at ? $item->created_at->toDateTimeString() : '',
                        $item->expires_at ? $item->expires_at->toDateTimeString() : '',
                        $item->last_downloaded_at ? $item->last_downloaded_at->toDateTimeString() : '',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
