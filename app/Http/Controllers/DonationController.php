<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    /**
     * Display a listing of donations belonging ONLY to the logged-in user.
     */
    public function index()
    {
        // We filter by user_id to ensure privacy and security
        $donations = Donation::with(['location'])
            ->where('user_id', Auth::id()) 
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('donations.index', compact('donations'));
    }
}