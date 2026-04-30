<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
public function handle(Request $request, Closure $next, string $role)
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $userRole = Auth::user()->role;

    // Check if the user has the required role
    if ($userRole !== $role) {
        $targetRoute = match($userRole) {
            'admin'  => 'admin.dashboard',
            'doctor' => 'doctor.dashboard',
            default  => 'dashboard', // Donor dashboard
        };

        // PREVENT THE LOOP: 
        // If the current request is already going to the targetRoute, let it pass!
        if ($request->routeIs($targetRoute)) {
            return $next($request);
        }

        return redirect()->route($targetRoute);
    }

    return $next($request);
}
}