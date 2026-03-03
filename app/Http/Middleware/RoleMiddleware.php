<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        $userRole = $user->role;
        
        // If user doesn't have the required role, redirect to their appropriate dashboard
        if ($userRole !== $role) {
            if ($user->isAdmin()) {
                return redirect()->route('dashboard');
            } elseif ($user->isEmploye()) {
                return redirect()->route('dashboard.employe');
            }
            
            // Fallback: logout if role is unknown
            Auth::logout();
            return redirect()->route('login');
        }
        
        return $next($request);
    }
}
