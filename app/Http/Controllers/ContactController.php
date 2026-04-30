<?php
namespace App\Http\Controllers;

use App\Models\Feedback; // Ensure you have the Feedback model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        // Users only see their own feedback
        $messages = Feedback::where('user_id', Auth::id())->latest()->get();
        return view('contact.index', compact('messages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Feedback::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your issue has been submitted to Admin.');
    }

    // Admin function to reply
    public function reply(Request $request, $id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->update([
            'admin_reply' => $request->reply,
            'status' => 'replied'
        ]);

        return back()->with('success', 'Reply sent to user.');
    }
}