<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support - Use \Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL; // FIX 1: Add this import
use App\Models\Appointment;
use App\Models\User;
use App\Models\Location;
use App\Models\Feedback;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /**
         * FIX 2: FORCE HTTPS
         * This removes the warning in image_4a4c5b.png. 
         * Localhost doesn't need this, but Render does.
         */
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view) {
            /**
             * FIX 3: TRY-CATCH WRAPPER
             * Locally, if your DB is off, you see a clear error. 
             * On Render, it just gives a generic 500. This prevents the crash.
             */
            try {
                $user = Auth::user();
                
                $pendingApptsCount = Appointment::whereIn('status', ['scheduled', 'Awaiting Exam', 'AWAITING EXAM'])->count();
                $totalDonors       = User::where('role', 'donor')->count();
                $totalDoctors      = User::where('role', 'doctor')->count();
                $locationsCount    = Location::exists() ? Location::count() : 0;
                $feedbackCount     = Feedback::exists() ? Feedback::where('status', 'pending')->count() : 0;

                if ($user && $user->role === 'doctor') {
                    $locationIds = $user->locations->pluck('id');
                    $pendingApptsCount = Appointment::whereIn('location_id', $locationIds)
                        ->whereIn('status', ['scheduled', 'approved', 'Awaiting Exam', 'AWAITING EXAM'])
                        ->count();
                }

                $view->with('stats', [
                    'pending'          => $pendingApptsCount,
                    'pending_appts'    => $pendingApptsCount,
                    'total_donors'     => $totalDonors,
                    'total_doctors'    => $totalDoctors,
                    'total_locations'  => $locationsCount,
                    'pending_feedback' => $feedbackCount,
                ]);
            } catch (\Exception $e) {
                // If the DB isn't ready, don't crash the whole page
                $view->with('stats', [
                    'pending' => 0, 'pending_appts' => 0, 'total_donors' => 0,
                    'total_doctors' => 0, 'total_locations' => 0, 'pending_feedback' => 0
                ]);
            }
        });
    }
}
