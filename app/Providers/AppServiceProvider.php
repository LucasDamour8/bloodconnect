<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
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
        // Force HTTPS in production to fix the "Not Secure" warning
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view) {
            try {
                $user = Auth::user();
                
                // Get global counts for the sidebar badges
                $pendingApptsCount = Appointment::whereIn('status', ['scheduled', 'Awaiting Exam', 'AWAITING EXAM'])->count();
                $totalDonors       = User::where('role', 'donor')->count();
                $totalDoctors      = User::where('role', 'doctor')->count();
                $locationsCount    = Location::exists() ? Location::count() : 0;
                $feedbackCount     = Feedback::exists() ? Feedback::where('status', 'pending')->count() : 0;

                // Apply Doctor-specific location filters if logged in
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
                // If DB fails, send default values so the page still loads (prevents 500 errors)
                $view->with('stats', [
                    'pending' => 0, 'pending_appts' => 0, 'total_donors' => 0,
                    'total_doctors' => 0, 'total_locations' => 0, 'pending_feedback' => 0
                ]);
            }
        });
    }
}
