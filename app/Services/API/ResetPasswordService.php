<?php 

namespace App\Services\API;

use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Str;

// Models
use App\Models\User;

class ResetPasswordService
{
    /**
     * Reset the user's password using a reset token.
     *
     * @param object $request The request containing the reset token and new password.
     * @return array The response indicating success or failure.
     */
    public function resetUserPassword($request)
    {
        // Find the password reset record using the token
        $resetRecord = DB::table('password_reset_tokens')->where('token', $request->token)->first();

        // Check if the token exists
        if (!$resetRecord) {
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

        // Find the user associated with the email in the reset token record
        $user = User::where('email', $resetRecord->email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found.',
            ];
        }

        // Update the user's password with the newly provided password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the reset token after use to prevent reuse
        DB::table('password_reset_tokens')->where('token', $request->token)->delete();

        return [
            'success' => true,
            'message' => 'Password has been successfully reset.',
            "result" => null
        ];
    }

    /**
     * Reset the user's password using an OTP-based verification.
     *
     * @param object $request The request containing the email and new password.
     * @return array The response indicating success or failure.
     */
    public function resetUserPasswordWithOTP($request)
    {
        // Find the user associated with the provided email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found.',
            ];
        }

        // Update the user's password with the newly provided password
        $user->password = Hash::make($request->password);
        $user->save();

        return [
            'success' => true,
            'message' => 'Password has been successfully reset.',
            "result" => null
        ];
    }
}
?>
