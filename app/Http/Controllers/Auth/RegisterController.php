<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle account registration.
     */
    public function register(Request $request)
    {
        // 1. Validation
        $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'required|email|unique:users',
            'phone'         => 'nullable|string|max:30',
            'role'          => 'required|in:donor,doctor',
            'national_id'   => 'required|digits:16|unique:users', 
            'district'      => 'required|string|max:100',
            'sector'        => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:-17 years',
            'gender'        => 'required|in:male,female,other',
            // Blood type is only required if the user chooses 'donor'
            'blood_type'    => 'nullable|required_if:role,donor|string|max:5',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password'      => 'required|min:8|confirmed',
            'terms'         => 'required|accepted',
        ]);

        // 2. Handle Profile Photo Upload
        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        // 3. Logic: Donors are active immediately, Doctors stay inactive (0)
        $isActive = ($request->role === 'donor');

        // 4. Create User
        $user = User::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'role'          => $request->role,
            'national_id'   => $request->national_id,
            'district'      => $request->district,
            'sector'        => $request->sector,
            'date_of_birth' => $request->date_of_birth,
            'gender'        => $request->gender,
            'blood_type'    => ($request->role === 'donor') ? $request->blood_type : null,
            'profile_photo' => $photoPath,
            'is_active'     => $isActive, 
            'password'      => Hash::make($request->password),
            'locale'        => session('locale', 'en'),
        ]);

        // 5. Trigger Registered Event (Email Verification)
        event(new Registered($user));

        // 6. Post-Registration Redirect Logic
        if ($request->role === 'doctor') {
            // Doctors are redirected to login with a success alert
            return redirect()->route('login')->with('success', 'Registration successful! Your doctor account is pending Admin approval. Please check your email for verification.');
        }

        // Donors are logged in automatically and sent to the dashboard
        Auth::login($user);
        
        return redirect()->route('dashboard')->with('success', 'Welcome to BloodConnect! Your donor account has been created successfully.');
    }
}