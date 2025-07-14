<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\ChangePasswordRequest;
use App\Http\Requests\API\UpdateUserProfileRequest;


class UserProfileController extends APIBaseController
{
    /**
     * Changes the user's password.
     * 
     * This method processes the request to change the user's password. It calls the `changeUserPassword`
     * method from the `user_profile_service` to handle the business logic for password change.
     * Upon success, it returns a structured JSON response with the success status. If an error occurs, 
     * it catches the exception and returns an error response with details about the failure.
     *
     * @param \App\Http\Requests\ChangePasswordRequest $request The request object containing the current and new password details.
     * 
     * @return \Illuminate\Http\JsonResponse JSON response indicating success or failure of the password change process.
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {  
            
            $response = $this->user_profile_service->changeUserPassword($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Retrieves the user's profile information.
     * 
     * This method processes the request to retrieve the current user's profile details. It calls the
     * `getProfile` method from the `user_profile_service` to fetch the user's profile data. Upon success, 
     * it returns a structured JSON response with the user's profile data. If any error occurs during the 
     * process, it catches the exception and returns an error response with the details of the failure.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing any necessary data to fetch the profile.
     * 
     * @return \Illuminate\Http\JsonResponse JSON response containing the user's profile or an error message.
     */
    public function profile(Request $request)
    {
        try {  
            
            $response = $this->user_profile_service->getProfile($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Updates the user's profile information.
     * 
     * This method processes the request to update the user's profile details. It calls the `updateUserProfile`
     * method from the `user_profile_service` to handle the business logic of updating the profile. Upon success, 
     * it returns a structured JSON response confirming the update. If any error occurs during the process, it 
     * catches the exception and returns an error response with the details of the failure.
     *
     * @param \App\Http\Requests\UpdateUserProfileRequest $request The request containing the new profile data for the user.
     * 
     * @return \Illuminate\Http\JsonResponse JSON response indicating the success or failure of the profile update.
     */
    public function updateProfile(UpdateUserProfileRequest $request)
    {
        try {  
            
            $response = $this->user_profile_service->updateUserProfile($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    
}
