<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     * This is the method your web.php is currently calling.
     */
    public function showLoginForm()
    {
        return view('auth.login'); 
    }

    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Set locale
            session(['locale' => Auth::user()->locale ?? 'en']);

            return match(Auth::user()->role) {
                'admin'  => redirect()->route('admin.dashboard'),
                'doctor' => redirect()->route('doctor.dashboard'),
                default  => redirect()->route('dashboard'),
            };
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->withInput();
    }

    /**
     * Handle Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}