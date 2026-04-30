<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Priority: session > authenticated user preference > app default
        $locale = Session::get('locale');

        if (!$locale && Auth::check()) {
            $locale = Auth::user()->locale ?? 'en';
            Session::put('locale', $locale);
        }

        $locale = $locale ?? config('app.locale', 'en');

        // Only allow supported locales
        if (!in_array($locale, ['en', 'rw'])) {
            $locale = 'en';
        }

        App::setLocale($locale);

        return $next($request);
    }
}