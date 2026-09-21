<?php

namespace App\Http\Controllers;

use App\Mail\ApkDownloadLinkMail;
use App\Models\ApkDownloadRequest;
use App\Rules\Turnstile;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApkDownloadController extends Controller
{
    /**
     * Handle submission of email to request a secret pre-release APK download link.
     */
    public function requestLink(Request $request): JsonResponse
    {
        $turnstileSecret = config('services.turnstile.secret_key');

        $rules = [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
        ];

        if (!empty($turnstileSecret) && (!app()->environment('testing') || !empty($request->header('X-Test-Turnstile-Verification')))) {
            $rules['cf-turnstile-response'] = ['required', new Turnstile];
        }

        $messages = [
            'email.required' => __('messages.apk_email_required'),
            'email.email' => __('messages.apk_email_invalid'),
            'cf-turnstile-response.required' => __('messages.turnstile_required'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $email = strtolower(trim($validated['email']));
        $token = Str::random(64);
        $expiryHours = (int) config('services.apk.token_expiry_hours', 24);

        $apkRequest = ApkDownloadRequest::create([
            'email' => $email,
            'token' => $token,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'expires_at' => now()->addHours($expiryHours),
        ]);

        try {
            Mail::to($apkRequest->email)->send(new ApkDownloadLinkMail($apkRequest));
        } catch (\Throwable $e) {
            Log::error('Failed to send APK download email: ' . $e->getMessage(), [
                'email' => $email,
                'token' => $token,
            ]);

            return response()->json([
                'success' => false,
                'message' => __('messages.apk_email_send_failed'),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.apk_link_sent_success'),
        ], 200);
    }

    /**
     * Securely stream the APK binary directly from the hidden upstream release without exposing the URL.
     */
    public function download(Request $request, string $token): StreamedResponse|\Illuminate\Http\Response
    {
        $apkRequest = ApkDownloadRequest::where('token', $token)->first();

        if (!$apkRequest || $apkRequest->isExpired()) {
            abort(403, __('messages.apk_expired_link'));
        }

        $releaseUrl = config('services.apk.release_url');
        $filename = config('services.apk.filename', 'na-egypt-1.2.0.apk');

        if (empty($releaseUrl)) {
            Log::critical('APK release URL is not configured in services.apk.release_url');
            abort(500, 'APK distribution is temporarily unavailable.');
        }

        // Record download metric
        $apkRequest->recordDownload();

        try {
            $upstreamResponse = \Illuminate\Support\Facades\Http::withHeaders([
                'User-Agent' => 'NA-Egypt-Portal-Proxy/1.0',
                'Accept' => 'application/octet-stream',
            ])->withOptions([
                'timeout' => 120,
                'stream' => true,
                'allow_redirects' => true,
            ])->get($releaseUrl);

            if (!$upstreamResponse->successful()) {
                throw new \Exception('Upstream HTTP error: ' . $upstreamResponse->status());
            }

            $psrResponse = $upstreamResponse->toPsrResponse();
            $body = $psrResponse->getBody();
            $contentLength = $psrResponse->getHeaderLine('Content-Length');

            $headers = [
                'Content-Type' => 'application/vnd.android.package-archive',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            if (!empty($contentLength)) {
                $headers['Content-Length'] = $contentLength;
            }

            return response()->stream(function () use ($body) {
                while (!$body->eof()) {
                    echo $body->read(1024 * 64);
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }
            }, 200, $headers);
        } catch (\Throwable $e) {
            Log::error('Error proxying APK binary stream: ' . $e->getMessage(), [
                'token' => $token,
                'release_url' => $releaseUrl,
            ]);

            abort(502, 'Unable to stream APK package. Please try again shortly.');
        }
    }
}
