<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Show the contact form and the history of messages.
     */
    public function index()
    {
        // Fetch feedback submitted by the current user, newest first
        $messages = Feedback::where('user_id', Auth::id())
                            ->latest()
                            ->get();

        // Pass the $messages variable to the view
        return view('contact.index', compact('messages'));
    }

    /**
     * Store a new feedback message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:5',
        ]);

        Feedback::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
            'status'  => 'pending',
        ]);

        return redirect()->back()->with('success', 'Your feedback has been submitted successfully!');
    }
}