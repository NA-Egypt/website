<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('Permissions-Policy', 'unload=*');

        if ($request->is(
            'guidelines*',
            '*/guidelines*',
            'store*',
            '*/store*',
            'lit',
            'lit/*',
            '*/lit',
            '*/lit/*',
            'inventory*',
            '*/inventory*',
            'slips*',
            '*/slips*',
            'literature-requests*',
            '*/literature-requests*',
            'committee-literature*',
            '*/committee-literature*'
        )) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet, noimageindex');
        }

        return $response;
    }
}
