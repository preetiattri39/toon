<?php 
namespace App\Services\API;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

// Notification events
use App\Events\NewUser;

// Mail
use App\Mail\OTPMail;

// Models
use App\Models\User;
use App\Models\UserOtp;

class RegisterService
{
    /**
     * Registers a new user, assigns a role, sends a notification, and generates an OTP.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function registerUser($request)
    {
        $customer_role = config('global-constant.USER_ROLES.CUSTOMER');

        // Retrieve the customer role ID
        $customerRole = Role::where('name', $customer_role)->pluck('id');
        
        if ($customerRole->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Customer role does not exist. Please verify your role.'
            ];
        }

        // Create a new user
        $user = User::create([
            'name' => $request->full_name,
            'phone_code' => $request->phone_code,
            'phone_number' => $request->phone_number,
            'country_code' => $request->country_code,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign a default role to the user
        $user->assignRole($customerRole);

        // Trigger event notification for admin
        event(new NewUser([
            'title' => 'Customer Registration',
            'notification_type' => 'register_user',
            'type' => 'customer',
            'message' => 'A new customer '.$user->name.' is registered.',
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ]));                    
          
        // Generate a 4-digit OTP
        $otp = rand(1000, 9999);

        // Store OTP in the database with a 1-minute expiry time
        UserOtp::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'otp_expiry' => Carbon::now()->addMinutes(1),
        ]);

        // Send OTP to the user's email
        Mail::to($user->email)->send(new OTPMail($otp));

        return [
            'success' => true,
            'message' => 'User registered successfully. Please verify your OTP.',
            'result' => $user
        ];
    }

    /**
     * Verifies the OTP entered by the user.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function verifyOtp($request)
    {
        // Retrieve user by email
        $user = User::with('roles:id,name')->where('email', $request->email)->first();
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User email does not exist.',
            ];
        }

        // Retrieve OTP entry for the user
        $otpEntry = UserOtp::where('user_id', $user->id)
                    ->where('otp', $request->otp)
                    ->first();

        if (!$otpEntry) {
            return [
                'success' => false,
                'message' => 'Invalid OTP.',
            ];
        }

        // Check if OTP is expired
        if (Carbon::now()->greaterThan($otpEntry->otp_expiry)) {
            return [
                'success' => false,
                'message' => 'OTP has expired. Please request a new OTP.',
            ];
        }

        // Mark user as verified
        $user->is_email_verified = true;
        $user->email_verified_at = now();
        $user->save();

        // Delete OTP entry after successful verification
        $otpEntry->delete();

        $reset_password = $request->reset_password_verify;

        if($reset_password){
            return [
                'success' => true,
                'message' => 'OTP verified successfully.',
                'result' => null
            ];
        }else{
            // Log in the user
            Auth::login($user);

             // If user has a profile picture, generate the full asset URL
            if ($user->profile_pic) {
                $user->profile_pic = asset('/storage/profile/' . $user->profile_pic);
            }
            
            // Generate token
            $token = $user->createToken('Personal Access Token')->accessToken;

            return [
                'success' => true,
                'message' => 'OTP verified successfully. You are now logged in.',
                'result' => [
                    'user' => $user,
                    'token' => $token
                ]
            ];
        }

    }

    /**
     * Resends a new OTP if the previous one has expired.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function resendOtp($request)
    {
        // Retrieve user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found.',
            ];
        }

        // Get the most recent OTP entry
        // $otpEntry = UserOtp::where('user_id', $user->id)
        //     ->orderBy('otp_expiry', 'desc')
        //     ->first();

        // Check if existing OTP is still valid
        // if ($otpEntry && Carbon::now()->lt($otpEntry->otp_expiry)) {
        //     return [
        //         'success' => true,
        //         'message' => 'OTP is still valid.',
        //         'result' => $otpEntry->otp ?? null
        //     ];
        // }

        // Generate a new 4-digit OTP
        $otp = rand(1000, 9999);

        // Update or create a new OTP entry
        UserOtp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp' => $otp,
                'otp_expiry' => Carbon::now()->addMinutes(1),
            ]
        );

        // Send the new OTP to the user's email
        Mail::to($user->email)->send(new OTPMail($otp));
        
        return [
            'success' => true,
            'message' => 'A new OTP has been sent to your email.',
            'result' => $user
        ];
    }
}
?>
