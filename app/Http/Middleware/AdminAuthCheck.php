<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminAuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the admin user is logged in using the 'admin' guard
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            $admin_role = $admin->roles->first()->guard_name ?? null;
            // Ensure the user has the correct role
            if ($admin_role === 'admin') {
                return $next($request);
            }
        }

        // Redirect to the custom admin login page if not logged in
        return redirect()->route('login');
    }
}
