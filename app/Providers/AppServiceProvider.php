<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Location;
use App\Models\Feedback;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Share $stats variable with layouts.app globally.
         * This logic handles role-based filtering for the sidebar badges.
         */
        View::composer('layouts.app', function ($view) {
            // 1. Get the authenticated user
            $user = Auth::user();
            
            // 2. Set default global values (used for Admins)
            // Added more status variations to ensure the count isn't zero due to casing
            $pendingApptsCount = Appointment::whereIn('status', ['scheduled', 'Awaiting Exam', 'AWAITING EXAM'])->count();
            $totalDonors       = User::where('role', 'donor')->count();
            $totalDoctors      = User::where('role', 'doctor')->count();
            $locationsCount    = Location::exists() ? Location::count() : 0;
            $feedbackCount     = Feedback::exists() ? Feedback::where('status', 'pending')->count() : 0;

            // 3. Apply Doctor-Specific Filters
            if ($user && $user->role === 'doctor') {
                // Get IDs of locations assigned to this specific doctor
                $locationIds = $user->locations->pluck('id');

                // Update the appointment count to only show those at the doctor's locations
                // We include various status strings to match your database exactly
                $pendingApptsCount = Appointment::whereIn('location_id', $locationIds)
                    ->whereIn('status', ['scheduled', 'approved', 'Awaiting Exam', 'AWAITING EXAM'])
                    ->count();
            }

            // 4. Send the data to the view
            $view->with('stats', [
                'pending'          => $pendingApptsCount, // CRITICAL: This matches your sidebar {{ $stats['pending'] }}
                'pending_appts'    => $pendingApptsCount, // Kept for consistency
                'total_donors'     => $totalDonors,
                'total_doctors'    => $totalDoctors,
                'total_locations'  => $locationsCount,
                'pending_feedback' => $feedbackCount,
            ]);
        });
    }
}