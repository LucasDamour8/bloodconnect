<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\User;
use App\Models\Location;
use App\Models\Appointment;
use App\Models\Donation;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Display the landing page
     */
    public function index()
    {
        $announcements = Announcement::latest()->take(3)->get();
        $stats = $this->getStats();
        
        // Added to support the Donation Centers SPA view
        $locations = Location::all(); 

        return view('index', compact('announcements', 'stats', 'locations'));
    }

    /**
     * Handle Appointment Tracking
     */
    public function track(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|string',
        ]);

        // 1. Find the appointment using tracking_id or ID
        $appointment = Appointment::where('tracking_id', $request->appointment_id)
            ->orWhere('id', $request->appointment_id)
            ->first();

        if (!$appointment) {
            return back()->with('error', 'No record found with that Tracking ID.');
        }

        // 2. Fetch the medical results from the 'donations' table 
        $medicalResult = Donation::where('appointment_id', $appointment->id)->first();

        // 3. Added $locations here as well so the page doesn't crash after tracking
        return view('index', [
            'announcements' => Announcement::latest()->take(3)->get(),
            'stats' => $this->getStats(),
            'locations' => Location::all(), 
            'tracked_appointment' => $appointment,
            'medical_result' => $medicalResult 
        ]);
    }

    /**
     * Store feedback/contact messages
     * Logic updated to point to 'userfeedbacks' table
     */
    public function feedback(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string'
        ]);

        // Insert directly into the newly created userfeedbacks table
        DB::table('userfeedbacks')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Thank you! Your feedback has been received.');
    }

    /**
     * Helper to get dashboard statistics
     */
    private function getStats()
    {
        return [
            'donors_count' => User::where('role', 'donor')->count(),
            'centers_count' => Location::count(),
            'lives_saved'   => Appointment::where('status', 'completed')->count() * 3,
        ];
    }
}