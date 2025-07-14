<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\LoginRequest;
use Laravel\Socialite\Facades\Socialite;
use Auth;
use App\Http\Requests\API\SocialLoginRequest;

class LoginController extends APIBaseController
{
    /**
     * Handles social login requests via a validated SocialLoginRequest.
     *
     * @param \App\Http\Requests\SocialLoginRequest $request.
     *
     * @return \Illuminate\Http\JsonResponse
     *         JSON response containing:
     */
    public function socialLogin(SocialLoginRequest $request)
    {
        try {  

            $socialLogin = $this->login_service->socialLoginUser($request);

            // If the authentication/registration is successful
            if($socialLogin['success']){
                return response()->json([
                    'status' => 'success',
                    'message' => $socialLogin['message'],
                    'data' => $socialLogin['result'],
                ],$this->success_status); 
            }else{

                return response()->json([
                    'status' => 'failed',
                    'message' => $socialLogin['message'],
                    'data' => [],
                ],$this->bad_request_status); //400 Bad Request status

            }
    
        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Failed to login user.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Redirect the user to Apple’s OAuth authorization page.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming HTTP request instance.
     * @return \Illuminate\Http\RedirectResponse  Redirect response to Apple's OAuth URL.
     */
    public function redirectToApple(Request $request)
    {
        // Build the Google redirect URL (stateless method for OAuth)
        $redirectUrl = Socialite::driver('apple')->stateless()->redirect()->getTargetUrl();

        // Redirect to Google with the generated URL
        return redirect($redirectUrl);
    }

    /**
     * Handle the callback from Apple's OAuth authentication.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming HTTP request instance containing the callback data.
     * @return \Illuminate\Http\JsonResponse  A JSON response indicating the success or failure of the operation.
     * 
     */
    public function handleAppleCallback(Request $request)
    {
        try {  
            $appleAuth = $this->login_service->handleAppleCallbackUser($request);

            // If the authentication/registration is successful
            if($appleAuth['success']){
                return response()->json([
                    'status' => 'success',
                    'message' => $appleAuth['message'],
                    'data' => $appleAuth['result'],
                ],$this->success_status); 
            }else{
                return response()->json([
                    'status' => 'failed',
                    'message' => $appleAuth['message'],
                    'data' => [],
                ],$this->bad_request_status);
            }
    
        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Failed to login user.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Redirect the user to Facebook’s OAuth authorization page.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming HTTP request instance.
     * @return \Illuminate\Http\RedirectResponse  Redirect response to Facebook's OAuth URL.
     */
    public function redirectToFacebook(Request $request)
    {
        // Build the Google redirect URL (stateless method for OAuth)
        $redirectUrl = Socialite::driver('facebook')->stateless()->redirect()->getTargetUrl();

        // Redirect to Google with the generated URL
        return redirect($redirectUrl);
    }

    /**
     * Handle the callback from Facebook after the user attempts to log in via Facebook.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request object containing Facebook's
     * @return \Illuminate\Http\JsonResponse JSON response with the outcome of the Facebook login/registration.
     */
    public function handleFacebookCallback(Request $request)
    {
        try {  
            // Call the service method to handle Facebook callback and authentication
            $facebookAuth = $this->login_service->handleFacebookCallbackUser($request);

            // If the authentication/registration is successful
            if($facebookAuth['success']){
                return response()->json([
                    'status' => 'success',
                    'message' => $facebookAuth['message'],
                    'data' => $facebookAuth['result'],
                ],$this->success_status); 
            }else{
                return response()->json([
                    'status' => 'failed',
                    'message' => $facebookAuth['message'],
                    'data' => [],
                ],$this->bad_request_status); //400 Bad Request status
            }
    
        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Failed to login user.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }


    /**
     * Redirects the user to Google for authentication.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming request object.
     * @return \Illuminate\Http\RedirectResponse  A redirect response to Google for authentication.
     */
    public function redirectToGoogle(Request $request)
    {
        // Build the Google redirect URL (stateless method for OAuth)
        $redirectUrl = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();

        // Redirect to Google with the generated URL
        return redirect($redirectUrl);
    }
 
    /**
     * Handles the Google login callback and manages the authentication or registration process.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming request object containing Google callback data.
     * @return \Illuminate\Http\JsonResponse  A JSON response indicating the success or failure of the login attempt.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {  
            // Call the service method to handle the Google login or registration
            $googleAuth = $this->login_service->handleGoogleCallbackUser($request);

            // If the authentication/registration is successful
            if($googleAuth['success']){
                return response()->json([
                    'status' => 'success',
                    'message' => $googleAuth['message'],
                    'data' => $googleAuth['result'],
                ],$this->success_status); 
            }else{
                return response()->json([
                    'status' => 'failed',
                    'message' => $googleAuth['message'],
                    'data' => [],
                ],$this->bad_request_status); //400 Bad Request status
            }
    
        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Failed to login user.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Handle user login.
     *
     * @param LoginRequest $request The validated login request.
     * @return \Illuminate\Http\JsonResponse JSON response with login status and user data.
     */
    public function login(LoginRequest $request)
    {
        try {  
            $response = $this->login_service->loginUser($request);

            if ($response['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => $response['message'],
                    'data' => $response['result'],
                ], $this->success_status); // 200 OK
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => $response['message'],
                    'data' => [],
                ], $this->bad_request_status); // 400 Bad Request
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to login user.',
                'details' => $e->getMessage(), // Optional debugging information
            ], $this->internal_server_status); // 500 Internal Server Error
        }
    }

    /**
     * Handle user logout.
     *
     * @param Request $request The logout request.
     * @return \Illuminate\Http\JsonResponse JSON response with logout status.
     */
    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user) {
                // Revoke all tokens to log out user from all devices
                $user->tokens()->delete();
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'User logged out successfully.',
                ], $this->success_status); // 200 OK
            }

            return response()->json([
                'status' => 'failed',
                'message' => 'User not authenticated.',
            ], $this->unauthorized_status); // 401 Unauthorized

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to logout.',
                'details' => $e->getMessage(), // Optional debugging information
            ], $this->internal_server_status); // 500 Internal Server Error
        }
    }
}
