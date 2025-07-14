<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\RegisterRequest;
use App\Http\Requests\API\UserVerificationRequest;

class RegisterController extends APIBaseController
{
    /**
     * Handles user registration.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {  
            $response = $this->register_service->registerUser($request);

            if ($response['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => $response['message'],
                    'data' => $response['result'],
                ], $this->created_status); // 201 Created status
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => $response['message'],
                    'data' => [],
                ], $this->bad_request_status); // 400 Bad Request status
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to register user.',
                'details' => $e->getMessage(), // Optional for debugging
            ], $this->internal_server_status);
        }
    }
    
    /**
     * Verifies the OTP for user registration.
     *
     * @param UserVerificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userVerifyOtp(UserVerificationRequest $request)
    {
        try {  
            $response = $this->register_service->verifyOtp($request);

            if ($response['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => $response['message'],
                    'data' => $response['result'],
                ], $this->success_status);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => $response['message'],
                    'data' => [],
                ], $this->bad_request_status); // 400 Bad Request status
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to verify OTP.',
                'details' => $e->getMessage(), // Optional for debugging
            ], $this->internal_server_status);
        }
    }

    /**
     * Resends a new OTP to the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendUserOtp(Request $request)
    {
        try {  
            $response = $this->register_service->resendOtp($request);

            if ($response['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => $response['message'],
                    'data' => $response['result'],
                ], $this->success_status);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => $response['message'],
                    'data' => [],
                ], $this->bad_request_status); // 400 Bad Request status
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to resend OTP.',
                'details' => $e->getMessage(), // Optional for debugging
            ], $this->internal_server_status);
        }
    }
}
