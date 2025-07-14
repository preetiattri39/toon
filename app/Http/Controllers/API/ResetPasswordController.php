<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\ResetPasswordRequest;

class ResetPasswordController extends APIBaseController
{
    /**
     * Handles the password reset request.
     *
     * @param ResetPasswordRequest $request The validated request data for resetting the password.
     * @return \Illuminate\Http\JsonResponse JSON response with status and message.
     * 
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {  
            
            // Call the service method to process the reset password request
            $response = $this->reset_password_service->resetUserPasswordWithOTP($request);

            if($response['success']){
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
            // In case of an exception, return a response with error details
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional, for debugging purposes
            ], $this->internal_server_status); // 500 Internal Server Error status
        }
    }
}
