<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Stats Logic
        $totalDonations = $user->donations()->where('status', 'completed')->count();
        
        // Calculate detailed eligibility
        $eligibility = $this->calculateNextEligible($user);
        
        $stats = [
            'total'         => $totalDonations,
            'blood_type'    => $user->blood_type ?? '??',
            'lives_saved'   => $totalDonations * 3,
            'next_eligible' => $eligibility['date_string'], 
            'can_donate'    => $eligibility['is_eligible'],
            'days_left'     => $eligibility['days_left']
        ];

        // 2. Upcoming Appointments
        $upcomingAppointments = $user->appointments()
            ->with('location')
            ->whereIn('status', ['scheduled', 'pending', 'approved'])
            ->orderBy('appointment_date', 'asc')
            ->get();

        // 3. Recent Donations
        $recentDonations = $user->donations()
            ->with('location')
            ->latest()
            ->take(3)
            ->get();

        // 4. Progress Calculation
        $goal = 15; 
        $progress = ($totalDonations > 0) ? ($totalDonations / $goal) * 100 : 0;

        return view('dashboard', compact(
            'user', 
            'stats', 
            'upcomingAppointments', 
            'recentDonations', 
            'goal', 
            'progress'
        ));
    }

    private function calculateNextEligible($user)
    {
        $lastDonation = $user->donations()
            ->where('status', 'completed')
            ->latest()
            ->first();

        if (!$lastDonation) {
            return [
                'date_string' => 'NOW', 
                'is_eligible' => true, 
                'days_left'   => 0
            ];
        }

        // Updated to 56 days
        $nextDate = Carbon::parse($lastDonation->created_at)->addDays(56);
        $isPast = $nextDate->isPast();

        return [
            'date_string' => $isPast ? 'NOW' : $nextDate->format('M d, Y'),
            'is_eligible' => $isPast,
            'days_left'   => $isPast ? 0 : ceil(now()->diffInDays($nextDate, false))
        ];
    }
}