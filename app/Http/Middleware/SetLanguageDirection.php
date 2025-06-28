<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


class SetLanguageDirection
{
    public function handle(Request $request, Closure $next)
    {
        // Get the current locale
        $locale = LaravelLocalization::getCurrentLocale() ?? config('app.locale');

        // Get the direction for the current locale (either 'ltr' or 'rtl')
        $direction = config("laravellocalization.supportedLocales.$locale.direction", 'ltr');
        
        // Share the direction with all views
        view()->share('direction', $direction);

        return $next($request);
    }
}
