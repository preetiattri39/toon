<?php 

namespace App\Services\API;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Auth;

// Models
use App\Models\User;

class UserProfileService
{
    /**
     * Change the authenticated user's password.
     *
     * This method handles the logic for changing the user's password, including verifying the
     * current password and updating it to a new one.
     *
     * @param \Illuminate\Http\Request $request The request containing the current and new password.
     * 
     * @return array A response indicating the success or failure of the operation, along with the relevant message.
     */
    public function changeUserPassword($request)
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Check if the user is authenticated
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not authenticated.',
            ];
        }

        // Check if the current password provided matches the stored password
        if (!Hash::check($request->current_password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Current password is incorrect.',
            ];
        }

        // Check if the current password is the same as the new update password
        if (Hash::check($request->password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Current password and new password cannot be the same. Please enter a different password for the update.',
            ];
        }

        // Update the user's password with the new one
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Return success response with the updated user data
        return [
            'success' => true,
            'message' => 'Password changed successfully.',
            'data' => $user
        ];
    }

    /**
     * Get the authenticated user's profile information.
     *
     * This method retrieves the profile data for the currently authenticated user, including
     * their roles and profile picture (if available).
     *
     * @return array A response containing the user's profile data or an error message.
     */
    public function getProfile()
    {
        // Fetch the currently authenticated user
        $user = Auth::user();

        $user_id = $user->id ?? null;

        // Retrieve the user's profile along with their roles
        $user = User::with(['roles:id,name'])->find($user_id);

        // Remove pivot data for roles (if exists)
        if ($user && isset($user->roles)) {
            foreach ($user->roles as $role) {
                unset($role->pivot);
            }
        }
        
        // Handle profile picture and ensure it's publicly accessible
        $user_profile = $user->profile_pic;
        if ($user_profile) {
            $user->profile_pic = asset('storage/profile/' . $user_profile);
        }

        if ($user) {
            return [
                'success' => true,
                'message' => 'User profile retrieved successfully',
                'data' => $user
            ];
        }

        return [
            'success' => false,
            'message' => 'User not found',
        ];
    }

    /**
     * Update the authenticated user's profile information.
     *
     * This method allows the user to update their basic profile information, such as name,
     * phone number, gender, and profile picture.
     *
     * @param \Illuminate\Http\Request $request The request containing the new profile data.
     * 
     * @return array A response indicating the success or failure of the update operation.
     */
    public function updateUserProfile($request)
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Check if the user is authenticated
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not authenticated.',
            ];
        }

        // Update basic user details
        $user->update([
            'name' => $request->full_name,
            'phone_code' => $request->phone_code,
            'phone_number' => $request->phone_number,
            'country_code' => $request->country_code,
            'gender' => $request->gender,
        ]);
      
        // Handle profile picture update if provided
        if ($request->hasFile('profile_pics')) {

            // Remove existing profile picture if it exists
            if ($user->profile_pic) {
                $profilePath = public_path('storage/profile/' . $user->profile_pic);
                if (file_exists($profilePath)) {
                    unlink($profilePath);
                }
            }

            // Save the new profile picture
            $fileName = uniqid() . "." . $request->file('profile_pics')->getClientOriginalExtension();
            $request->file('profile_pics')->storeAs('profile', $fileName);
            $user->update([
                'profile_pic' => $fileName,
            ]);
        }

        // If user has a profile picture, generate the full asset URL
        if ($user->profile_pic) {
            $user->profile_pic = asset('/storage/profile/' . $user->profile_pic);
        }

        return [
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => $user
        ];
    }
}
?>
