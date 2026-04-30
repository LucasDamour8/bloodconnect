<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Location;
use App\Models\CenterCapacity;
use App\Models\Donation; // Added to match your Dashboard logic
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Ensure Carbon is used for date calculations

class AppointmentController extends Controller
{
    /**
     * Updated index to handle Doctor/Provider Center filtering
     */
    public function index(Request $request)
    {
        // 1. Get centers assigned to this provider (Doctor)
        $assignedCenters = Auth::user()->locations; 

        // 2. Start the query with relationships
        $query = Appointment::with(['location', 'user']);

        // 3. Filter by selected centers (the checkboxes you added to the view)
        if ($request->has('centers')) {
            $query->whereIn('location_id', $request->centers);
        } else {
            // Default: show all appointments for centers they are assigned to
            $query->whereIn('location_id', $assignedCenters->pluck('id'));
        }

        // 4. Handle existing filters: Search
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%");
            });
        }

        // 5. Handle existing filters: Status (Defaults to scheduled if not provided)
        $status = $request->get('status', 'scheduled');
        $query->where('status', $status);

        // 6. Finalize results
        $appointments = $query->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->get(); 

        return view('admin.Appointments.index', compact('appointments', 'assignedCenters'));
    }

    /**
     * Updated create to handle the 56-day rule based on the donations table
     */
    public function create()
    {
        // Fetch the last completed donation exactly like DashboardController
        $lastDonation = Auth::user()->donations()
            ->where('status', 'completed')
            ->latest()
            ->first();

        $daysUntilNextDonation = 0;

        if ($lastDonation) {
            // Calculate 56 days from the created_at timestamp
            $nextEligibleDate = Carbon::parse($lastDonation->created_at)->addDays(56);
            
            if ($nextEligibleDate->isFuture()) {
                // Round up the days left
                $daysUntilNextDonation = ceil(now()->diffInDays($nextEligibleDate, false));
            }
        }

        $locations = Location::where('is_active', true)->get();
        return view('admin.Appointments.book', compact('locations', 'daysUntilNextDonation'));
    }

    /**
     * API for dynamic dropdown: Returns dates with remaining slot counts
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
                    'id' => $cap->id,
                    'date' => $cap->date,
                    'remaining' => $cap->max_donors - $bookedCount,
                ];
            })
            ->filter(fn($item) => $item['remaining'] > 0)
            ->values();

        return response()->json($availabilities);
    }

    /**
     * Updated store to include the server-side safety check for 56 days
     */
    public function store(Request $request)
    {
        $request->validate([
            'location_id'   => ['required', 'exists:locations,id'],
            'capacity_id'   => ['required', 'exists:center_capacities,id'],
            'donation_type' => ['required', 'in:whole_blood,platelets,plasma'],
        ]);

        // Safety check: Ensure user isn't bypassing the front-end alert
        $lastDonation = Auth::user()->donations()
            ->where('status', 'completed')
            ->latest()
            ->first();

        if ($lastDonation) {
            $nextEligibleDate = Carbon::parse($lastDonation->created_at)->addDays(56);
            if ($nextEligibleDate->isFuture()) {
                $wait = ceil(now()->diffInDays($nextEligibleDate, false));
                return back()->withErrors(['error' => "Uretse gato! Ugomba gutegereza iminsi {$wait} kugirango wongere gutanga amaraso."])->withInput();
            }
        }

        $capacity = CenterCapacity::findOrFail($request->capacity_id);

        // Final capacity check
        $bookedCount = Appointment::where('location_id', $request->location_id)
            ->where('appointment_date', $capacity->date)
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($bookedCount >= $capacity->max_donors) {
            return back()->withErrors(['capacity_id' => 'This date just filled up!'])->withInput();
        }

        // Generate Tracking ID
        $user = Auth::user();
        $trackingId = strtoupper(substr($user->first_name, 0, 3)) . strtoupper(bin2hex(random_bytes(2)));

        Appointment::create([
            'user_id'          => $user->id,
            'location_id'      => $request->location_id,
            'tracking_id'      => $trackingId,
            'appointment_date' => $capacity->date,
            'appointment_time' => now()->format('H:i:s'), 
            'donation_type'    => $request->donation_type,
            'status'           => 'scheduled', 
        ]);

        return redirect()->route('dashboard')->with('success', "Booked! Tracking ID: {$trackingId}");
    }

    /**
     * NEW METHOD: Show the edit form (Only if scheduled)
     */
    public function edit($id)
    {
        $appointment = Appointment::where('user_id', Auth::id())->findOrFail($id);

        if ($appointment->status !== 'scheduled') {
            return redirect()->route('dashboard')->with('error', 'This appointment cannot be edited.');
        }

        $locations = Location::where('is_active', true)->get();
        return view('admin.Appointments.edit', compact('appointment', 'locations'));
    }

    /**
     * NEW METHOD: Update appointment (Only if scheduled)
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::where('user_id', Auth::id())->findOrFail($id);

        if ($appointment->status !== 'scheduled') {
            return redirect()->route('dashboard')->with('error', 'Update failed: Status has changed.');
        }

        $request->validate([
            'location_id'   => ['required', 'exists:locations,id'],
            'appointment_date' => ['required', 'date', 'after:today'],
        ]);

        $appointment->update([
            'location_id' => $request->location_id,
            'appointment_date' => $request->appointment_date,
        ]);

        return redirect()->route('dashboard')->with('success', 'Appointment updated successfully.');
    }

    /**
     * NEW METHOD: Delete appointment (Only if scheduled)
     */
    public function destroy($id)
    {
        $appointment = Appointment::where('user_id', Auth::id())->findOrFail($id);

        if ($appointment->status !== 'scheduled') {
            return redirect()->route('dashboard')->with('error', 'Cannot delete an appointment that is already processed.');
        }

        $appointment->delete();

        return redirect()->route('dashboard')->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Handles the status update from Doctors and Admins
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $appointment = Appointment::findOrFail($id);
        
        $appointment->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', "Appointment status updated to: " . ucfirst($request->status));
    }
}