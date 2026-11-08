<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CommitteeReportsGuideController extends Controller
{
    /**
     * Get the external public link for the Committee Reports Guide.
     */
    public static function getPublicUrl(): string
    {
        $token = (string) config('services.reports_guide.token', 'naegypt-reports-guide-secret-key-2025');
        return route('reports.guide.public', ['token' => $token]);
    }

    /**
     * Display the Committee Reports and Workgroups Guide (internal authenticated route).
     */
    public function index(Request $request)
    {
        // If not logged in, delegate to public token validation
        if (!auth()->check()) {
            return $this->publicIndex($request);
        }

        return $this->renderGuideResponse($request);
    }

    /**
     * Display the Committee Reports and Workgroups Guide (external public route).
     */
    public function publicIndex(Request $request)
    {
        // 1. Authenticated users always have access
        if (auth()->check()) {
            return $this->renderGuideResponse($request);
        }

        // 2. Validate token for unauthenticated visitors
        $token = $request->query('token');
        $expectedToken = (string) config('services.reports_guide.token', 'naegypt-reports-guide-secret-key-2025');

        if (empty($token) || !hash_equals($expectedToken, (string) $token)) {
            return redirect()->route('login.azure')->with('error', __('messages.reports_guide_invalid_token'));
        }

        return $this->renderGuideResponse($request);
    }

    /**
     * Render the guide view with security & anti-indexing headers.
     */
    protected function renderGuideResponse(Request $request): Response
    {
        $publicUrl = self::getPublicUrl();

        return response()
            ->view('guides.reports-workgroups', [
                'publicUrl' => $publicUrl,
            ])
            ->header('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet, noimageindex');
    }
}
