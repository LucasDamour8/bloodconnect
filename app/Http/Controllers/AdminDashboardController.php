<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Donation;
use App\Models\Appointment;
use App\Models\Location;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Shared Stats Helper
     * Restores all keys required by the admin dashboard blade
     */
    private function shareStats()
    {
        $stats = [
            'total_donors'          => User::where('role', 'donor')->count(),
            'total_doctors'         => User::where('role', 'doctor')->count(),
            'total_donations'       => Donation::where('status', 'completed')->count(),
            'total_locations'       => Location::count(),
            
            'pending_appts'         => Appointment::where('status', 'pending')->count(),
            'completed_this_month'  => Donation::where('status', 'completed')
                                        ->whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year)
                                        ->count(),
            
            'pending_feedback'      => Feedback::where('status', 'pending')->count(),
            'pending'               => Appointment::where('status', 'pending')->count(),
        ];
        
        View::share('stats', $stats);
        return $stats;
    }

    /**
     * MAIN ADMIN OVERVIEW
     */
    public function index()
    {
        $stats = $this->shareStats();
        
        $recentUsers = User::where('role', 'donor')->latest()->take(5)->get();
        $recentDonations = Donation::with(['user', 'location'])->latest()->take(5)->get();
        $recentFeedback = Feedback::with('user')->where('status', 'pending')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentDonations', 'recentFeedback'));
    }

    /**
     * NEW: View Medical Report / PDF Logic
     */
    public function viewReport($id)
    {
        $this->shareStats();
        
        // Fetch appointment with all needed relationships, specifically 'donation'
        $appointment = Appointment::with(['user', 'location', 'donation', 'doctor'])->findOrFail($id);

        // If there is no donation record yet, redirect back
        if (!$appointment->donation) {
            return back()->with('error', 'No medical record found for this appointment yet.');
        }

        // Return the professional layout you provided
        return view('admin.appointments.report', [
            'donation' => $appointment->donation
        ]);
    }

    /**
     * MANAGE USERS (Donors) with SEARCH INTEGRATION
     */
    public function users(Request $request)
    {
        $this->shareStats();
        
        $query = User::where('role', 'donor');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%")
                  ->orWhere('custom_id', 'like', "%$s%");
            });
        }

        $users = $query->latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function create()
    {
        $this->shareStats();
        return view('admin.users_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'phone'      => 'required|string|max:20',
            'role'       => 'required|in:donor,doctor',
            'district'   => 'nullable|string|max:255',
            'blood_type' => 'nullable|string|max:5',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'role'       => $request->role,
            'district'   => $request->district,
            'blood_type' => $request->blood_type,
            'password'   => Hash::make('pass@123'), // Default password updated
            'is_active'  => true,
        ]);

        $target = ($request->role === 'doctor') ? 'admin.doctors.index' : 'admin.users';
        return redirect()->route($target)->with('success', ucfirst($request->role) . " created successfully. Default password is: pass@123");
    }

    /**
     * RESET PASSWORD METHOD
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make('password123') // Sets password to password123 as requested
        ]);

        return back()->with('success', "Password for {$user->first_name} has been reset to: password123");
    }

    /**
     * MANAGE ALL APPOINTMENTS
     */
    public function appointmentsIndex(Request $request)
    {
        $this->shareStats();
        
        $status = $request->get('status');
        
        $query = Appointment::with(['user', 'location', 'doctor', 'completer', 'donation']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->whereHas('user', function($inner) use ($s) {
                    $inner->where('first_name', 'like', "%$s%")
                          ->orWhere('last_name', 'like', "%$s%")
                          ->orWhere('phone', 'like', "%$s%");
                })->orWhereHas('location', function($inner) use ($s) {
                    $inner->where('name', 'like', "%$s%");
                })->orWhere('tracking_id', 'like', "%$s%");
            });
        }

        if ($status && strtoupper($status) !== 'ALL') {
            $query->where('status', $status);
        }

        $appointments = $query->latest()->paginate(15);

        $counts = [
            'ALL'       => Appointment::count(),
            'SCHEDULED' => Appointment::where('status', 'scheduled')->count(),
            'APPROVED'  => Appointment::where('status', 'approved')->count(),
            'COMPLETED' => Appointment::where('status', 'completed')->count(),
            'CANCELLED' => Appointment::where('status', 'cancelled')->count(),
        ];

        $groupedAppointments = Appointment::with(['user', 'location'])
            ->where('status', 'pending')
            ->get()
            ->groupBy('location.name');

        return view('admin.appointments.index', compact('appointments', 'groupedAppointments', 'counts'));
    }

    public function updateAppointmentStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:scheduled,approved,completed,cancelled,SCHEDULED,APPROVED,COMPLETED,CANCELLED',
        ]);

        $appointment = Appointment::findOrFail($id);
        
        $appointment->update([
            'status' => strtolower($request->status)
        ]);

        return redirect()->back()->with('success', "Appointment status updated to: " . ucfirst($request->status));
    }

    public function reports(Request $request)
    {
        $globalStats = $this->shareStats();
        
        $reportStats = [
            'donors'               => $globalStats['total_donors'],
            'completed_donations'  => $globalStats['total_donations'],
            'locations'            => $globalStats['total_locations'],
            'pending_appointments' => $globalStats['pending'],
        ];

        $selectedStatus = $request->get('chart_status', 'completed');
        $validStatuses = ['completed', 'approved', 'cancelled', 'scheduled'];
        if (!in_array($selectedStatus, $validStatuses)) {
            $selectedStatus = 'completed';
        }

        $currentMonth = now()->month;
        $currentYear = now()->year;
        $daysInMonth = now()->daysInMonth;
        $monthName = now()->format('F Y');

        $rawChartData = Appointment::where('status', $selectedStatus)
            ->select(
                DB::raw('count(id) as total'),
                DB::raw('DAY(updated_at) as day')
            )
            ->whereMonth('updated_at', $currentMonth)
            ->whereYear('updated_at', $currentYear)
            ->groupBy('day')
            ->pluck('total', 'day');

        $chartLabels = [];
        $chartValues = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $chartLabels[] = "Day " . $i;
            $chartValues[] = $rawChartData->get($i, 0); 
        }

        $locations = Location::withCount(['donations' => function($query) use ($currentMonth, $currentYear) {
            $query->where('status', 'completed')
                  ->whereMonth('created_at', $currentMonth)
                  ->whereYear('created_at', $currentYear);
        }])->orderBy('donations_count', 'desc')->take(5)->get();

        $totalCompletedMonthly = Donation::where('status', 'completed')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $bloodDist = Donation::where('status', 'completed')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->select('blood_type', DB::raw('count(*) as total'))
            ->groupBy('blood_type')
            ->get();

        $bloodTypes = $bloodDist->map(function($item) use ($totalCompletedMonthly) {
            $percentage = $totalCompletedMonthly > 0 ? round(($item->total / $totalCompletedMonthly) * 100) : 0;
            return [
                'type' => $item->blood_type ?? 'N/A',
                'percentage' => $percentage . '%',
                'color' => str_contains($item->blood_type, 'O') ? 'bg-red-500' : 'bg-red-400'
            ];
        });

        $latestDonors = User::where('role', 'donor')->latest()->take(5)->get()->map(function($u) {
            return ['title' => "New Donor: {$u->first_name}", 'time' => $u->created_at->diffForHumans(), 'icon' => 'bg-green-500'];
        });

        $latestDonations = Donation::where('status', 'completed')->latest()->take(5)->get()->map(function($d) {
            return ['title' => "Donation #{$d->id} Completed", 'time' => $d->created_at->diffForHumans(), 'icon' => 'bg-red-600'];
        });

        $latestAppointments = Appointment::latest()->take(5)->get()->map(function($a) {
            return ['title' => "Appt #{$a->id} set to " . strtoupper($a->status), 'time' => $a->updated_at->diffForHumans(), 'icon' => 'bg-blue-500'];
        });

        $recentActivity = $latestDonors->concat($latestDonations)->concat($latestAppointments)
            ->sortByDesc('time')
            ->take(10);

        return view('admin.reports.index', [
            'stats'          => $reportStats,
            'locations'      => $locations,
            'bloodTypes'     => $bloodTypes,
            'recentActivity' => $recentActivity,
            'chartLabels'    => $chartLabels,
            'chartValues'    => $chartValues,
            'monthName'      => $monthName,
            'selectedStatus' => $selectedStatus
        ]);
    }

    public function replyToFeedback(Request $request, $id)
    {
        $request->validate(['admin_reply' => 'required|string']);
        $feedback = Feedback::findOrFail($id);
        $feedback->update([
            'admin_reply' => $request->admin_reply,
            'status' => 'replied'
        ]);
        return back()->with('success', "Reply sent to {$feedback->user->first_name}.");
    }

    public function feedbackIndex()
    {
        $this->shareStats();
        $feedbacks = Feedback::with('user')->latest()->paginate(15);
        return view('admin.feedback.index', compact('feedbacks'));
    }

   public function updateUser(Request $request, $id)
{
    $user = User::findOrFail($id);

    // 1. Add names and email to validation
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'email'      => 'required|email|unique:users,email,' . $id,
        'phone'      => 'nullable|string|max:20',
        'district'   => 'nullable|string|max:100',
        'blood_type' => 'nullable|string|max:5',
        'photo'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    // Handle Photo upload
    if ($request->hasFile('photo')) {
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }
        $user->profile_photo = $request->file('photo')->store('profile-photos', 'public');
    }

    // 2. Add names and email to the update list
    $user->update($request->only([
        'first_name', 
        'last_name', 
        'email', 
        'phone', 
        'district', 
        'blood_type'
    ]));

    return back()->with('success', "User updated successfully.");
}

    public function doctorsIndex(Request $request)
    {
        $this->shareStats();
        
        $query = User::where('role', 'doctor');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%");
            });
        }

        $doctors = $query->latest()->paginate(15);
        return view('doctors.index', compact('doctors'));
    }

    public function updateDoctor(Request $request, $id)
    {
        $doctor = User::findOrFail($id);
        $request->validate(['phone' => 'nullable|string', 'district' => 'nullable|string', 'photo' => 'nullable|image']);

        if ($request->hasFile('photo')) {
            if ($doctor->profile_photo) {
                Storage::disk('public')->delete($doctor->profile_photo);
            }
            $doctor->profile_photo = $request->file('photo')->store('profile-photos', 'public');
        }

        $doctor->update($request->only(['phone', 'district']));
        $doctor->save();

        return back()->with('success', "Doctor updated successfully.");
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active; 
        $user->save();
        return back()->with('success', "Status updated.");
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }
        $user->delete();
        return back()->with('success', "Donor record removed permanently.");
    }

    public function donations()
    {
        $this->shareStats();
        $donations = Donation::with(['user', 'location'])->latest()->paginate(15);
        return view('admin.donations', compact('donations'));
    }

    public function updateRole(Request $request, $id)
    {
        User::findOrFail($id)->update(['role' => $request->role]);
        return back()->with('success', "Role updated successfully.");
    }

    public function centersIndex() { $this->shareStats(); return view('admin.centers.index'); }
    public function centersStore(Request $request) { return back()->with('success', 'Center added.'); }
}