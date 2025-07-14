<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends APIBaseController
{
    
    /**
     * Handles the forgot password process for the user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        try {  
            
            // Call the service method to process the forgot password request
            $response = $this->forgot_password_service->forgotUserPasswordWithOTP($request);

            if($response['success']){
                return response()->json([
                    'status' => 'success',
                    'message' => $response['message'],
                    'data' => $response['result'],
                ],$this->success_status);
            }else{
                return response()->json([
                    'status' => 'failed',
                    'message' => $response['message'],
                    'data' => [],
                ],$this->bad_request_status); //400 Bad Request status
            }
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }

    }

}
