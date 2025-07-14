<?php 

namespace App\Services\API;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

// Notification events
use App\Events\NewUser;

// Mail
use App\Mail\OTPMail;

// Models
use App\Models\User;
use App\Models\UserOtp;

class LoginService
{
    /**
     * Handle user login.
     *
     * @param \Illuminate\Http\Request $request The login request containing email and password.
     * @return array JSON response indicating success or failure, along with user data and token if successful.
     */
    public function loginUser($request)
    {
        $user = User::with('roles:id,name')->where('email', $request->email)->first();

        if ($user && isset($user->roles)) {
            foreach ($user->roles as $role) {
                // Ensure no pivot data is included
                unset($role->pivot);
            }
        }

        // Check if user exists and if the password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Incorrect email or password',
                'result' => ''
            ];
        }

        if ($user->status == 0) {
        return [
            'success' => false,
            'message' => 'Your account is currently inactive. You cannot log in at the moment.',
            'result' => ''
        ];
    }




        // Check if the user is verified
        if (!$user->is_email_verified) {

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
            Mail::to($user->email)->send(new OtpMail($otp));

            return [
                'success' => true,
                'message' => 'Please verify your email/OTP before logging in.',
                'result' => [
                    'verify_status' => 'not_verify',
                    'user_email' => $user->email ?? null,
                ]
            ];
        }

        // Check if the user is blocked
        if (!$user->status) {
            return [
                'success' => false,
                'message' => 'Your account is currently inactive. You cannot log in at the moment.',
                'result' => (object) [] 
            ];
        }

        // If user has a profile picture, generate the full asset URL
        if ($user->profile_pic) {
            $user->profile_pic = asset('/storage/profile/' . $user->profile_pic);
        }

        // Generate token
        $token = $user->createToken('Personal Access Token')->accessToken;

        return [
            'success' => true,
            'message' => 'User login successfully',
            'result' => [
                'user' => $user,
                'token' => $token
            ]
        ];
    }


    /**
     * Handles the Google callback for user authentication or registration.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming request object.
     * @return array  An array containing success status, message, and user data or errors.
     */
    public function handleGoogleCallbackUser($request)
    {
        // Retrieve the customer role ID from config
        $customer_role = config('global-constant.USER_ROLES.CUSTOMER');

        // Retrieve the customer role based on its name
        $customerRole = Role::where('name', $customer_role)->pluck('id');
        
        // If the customer role doesn't exist, return an error message
        if ($customerRole->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Customer role does not exist. Please verify your role.'
            ];
        }

        // Retrieve user information from Google via Socialite
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Check if a user already exists with the provided Google ID
        $user = User::where('google_id', $googleUser->getId())->first();

        // If user does not exist, create a new user
        if (!$user) {
            // Check if a user already exists with the same email address
            $checkmail = User::where('email', $googleUser->getEmail())->first();
            
            if ($checkmail) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already exists with this email, you cannot log in using Google login.',
                ]);
            }

            // Create a new user in the database
            $user = User::create([
                'name' => $googleUser->getName(),
                'first_name' => $googleUser->getName(),
                'last_name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'google_email' => $googleUser->getEmail(),
                'password' => bcrypt(Str::random(16)), // Generate a random password for the user
            ]);

            // Assign the customer role to the newly created user
            $user->assignRole($customerRole);

            // Trigger an event to notify admin about the new user registration
            event(new NewUser([
                'title' => 'Customer Registration',
                'notification_type' => 'register_user',
                'type' => 'customer',
                'message' => 'A new customer ' . $user->name . ' is registered.',
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]));

            // Generate a 4-digit OTP for email verification
            $otp = rand(1000, 9999);

            // Store OTP in the UserOtp table with an expiry time of 5 minutes
            $otpEntry = UserOtp::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'otp_expiry' => Carbon::now()->addMinutes(5), // OTP valid for 5 minutes
            ]);

            // Send OTP to the user's email address
            Mail::to($user->email)->send(new OTPMail($otp));

            return [
                'success' => true,
                'message' => 'User registered successfully. Please verify your OTP.',
                'result' => [
                    'user' => $user,
                    'otp' => $otp,
                ]
            ];
        }

        // Check if the user has verified their email
        if (!$user->is_email_verified) {
            return [
                'success' => false,
                'message' => 'Please verify your OTP before logging in.',
            ];
        }

        // Check if the user's account is blocked
        if (!$user->status) {
            return [
                'success' => false,
                'message' => 'Your account is currently blocked. You cannot log in at the moment.',
            ];
        }

        // If the user has a profile picture, generate the full URL for it
        if ($user->profile_pic) {
            $user->profile_pic = asset('/storage/profile/' . $user->profile_pic);
        }

        // Generate an access token for the user using Passport
        $token = $user->createToken('Personal Access Token')->accessToken;

        return [
            'success' => true,
            'message' => 'User logged in successfully.',
            'result' => [
                'user' => $user,
                'token' => $token
            ]
        ];
    }


    /**
     * Handle the callback from Facebook after a user attempts to log in via Facebook.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing user data.
     *
     * @return array A structured array with the result of the login or registration process.
     */
    public function handleFacebookCallbackUser($request)
    {
        // Retrieve the customer role ID from config
        $customer_role = config('global-constant.USER_ROLES.CUSTOMER');

        // Retrieve the customer role based on its name
        $customerRole = Role::where('name', $customer_role)->pluck('id');
        
        // If the customer role doesn't exist, return an error message
        if ($customerRole->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Customer role does not exist. Please verify your role.'
            ];
        }

        // Retrieve user information from Google via Socialite
        $facebookUser = Socialite::driver('facebook')->stateless()->user();

        // Check if a user already exists with the provided Google ID
        $user = User::where('facebook_id', $facebookUser->getId())->first();

        // If user does not exist, create a new user
        if (!$user) {
            // Check if a user already exists with the same email address
            $checkmail = User::where('email', $facebookUser->getEmail())->first();
            
            if ($checkmail) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already exists with this email, you cannot log in using Google login.',
                ]);
            }

            // Create a new user in the database
            $user = User::create([
                'name' => $facebookUser->getName(),
                'first_name' => $facebookUser->getName(),
                'last_name' => $facebookUser->getName(),
                'email' => $facebookUser->getEmail(),
                'facebook_id' => $facebookUser->getId(),
                'facebook_email' => $facebookUser->getEmail(),
                'password' => bcrypt(Str::random(16)), // Generate a random password for the user
            ]);

            // Assign the customer role to the newly created user
            $user->assignRole($customerRole);

            // Trigger an event to notify admin about the new user registration
            event(new NewUser([
                'title' => 'Customer Registration',
                'notification_type' => 'register_user',
                'type' => 'customer',
                'message' => 'A new customer ' . $user->name . ' is registered.',
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]));

            // Generate a 4-digit OTP for email verification
            $otp = rand(1000, 9999);

            // Store OTP in the UserOtp table with an expiry time of 5 minutes
            $otpEntry = UserOtp::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'otp_expiry' => Carbon::now()->addMinutes(5), // OTP valid for 5 minutes
            ]);

            // Send OTP to the user's email address
            Mail::to($user->email)->send(new OTPMail($otp));

            return [
                'success' => true,
                'message' => 'User registered successfully. Please verify your OTP.',
                'result' => [
                    'user' => $user,
                    'otp' => $otp,
                ]
            ];
        }

        // Check if the user has verified their email
        if (!$user->is_email_verified) {
            return [
                'success' => false,
                'message' => 'Please verify your OTP before logging in.',
            ];
        }

        // Check if the user's account is blocked
        if (!$user->status) {
            return [
                'success' => false,
                'message' => 'Your account is currently blocked. You cannot log in at the moment.',
            ];
        }

        // If the user has a profile picture, generate the full URL for it
        if ($user->profile_pic) {
            $user->profile_pic = asset('/storage/profile/' . $user->profile_pic);
        }

        // Generate an access token for the user using Passport
        $token = $user->createToken('Personal Access Token')->accessToken;

        return [
            'success' => true,
            'message' => 'User logged in successfully.',
            'result' => [
                'user' => $user,
                'token' => $token
            ]
        ];
    }


    /**
     * Handle Apple Login Callback for User.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function handleAppleCallbackUser($request)
    {
        // Retrieve the customer role ID from config
        $customer_role = config('global-constant.USER_ROLES.CUSTOMER');

        // Retrieve the customer role based on its name
        $customerRole = Role::where('name', $customer_role)->pluck('id');
        
        // If the customer role doesn't exist, return an error message
        if ($customerRole->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Customer role does not exist. Please verify your role.'
            ];
        }

        // Get user information from Apple
        $appleUser = Socialite::driver('apple')->stateless()->user();

        // Check if user exists with Apple ID
        $user = User::where('apple_id', $appleUser->getId())->first();

        // If user does not exist, register a new user
        if (!$user) {
            // Check if user already exists by email
            $checkmail = User::where('email', $appleUser->getEmail())->first();
            
            if ($checkmail) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already exists with this email, you cannot log in using Apple login.',
                ]);
            }

            // Create a new user record
            $user = User::create([
                'name' => $appleUser->getName(),
                'first_name' => $appleUser->getName(),
                'last_name' => $appleUser->getName(),
                'email' => $appleUser->getEmail(),
                'apple_id' => $appleUser->getId(),
                'apple_email' => $appleUser->getEmail(),
                'password' => bcrypt(Str::random(16)), // Generate a random password
            ]);

            // Assign the customer role to the new user
            $user->assignRole($customerRole);

            // Fire event to notify admin about new registration
            event(new NewUser([
                'title' => 'Customer Registration',
                'notification_type' => 'register_user',
                'type' => 'customer',
                'message' => 'A new customer ' . $user->name . ' is registered.',
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]));

            // Generate a 4-digit OTP for email verification
            $otp = rand(1000, 9999);

            // Store OTP with 5 minutes expiry
            UserOtp::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'otp_expiry' => Carbon::now()->addMinutes(5),
            ]);

            // Send OTP via email
            Mail::to($user->email)->send(new OTPMail($otp));

            return [
                'success' => true,
                'message' => 'User registered successfully. Please verify your OTP.',
                'result' => [
                    'user' => $user,
                    'otp' => $otp,
                ]
            ];
        }

        // Check if the user is email-verified
        if (!$user->is_email_verified) {
            return [
                'success' => false,
                'message' => 'Please verify your OTP before logging in.',
            ];
        }

        // Check if the user account is active
        if (!$user->status) {
            return [
                'success' => false,
                'message' => 'Your account is currently blocked. You cannot log in at the moment.',
            ];
        }

        // Append full URL for profile picture if available
        if ($user->profile_pic) {
            $user->profile_pic = asset('/storage/profile/' . $user->profile_pic);
        }

        // Generate an access token for the user
        $token = $user->createToken('Personal Access Token')->accessToken;

        return [
            'success' => true,
            'message' => 'User logged in successfully.',
            'result' => [
                'user' => $user,
                'token' => $token
            ]
        ];
    }


    /**
     * Handle social login for users via Google or Apple.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing social login details (type, id, email).
     * @return array An array containing the success status, message, and user data or error details.
     *
     */
    public function socialLoginUser($request)
    {
        try{
            
            // Retrieve the customer role ID from config
            $customer_role = config('global-constant.USER_ROLES.CUSTOMER');

            // Retrieve the customer role based on its name
            $customerRole = Role::where('name', $customer_role)->pluck('id');
            
            // If the customer role doesn't exist, return an error message
            if ($customerRole->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Customer role does not exist. Please verify your role.'
                ];
            }

            $type = $request->type;

            // Validate that the type is either 'google' or 'apple'
            if (!in_array($type, ['google', 'apple'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid login type. Only Google or Apple login is supported.',
                ];
            }

            if($type=='google'){
                $user = User::where('google_id', $request->id)->first();
            }

            if($type=='apple'){
                $user = User::where('apple_id', $request->id)->first();
            }

            // If user does not exist, create a new user
            if (!$user) {

                if($type=='google'){
                    $user = User::create([
                        'email' => $request->email,
                        'google_id' => $request->id,
                        'google_email' => $request->email,
                        'password' => bcrypt(Str::random(16)),
                    ]);
                }

                if($type=='apple'){
                    $user = User::create([
                        'email' => $request->email,
                        'apple_id' => $request->id,
                        'apple_email' => $request->email,
                        'password' => bcrypt(Str::random(16)),
                    ]);
                }

                if($type=='facebook'){
                    $user = User::create([
                        'email' => $request->email,
                        'facebook_id' => $request->id,
                        'facebook_email' => $request->email,
                        'password' => bcrypt(Str::random(16)),
                    ]);
                }

                // Assign the customer role to the newly created user
                $user->assignRole($customerRole);

                // Trigger an event to notify admin about the new user registration
                event(new NewUser([
                    'title' => 'Customer Registration',
                    'notification_type' => 'register_user',
                    'type' => 'customer',
                    'message' => 'A new customer ' . $request->email ?? null. ' is registered.',
                    'user_id' => $user->id,
                    'email' => $request->email
                ]));

                // Generate a 5-digit OTP for email verification
                $otp = rand(10000, 99999);

                // Store OTP in the UserOTP table with an expiry time of 5 minutes
                $otpEntry = UserOTP::create([
                    'user_id' => $user->id,
                    'otp' => $otp,
                    'otp_expiry' => Carbon::now()->addMinutes(5), // OTP valid for 5 minutes
                ]);

                // Send OTP to the user's email address
                // Mail::to($user->email)->send(new OTPMail($otp));

                return [
                    'success' => true,
                    'message' => 'User registered successfully. Please verify your OTP.',
                    'result' => [
                        'user' => $user,
                        'otp' => $otp,
                    ]
                ];
            }

            // Check if the user has verified their email
            if (!$user->is_email_verified) {
                return [
                    'success' => false,
                    'message' => 'Please verify your OTP before logging in.',
                ];
            }

            // Check if the user's account is blocked
            if (!$user->status) {
                return [
                    'success' => false,
                    'message' => 'Your account is currently blocked. You cannot log in at the moment.',
                ];
            }

            // If the user has a profile picture, generate the full URL for it
            if ($user->profile_pic) {
                $user->profile_pic = asset('/storage/profile/' . $user->profile_pic);
            }

            // Generate an access token for the user using Passport
            $token = $user->createToken('Personal Access Token')->accessToken;

            return [
                'success' => true,
                'message' => 'User logged in successfully.',
                'result' => [
                    'user' => $user,
                    'token' => $token
                ]
            ];

        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }

    }

}
?>
