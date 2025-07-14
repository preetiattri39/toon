<?php 

namespace App\Services\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\Admin;

class LoginService
{
    /**
     * Attempt to log in an admin user.
     *
     * This method uses Laravel's authentication system to attempt to authenticate
     * an admin user based on the provided credentials (email and password).
     * 
     * @param \Illuminate\Http\Request $request The incoming request containing the admin's credentials.
     * @return array The result of the login attempt, including a success flag and message.
     */
    public function loginAdminUser($request)
    {
        try {
            // Extract the email and password from the request
            $credentials = $request->only(['email', 'password']);
            
            // Check if the "remember" option was selected (for persistent login)
            $remember = $request->has('remember');

            // Attempt to log in the admin user using the 'admin' guard
            if (Auth::guard('admin')->attempt($credentials, $remember)) {
                // If login is successful, retrieve the authenticated user
                $user = Auth::guard('admin')->user();

                // Return a success response if the login is successful
                return [
                    'success' => true,
                    'message' => 'Login successful.',
                ];
            }

            // If login failed (invalid credentials), return an error message
            return [
                'success' => false,
                'message' => 'Invalid email or password. Please try again.',
            ];

        } catch (\Exception $e) {
            // Catch any unexpected exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }
}
?>
