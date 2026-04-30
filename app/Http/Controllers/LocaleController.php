<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function switch(string $locale)
    {
        $allowed = ['en', 'rw'];
        if (!in_array($locale, $allowed)) {
            $locale = 'en';
        }

        Session::put('locale', $locale);

        // Also persist to database if user is logged in
        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }

        return redirect()->back()->withHeaders([
            // Force browser not to cache the redirected page
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}