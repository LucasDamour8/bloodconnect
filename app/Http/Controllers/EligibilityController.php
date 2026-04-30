<?php
namespace App\Http\Controllers;

use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EligibilityController extends Controller
{
    public function index()
    {
        return view('eligibility');
    }

    public function check(Request $request)
    {
        $answers = $request->only([
            'feeling_well', 'weight', 'age', 'recent_illness',
            'recent_travel', 'medications', 'last_donation',
        ]);

        $eligible = true;
        $deferralReason = null;

        if (($answers['feeling_well'] ?? '') === 'no') {
            $eligible = false;
            $deferralReason = 'You must be feeling well to donate.';
        }

        if (($answers['weight'] ?? 0) < 50) {
            $eligible = false;
            $deferralReason = 'You must weigh at least 50kg (110 lbs) to donate blood safely.';
        }

        if (($answers['age'] ?? 0) < 17) {
            $eligible = false;
            $deferralReason = 'You must be at least 17 years old to donate.';
        }

        $result = QuizResult::create([
            'user_id'         => Auth::id(),
            'is_eligible'     => $eligible,
            'deferral_reason' => $deferralReason,
            'answers'         => json_encode($answers),
        ]);

        return response()->json([
            'eligible'        => $eligible,
            'deferral_reason' => $deferralReason,
        ]);
    }
}