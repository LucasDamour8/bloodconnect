<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DonorEligibilityController extends Controller
{
    public function index()
    {
        // Based on your routes, the view is likely at resources/views/eligibility/index.blade.php
        // If your file is actually at resources/views/eligibility.blade.php, change this to: return view('eligibility');
        return view('eligibility.index');
    }
}