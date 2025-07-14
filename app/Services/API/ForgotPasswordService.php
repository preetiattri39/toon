<?php 

namespace App\Services\API;

use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Str;
use App\Mail\OTPMail;

// Models
use App\Models\User;
use App\Models\UserOtp;

class ForgotPasswordService
{
    /**
     * Handles the process for a user who has forgotten their password.
     * 
     * This method will attempt to find a user by their email address, and if
     * found, it will generate a password reset token, store it in the database, 
     * and send a password reset link to the user's email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function forgotUserPassword($request)
    {
        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // If user does not exist, return an error message
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found.',
            ];
        }

        // Generate a reset token for password reset
        $resetToken = Str::random(60);

        // Save the generated token into the password_resets table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $resetToken,
                'created_at' => Carbon::now()
            ]
        );

        // Create a reset link that includes the generated reset token
        $resetLink = "http://127.0.0.1:8000/auth/reset-password?token=" . $resetToken;

        // Send the password reset link to the user's email address
        Mail::to($user->email)->send(new PasswordResetMail($resetLink));

        // Return success message
        return [
            'success' => true,
            'message' => 'Password reset link sent to your email.',
            "result" => null
        ];
    }   

    /**
     * Handles the process for a user who has forgotten their password using OTP.
     *
     * This method will attempt to find a user by their email address, and if
     * found, it will generate a 4-digit OTP (One-Time Password), store it in
     * the database with an expiration time, and send the OTP to the user's email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function forgotUserPasswordWithOTP($request)
    {
        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // If user does not exist, return an error message
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found.',
            ];
        }

        // Generate a new 4-digit OTP (One-Time Password)
        $otp = rand(1000, 9999);

        // Update or create a new OTP entry for the user in the database
        UserOtp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp' => $otp,
                'otp_expiry' => Carbon::now()->addMinutes(1), // OTP expires in 1 minute
            ]
        );

        // Send the OTP to the user's email address
        Mail::to($user->email)->send(new OTPMail($otp));

        // Return success message
        return [
            'success' => true,
            'message' => 'Password reset OTP sent to your mail.',
            "result" => null
        ];
    }   
}
?>
