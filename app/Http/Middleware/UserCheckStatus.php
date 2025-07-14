<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UserCheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated and their account is active
        $user = Auth::user();
        
        if ($user && $user->status == 0) {
            // Return a response if the user is blocked
            return response()->json([
                'status' => 'failed',
                'message' => 'Your account has been disabled. Please contact the admin.',
            ], 403);
        }

        return $next($request);
    }
}
