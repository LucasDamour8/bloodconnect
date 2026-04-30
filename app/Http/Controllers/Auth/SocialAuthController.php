<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Social login failed. Please try again.']);
        }

        $nameParts = explode(' ', $socialUser->getName() ?? 'User', 2);

        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'first_name'      => $nameParts[0],
                'last_name'       => $nameParts[1] ?? '',
                'password'        => bcrypt(\Str::random(32)),
                'social_id'       => $socialUser->getId(),
                'social_provider' => $provider,
                'role'            => 'donor',
                'locale'          => session('locale', 'en'),
            ]
        );

        Auth::login($user);
        session(['locale' => $user->locale]);

        return match($user->role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'doctor' => redirect()->route('doctor.dashboard'),
            default  => redirect()->route('dashboard'),
        };
    }
}