<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Donation;
use App\Models\User;
use App\Models\Achievement;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DoctorDashboardController extends Controller
{
    public function index()
    {
        $doctor = Auth::user();

        // FIX: Get locations from the pivot table relationship
        $assignedLocations = $doctor->locations; 
        $locationIds = $assignedLocations->pluck('id');

        // 2. Filter Appointments ONLY for those locations
        $pendingAppointments = Appointment::with(['user', 'location'])
            ->whereIn('location_id', $locationIds)
            ->whereIn('status', ['scheduled', 'approved'])
            ->orderBy('appointment_date')
            ->take(10)
            ->get();

        // 3. Data Integrations for Announcements and Feedback
        $notifications = DB::table('announcements')->latest()->take(5)->get();
        $feedbacks = DB::table('userfeedbacks')->latest()->take(5)->get();

        // 4. Filtered Stats - Only counts records from doctor's assigned locations
        $stats = [
            'pending' => Appointment::whereIn('location_id', $locationIds)
                            ->where('status', 'scheduled')
                            ->count(),
            'approved_today' => Appointment::whereIn('location_id', $locationIds)
                                ->where('status', 'approved')
                                ->whereDate('updated_at', today())
                                ->count(),
            'deferred_today' => Appointment::whereIn('location_id', $locationIds)
                                ->where('status', 'cancelled')
                                ->whereDate('updated_at', today())
                                ->count(),
            'monthly_total' => Donation::whereIn('location_id', $locationIds)
                                ->where('status', 'completed')
                                ->whereMonth('created_at', now()->month)
                                ->count(),
        ];

        // 5. Filtered Recent Donations
        $recentDonations = Donation::with(['user', 'location'])
            ->whereIn('location_id', $locationIds)
            ->latest()
            ->take(8)
            ->get();

        return view('doctors.dashboard', compact(
            'pendingAppointments', 
            'stats', 
            'recentDonations', 
            'notifications', 
            'feedbacks', 
            'assignedLocations'
        ));
    }

    public function feedback()
    {
        $feedbacks = DB::table('userfeedbacks')->latest()->paginate(15);
        return view('doctors.feedback', compact('feedbacks'));
    }

    public function reports()
    {
        $doctor = Auth::user();
        $assignedLocations = $doctor->locations; 
        $locationIds = $assignedLocations->pluck('id');

        $stats = [
            'total_exams' => Donation::where('doctor_id', $doctor->id)
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count(),
            'approved' => Donation::whereIn('location_id', $locationIds)
                            ->where('status', 'completed')
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count(),
        ];

        $chartData = Donation::whereIn('location_id', $locationIds)
            ->where('status', 'completed')
            ->select(
                DB::raw('count(*) as count'),
                DB::raw("DATE_FORMAT(created_at, '%b') as month"),
                DB::raw('MONTH(created_at) as month_num')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month', 'month_num')
            ->orderBy('month_num')
            ->get();

        $labels = $chartData->pluck('month');
        $values = $chartData->pluck('count');

        return view('doctors.reports', compact('stats', 'labels', 'values', 'assignedLocations'));
    }

    public function exportExcel()
    {
        $doctor = Auth::user();
        $locationIds = $doctor->locations->pluck('id');

        $centersData = Location::whereIn('id', $locationIds)
            ->with(['doctors', 'donations'])
            ->get()
            ->map(function($location) {
                return [
                    'Center Name' => $location->name,
                    'City' => $location->city,
                    'Working Doctors' => $location->doctors->map(fn($d) => $d->first_name . ' ' . $d->last_name)->implode(', '),
                    'Total Donations' => $location->donations->count(),
                    'Completed' => $location->donations->where('status', 'completed')->count(),
                    'Cancelled' => $location->donations->where('status', 'cancelled')->count(),
                    'Other/Pending' => $location->donations->whereNotIn('status', ['completed', 'cancelled'])->count(),
                ];
            });
        
        return back()->with('info', 'Excel export logic triggered. Ensure Laravel-Excel is configured.');
    }

    public function appointments(Request $request)
    {
        $doctor = Auth::user();
        $assignedLocations = $doctor->locations; 
        $allAssignedIds = $assignedLocations->pluck('id')->toArray();

        $query = Appointment::with(['user', 'location']);

        if ($request->has('centers') && is_array($request->centers)) {
            $query->whereIn('location_id', $request->centers);
        } else {
            $query->whereIn('location_id', $allAssignedIds);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')->paginate(20);

        return view('doctors.appointments', compact('appointments', 'assignedLocations'));
    }

    public function users(Request $request)
    {
        $query = User::whereIn('role', ['donor', 'user']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('blood_type', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(8);
        return view('doctors.users', compact('users'));
    }

    public function createUser()
    {
        return view('doctors.users_create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users',
            'national_id' => 'required|string|digits:16|unique:users', 
            'phone'       => 'required|string|digits:10', 
            'dob'         => 'required|date|before:today', 
            'gender'      => 'required|in:male,female',
            'district'    => 'nullable|string|max:100',
            'blood_type'  => 'nullable|string|max:5',
        ]);

        try {
            User::create([
                'first_name'        => $validated['first_name'],
                'last_name'         => $validated['last_name'],
                'email'             => $validated['email'],
                'national_id'       => $validated['national_id'],
                'phone'             => $validated['phone'],
                'dob'               => $validated['dob'],
                'gender'            => $validated['gender'],
                'district'          => $validated['district'],
                'blood_type'        => $validated['blood_type'],
                'role'              => 'donor',
                'status'            => '1', // FIX: Forces user to be Active instead of Suspended
                'password'          => Hash::make('pass@123'), 
                'email_verified_at' => now(),
            ]);

            // FIX: Updated route name to 'doctor.users.index' to match web.php
            return redirect()->route('doctor.users.index')
                ->with('success', 'Donor successfully registered! Default password is pass@123.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to register donor: ' . $e->getMessage());
        }
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,'.$id,
            'phone'      => 'required|string|digits:10',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['first_name', 'last_name', 'email', 'blood_type', 'district', 'phone']);

        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path) {
                Storage::delete('public/' . $user->profile_photo_path);
            }
            $data['profile_photo_path'] = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->update($data);
        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function examine($id)
    {
        $appointment = Appointment::with('user', 'location')->findOrFail($id);
        if ($appointment->status === 'completed') {
            return redirect()->route('doctor.appointments')->with('error', 'This appointment is already completed.');
        }
        return view('doctors.examine', compact('appointment'));
    }

    public function storeDonation(Request $request, $id)
    {
        $validated = $request->validate([
            'age' => 'required|integer|between:18,65',
            'weight' => 'required|numeric|min:50|max:300',
            'blood_pressure' => 'required|string|max:20',
            'pulse_rate' => 'required|integer|between:40,200',
            'temperature' => 'required|numeric|between:30,45',
            'hemoglobin' => 'required|numeric|between:5,25',
            'blood_group' => 'required|string',
            'rhesus_factor' => 'required|string',
            'hiv_test' => 'required|string|in:negative,positive',
            'hep_b' => 'required|string',
            'hep_c' => 'required|string',
            'syphilis' => 'required|string',
            'conclusion' => 'required|string|max:1000',
            'action' => 'required|in:approved,rejected'
        ]);

        try {
            $appt = Appointment::findOrFail($id);
            $doctor = Auth::user(); 
            $bloodType = $request->blood_group . $request->rhesus_factor;

            $isEligible = ($request->hiv_test === 'negative' && $request->action === 'approved');
            $finalStatus = $isEligible ? 'pending_collection' : 'cancelled';
            $apptStatus = $isEligible ? 'approved' : 'cancelled';

            Donation::create([
                'appointment_id'        => $appt->id,
                'user_id'               => $appt->user_id,
                'location_id'           => $appt->location_id,
                'doctor_id'             => $doctor->id,
                'approved_by_firstname' => $doctor->first_name, 
                'approved_by_lastname'  => $doctor->last_name,  
                'blood_type'            => $bloodType, 
                'age'                   => $request->age,
                'weight'                => $request->weight,
                'blood_pressure'        => $request->blood_pressure,
                'pulse_rate'            => $request->pulse_rate,
                'temperature'           => $request->temperature,
                'hemoglobin'            => $request->hemoglobin,
                'hiv_test'              => $request->hiv_test,
                'hep_b'                 => $request->hep_b,
                'hep_c'                 => $request->hep_c,
                'syphilis'              => $request->syphilis,
                'conclusion'            => $request->conclusion,
                'donation_date'         => now(),
                'status'                => $finalStatus
            ]);

            $appt->update(['status' => $apptStatus]);
            return redirect()->route('doctor.appointments')->with('success', 'Medical exam saved!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Database Error: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:approved,cancelled,completed',
            'reason' => 'required_if:status,cancelled|string|max:500'
        ]);

        $appt = Appointment::findOrFail($id);

        if ($request->status === 'cancelled') {
            $appt->update(['status' => 'cancelled', 'notes' => 'Deferred: ' . $request->reason]);
            return back()->with('success', 'Appointment deferred successfully.');
        }

        if ($request->status === 'completed') {
            $donation = Donation::where('appointment_id', $appt->id)->first();
            if (!$donation) return back()->with('error', 'Medical exam missing.');
            $appt->update(['status' => 'completed']);
            $donation->update(['status' => 'completed']);
            $this->checkAchievements($appt->user_id);
            return back()->with('success', 'Donation Finalized!');
        }
        return back()->with('error', 'Invalid action.');
    }

    public function viewResults($id)
    {
        $donation = Donation::with(['user', 'location', 'appointment'])->where('appointment_id', $id)->first();
        if (!$donation) return back()->with('error', 'Results not found.');
        return view('doctors.results_view', compact('donation'));
    }

    public function downloadResults($id)
    {
        $donation = Donation::where('appointment_id', $id)->first();
        if (!$donation) return back()->with('error', 'No record found.');
        return redirect()->route('doctor.appointments.viewResults', $id);
    }

    private function checkAchievements(int $userId): void
    {
        $user = User::find($userId);
        if (!$user) return;
        $count = Donation::where('user_id', $userId)->where('status', 'completed')->count(); 
        $achievements = Achievement::where('required_donations', '<=', $count)->get();
        foreach ($achievements as $a) {
            $user->achievements()->syncWithoutDetaching([$a->id => ['unlocked_at' => now()]]);
        }
    }

    public function donations()
    {
        $doctor = Auth::user();
        $locationIds = $doctor->locations->pluck('id');
        $donations = Donation::with(['user', 'location'])->whereIn('location_id', $locationIds)->where('status', 'completed')->latest()->paginate(20);
        return view('doctors.donations', compact('donations'));
    }
}