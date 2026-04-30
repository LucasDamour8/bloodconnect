<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\CenterCapacity;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LocationController extends Controller
{
    /**
     * Display the learn/educational view.
     */
    public function learn()
    {
        return view('learn');
    }

    /**
     * Display a listing of locations for the home/user view.
     */
    public function index(Request $request)
    {
        $query = Location::where('is_active', true);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name','like',"%$s%")
                                      ->orWhere('address','like',"%$s%")
                                      ->orWhere('city','like',"%$s%"));
        }

        if ($request->filled('availability')) {
            $query->where('availability', $request->availability);
        }

        $locations = $query->get();
        $stats = [
            'total'      => $locations->count(),
            'walk_ins'   => $locations->where('walk_ins', true)->count(),
            'high_avail' => $locations->where('availability', 'high')->count(),
            'open_today' => $locations->whereNotIn('hours', ['Closed', 'closed'])->count(),
        ];

       return view('homelocation', compact('locations', 'stats'));
    }

    /**
     * Display a listing of locations for the admin panel.
     */
    public function adminIndex()
    {
        $locations = Location::latest()->paginate(20);
        return view('admin.locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new location.
     */
    public function create()
    {
        $doctors = User::where('role', 'doctor')->get();
        return view('admin.locations.create', compact('doctors'));
    }

    /**
     * Store a newly created location in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'address'      => 'required|string|max:255',
            'city'         => 'required|string|max:100',
            'phone'        => 'nullable|string|max:30',
            'hours'        => 'nullable|string|max:100',
            'availability' => 'required|in:high,medium,low',
            'walk_ins'     => 'nullable',
            'start_date'   => 'required|date|after_or_equal:today',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'max_donors'   => 'required|integer|min:1',
            'doctor_ids'   => 'nullable|array',
            'doctor_ids.*' => 'exists:users,id',
        ]);

        $location = Location::create([
            'name'         => $request->name,
            'address'      => $request->address,
            'city'         => $request->city,
            'phone'        => $request->phone,
            'hours'        => $request->hours ?? '08:00 AM - 05:00 PM',
            'availability' => $request->availability,
            'walk_ins'     => $request->has('walk_ins'), 
            'is_active'    => true,
            'active_from'  => $request->start_date,
            'active_until' => $request->end_date,
            'max_donors'   => $request->max_donors,
        ]);

        if ($request->filled('doctor_ids')) {
            $location->doctors()->sync($request->doctor_ids);
        }

        $this->generateCapacities($location, $request->start_date, $request->end_date, $request->max_donors);

        return redirect()->route('admin.centers.index')
                         ->with('success', 'Location and availability range added.');
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(int $id)
    {
        $location = Location::findOrFail($id);
        $doctors = User::where('role', 'doctor')->get();
        $assignedDoctors = $location->doctors->pluck('id')->toArray();
        
        return view('admin.locations.edit', compact('location', 'doctors', 'assignedDoctors'));
    }

    /**
     * Update the specified location in storage.
     */
    public function adminUpdate(Request $request, int $id)
    {
        // Detect which date keys are used in the form (handles both start_date and active_from)
        $startKey = $request->has('start_date') ? 'start_date' : 'active_from';
        $endKey = $request->has('end_date') ? 'end_date' : 'active_until';

        $request->validate([
            'name'         => 'required|string|max:255',
            'address'      => 'required|string|max:255',
            'city'         => 'required|string|max:100',
            'availability' => 'required|in:high,medium,low',
            $startKey      => 'required|date',
            $endKey        => 'required|date|after_or_equal:' . $startKey,
            'max_donors'   => 'required|integer|min:1',
            'doctor_ids'   => 'nullable|array',
            'doctor_ids.*' => 'exists:users,id',
            'hours'        => 'nullable|string|max:100',
        ]);

        $location = Location::findOrFail($id);
        
        $location->update([
            'name'         => $request->name,
            'address'      => $request->address,
            'city'         => $request->city,
            'availability' => $request->availability,
            'walk_ins'     => $request->has('walk_ins'),
            'is_active'    => $request->has('is_active'),
            'active_from'  => $request->$startKey,
            'active_until' => $request->$endKey,
            'max_donors'   => $request->max_donors,
            'hours'        => $request->hours ?? '08:00 AM - 05:00 PM',
        ]);

        // Sync doctors assigned to this location
        $location->doctors()->sync($request->doctor_ids ?? []);

        // Refresh capacity schedule based on updated dates and donor limits
        CenterCapacity::where('location_id', $location->id)->delete();
        $this->generateCapacities($location, $location->active_from, $location->active_until, $location->max_donors);

        return redirect()->route('admin.centers.index')->with('success', 'Location and schedule updated successfully.');
    }

    /**
     * Remove the specified location from storage.
     */
    public function destroy(int $id)
    {
        $location = Location::findOrFail($id);
        CenterCapacity::where('location_id', $location->id)->delete();
        $location->doctors()->detach(); 
        $location->delete();
        
        return back()->with('success', 'Location deleted.');
    }

    /**
     * Helper function to generate donor capacities for a date range.
     */
    private function generateCapacities($location, $startDate, $endDate, $maxDonors)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($start <= $end) {
            CenterCapacity::create([
                'location_id' => $location->id,
                'date'         => $start->format('Y-m-d'),
                'max_donors'  => $maxDonors,
            ]);
            $start->addDay();
        }
    }
}