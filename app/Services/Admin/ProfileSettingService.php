<?php 

namespace App\Services\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\Admin;

class ProfileSettingService
{
    /**
     * Update the profile information of the currently authenticated admin user.
     *
     * @param \Illuminate\Http\Request $request The request object containing the updated profile data.
     * @return array An array containing the success status and a message.
     */
    public function updateAdminProfile($request)
    {
        try {
            // Get the authenticated admin user
            $admin_user = Auth::guard('admin')->user();
            $admin_id = $admin_user->id;

            // Find the admin record in the database
            $admin = Admin::find($admin_id);

            // Return an error if the admin does not exist
            if (!$admin) {
                return [
                    'success' => false,
                    'message' => 'User not exist.',
                ];
            }

            // Prepare the updated data
            $admin_user_data = [
                'name' => $request->name,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
            ];

            if ($request->hasFile('profile_pics')) {

                // Remove existing profile picture if it exists
                if ($admin->profile_pic) {
                     // Check if the brand has a logo and unlink it
                    $profilepath = public_path('storage/profile/' . $admin->profile_pic);
                    if ($admin->profile_pic && file_exists($profilepath)) {
                        unlink($profilepath);
                    }
                }
                // Save new profile picture
                $file_name = uniqid() . "." . $request->file('profile_pics')->getClientOriginalExtension();
                $request->file('profile_pics')->storeAs('profile', $file_name);
               
                $admin_user_data['profile_pic'] = $file_name;
            }

            // Update the admin profile
            $admin->update($admin_user_data);

            // Return a success message
            return [
                'success' => true,
                'message' => 'Profile updated successfully.',
            ];

        } catch (\Exception $e) {
            // Handle unexpected exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }  

    /**
     * Change the password of the currently authenticated admin user.
     *
     * @param \Illuminate\Http\Request $request The request object containing the current and new password data.
     * @return array An array containing the success status and a message.
     */
    public function changeAdminPassword($request)
    {
        try {
            // Get the authenticated admin user
            $admin_user = Auth::guard('admin')->user();
            $admin_id = $admin_user->id;

            // Find the admin record in the database
            $admin = Admin::find($admin_id);

            // Return an error if the admin does not exist
            if (!$admin) {
                return [
                    'success' => false,
                    'message' => 'User not exist.',
                ];
            }

            // Check if the current password matches the existing password
            if (!Hash::check($request->current_password, $admin->password)) {
                return [
                    'success' => false,
                    'message' => 'Current password is incorrect.',
                ];
            }

            // Check if the current password is the same as the new update password
            if (Hash::check($request->password, $admin->password)) {
                return [
                    'success' => false,
                    'message' => 'Current password and new password cannot be the same. Please enter a different password for the update.',
                ];
            }

            // Update the admin's password
            $admin->update([
                'password' => Hash::make($request->password),
            ]);

            // Return a success message
            return [
                'success' => true,
                'message' => 'Password changed successfully.',
            ];

        } catch (\Exception $e) {
            // Handle unexpected exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

}
?>
