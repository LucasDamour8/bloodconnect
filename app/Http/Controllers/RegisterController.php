<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle the registration form submission.
     * NO RegistersUsers trait — fully custom, no 'name' field anywhere.
     */
    public function register(Request $request)
    {
        // ── Validate ────────────────────────────────────────────────────────
        $request->validate([
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],
            'email'         => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'national_id'   => ['required', 'string', 'size:16', 'unique:users,national_id'],
            'role'          => ['required', 'in:donor,doctor'],
            'district'      => ['required', 'string', 'max:100'],
            'sector'        => ['required', 'string', 'max:100'],
            'gender'        => ['required', 'in:male,female'],
            'date_of_birth' => ['required', 'date', 'before:-16 years'],
            'blood_type'    => ['required_if:role,donor', 'nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'terms'         => ['required', 'accepted'],
        ], [
            // Custom messages so errors are crystal clear
            'national_id.size'          => 'National ID must be exactly 16 digits.',
            'national_id.unique'        => 'This National ID is already registered.',
            'blood_type.required_if'    => 'Blood type is required for donors.',
            'date_of_birth.before'      => 'You must be at least 16 years old.',
            'terms.accepted'            => 'You must accept the terms and conditions.',
            'password.confirmed'        => 'Passwords do not match.',
        ]);

        // ── Handle photo upload ─────────────────────────────────────────────
        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        // ── Doctors start inactive (admin must activate) ────────────────────
        $isActive = ($request->role === 'donor');

        // ── Create user ─────────────────────────────────────────────────────
        $user = User::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'national_id'   => $request->national_id,
            'role'          => $request->role,
            'district'      => $request->district,
            'sector'        => $request->sector,
            'gender'        => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'blood_type'    => ($request->role === 'donor') ? $request->blood_type : null,
            'profile_photo' => $photoPath,
            'is_active'     => $isActive,
            'locale'        => session('locale', 'en'),
            'password'      => Hash::make($request->password),
        ]);

        // ── Log in immediately ──────────────────────────────────────────────
        Auth::login($user);
        session(['locale' => $user->locale]);

        session()->flash('success', "Welcome to BloodConnect, {$user->first_name}! Your account has been created.");

        // ── Redirect by role ────────────────────────────────────────────────
        return match($user->role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'doctor' => redirect()->route('doctor.dashboard'),
            default  => redirect()->route('dashboard'),
        };
    }
}