<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackApiUsage
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('api_tracking_start_time', microtime(true));

        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser / client.
     */
    public function terminate(Request $request, Response $response): void
    {
        try {
            $startTime = $request->attributes->get('api_tracking_start_time') ?? (defined('LARAVEL_START') ? LARAVEL_START : microtime(true));
            $durationMs = max(1, (int) round((microtime(true) - $startTime) * 1000));

            $userAgent = (string) $request->header('User-Agent', '');
            $platformHeader = strtolower(trim((string) $request->header('X-App-Platform', '')));
            $appVersion = $request->header('X-App-Version');
            $deviceId = $request->header('X-Device-Id');

            $platform = $this->resolvePlatform($platformHeader, $userAgent);

            $userId = auth('sanctum')->id() ?? $request->user()?->id;
            $routeName = $request->route()?->getName();
            $endpoint = '/' . ltrim($request->path(), '/');

            // Limit endpoint string length to prevent DB overflow if path is abnormally long
            $endpoint = mb_substr($endpoint, 0, 500);

            ApiLog::create([
                'method' => strtoupper($request->method()),
                'endpoint' => $endpoint,
                'route_name' => $routeName ? mb_substr($routeName, 0, 255) : null,
                'status_code' => $response->getStatusCode(),
                'response_time_ms' => $durationMs,
                'platform' => $platform,
                'app_version' => $appVersion ? mb_substr($appVersion, 0, 50) : null,
                'device_id' => $deviceId ? mb_substr($deviceId, 0, 100) : null,
                'ip_address' => $request->ip(),
                'user_id' => $userId,
                'user_agent' => $userAgent ? mb_substr($userAgent, 0, 1000) : null,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Silently fail in terminating hook to ensure zero impact on API client
        }
    }

    /**
     * Resolve the client platform (ios, android, web, other).
     */
    protected function resolvePlatform(string $platformHeader, string $userAgent): string
    {
        if (in_array($platformHeader, ['ios', 'android', 'web'])) {
            return $platformHeader;
        }

        $ua = strtolower($userAgent);

        if (str_contains($ua, 'iphone') || str_contains($ua, 'ipad') || str_contains($ua, 'cfnetwork') || str_contains($ua, 'darwin') || str_contains($ua, 'naegypt-ios')) {
            return 'ios';
        }

        if (str_contains($ua, 'android') || str_contains($ua, 'okhttp') || str_contains($ua, 'dalvik') || str_contains($ua, 'naegypt-android')) {
            return 'android';
        }

        if (str_contains($ua, 'mozilla') || str_contains($ua, 'chrome') || str_contains($ua, 'safari') || str_contains($ua, 'firefox') || str_contains($ua, 'edge')) {
            return 'web';
        }

        return 'other';
    }
}
