<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Appointment;
use App\Models\CenterCapacity;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DonorLocationController extends Controller
{
    /**
     * Display locations with search and footer stats.
     */
    public function index(Request $request)
    {
        $query = Location::where('is_active', true);

        // Functional Search Logic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $locations = $query->get();

        // Stats calculation for the footer
        $stats = [
            'total' => $locations->count(),
            'walk_ins' => $locations->where('type', 'center')->count(),
            'open_today' => $locations->count(),
        ];

        return view('locations.index', compact('locations', 'stats'));
    }

    /**
     * API for the Modal: Fetches dates and checks for empty slots.
     */
    public function getAvailableDates($location_id)
    {
        $availabilities = CenterCapacity::where('location_id', $location_id)
            ->where('date', '>=', now()->toDateString())
            ->get()
            ->map(function($cap) {
                $bookedCount = Appointment::where('location_id', $cap->location_id)
                    ->where('appointment_date', $cap->date)
                    ->where('status', '!=', 'cancelled')
                    ->count();

                return [
                    'value' => $cap->date,
                    // translatedFormat works with the app locale (en or rw)
                    'label' => Carbon::parse($cap->date)->translatedFormat('l, M d, Y'),
                    'remaining' => $cap->max_donors - $bookedCount,
                ];
            })
            ->filter(fn($item) => $item['remaining'] > 0)
            ->values();

        return response()->json($availabilities);
    }

    /**
     * Store appointment with safety checks.
     */
    public function store(Request $request)
    {
        $request->validate([
            'location_id'      => ['required', 'exists:locations,id'],
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['required'],
        ]);

        $lastDonation = Auth::user()->donations()->where('status', 'completed')->latest()->first();

        if ($lastDonation) {
            $nextEligibleDate = Carbon::parse($lastDonation->created_at)->addDays(56);
            if ($nextEligibleDate->isFuture()) {
                $wait = ceil(now()->diffInDays($nextEligibleDate, false));
                
                // UPDATED: Added 'appt.' prefix for PHP file translations
                return back()->with('error', __("appt.wait_msg", ['days' => $wait]));
            }
        }

        $user = Auth::user();
        $trackingId = strtoupper(substr($user->first_name, 0, 3)) . strtoupper(bin2hex(random_bytes(2)));

        Appointment::create([
            'user_id'          => $user->id,
            'location_id'      => $request->location_id,
            'tracking_id'      => $trackingId,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'donation_type'    => 'whole_blood',
            'status'           => 'scheduled', 
        ]);

        // UPDATED: Added 'appt.' prefix for PHP file translations
        return redirect()->route('dashboard')->with('success', __("appt.success_msg", ['id' => $trackingId]));
    }
}