<?php 

namespace App\Services\Admin;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Models\Admin;

class ForgotPasswordService
{
    /**
     * Handle the process of requesting a password reset for an admin user.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing the email.
     * @return array An associative array containing:
     *               - 'success' (bool): Indicates if the operation was successful.
     *               - 'message' (string): Provides details about the operation result.
     */
    public function forgotUserPassword($request)
    {
        try {
            // Find the user by the provided email
            $user = Admin::where('email', $request->email)->first();

            if (!$user) {
                // If no user is found with the given email, return an error response
                return [
                    'success' => false,
                    'message' => 'User not found.',
                ];
            }

            // Generate a secure random reset token
            $resetToken = Str::random(60);

            // Store or update the token in the password reset tokens table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => $resetToken,
                    'created_at' => Carbon::now() // Record token creation timestamp
                ]
            );

            // Generate a password reset link with the token
            $resetLink = url('/admin/reset-password/' . $resetToken);

            // Send the reset link to the user's email
            Mail::to($user->email)->send(new PasswordResetMail($resetLink));

            return [
                'success' => true,
                'message' => 'Password reset link sent to your email.',
            ];

        } catch (\Exception $e) {
            // Handle any exceptions and return the error message
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle the process of resetting an admin user's password using a token.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing the token and new password.
     * @return array An associative array containing:
     *               - 'success' (bool): Indicates if the operation was successful.
     *               - 'message' (string): Provides details about the operation result.
     */
    public function resetAdminUserPassword($request)
    {
        try {
            // Retrieve the password reset record for the provided token
            $resetRecord = DB::table('password_reset_tokens')->where('token', $request->token)->first();

            if (!$resetRecord) {
                // Return an error if no matching token record is found
                return [
                    'success' => false,
                    'message' => 'Invalid or expired token.',
                ];
            }

            // Check if the token has expired (valid for 60 minutes)
            if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
                return [
                    'success' => false,
                    'message' => 'Token has expired.',
                ];
            }

            // Find the user associated with the email in the reset record
            $user = Admin::where('email', $resetRecord->email)->first();

            if (!$user) {
                // Return an error if the user cannot be found
                return [
                    'success' => false,
                    'message' => 'User not found.',
                ];
            }

            // Update the user's password with the new one from the request
            $user->password = Hash::make($request->password); // Hash the new password
            $user->save(); // Persist the changes to the database

            // Delete the reset token to prevent reuse
            DB::table('password_reset_tokens')->where('token', $request->token)->delete();

            return [
                'success' => true,
                'message' => 'Password has been successfully reset.',
            ];

        } catch (\Exception $e) {
            // Handle exceptions and return the error message
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
