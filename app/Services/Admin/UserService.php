<?php 

namespace App\Services\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\User;

class UserService
{
    /**
     * Update the status of a user.
     *
     * This method toggles the user's status between active (1) and inactive (0).
     * It uses the `user_id` from the incoming request to find the user and update their status.
     * 
     * @param \Illuminate\Http\Request $request The incoming request containing the user ID.
     * @return array Response containing success status and a message.
     */
    public function updateStatus($request) {
    try {
        // Retrieve the user ID from the request
        $user_id = $request->user_id;

        // Find the user in the database
        $user = User::find($user_id);

        // If the user doesn't exist, return a failure response
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found.',
            ];
        }

        // Get the current status of the user
        $currentStatus = $user->status;

        // Toggle the status: if 0, change to 1; if 1, change to 0
        $newStatus = ($currentStatus == 1) ? 0 : 1;
        // Update the user's status in the database
        $user->update(['status' => $newStatus]);

        // If the status is being set to 0, revoke all user tokens (logout)
        if ($newStatus == 0) {
            $user->tokens()->delete();
        }

        // Return success response with status change message
        return [
            'success' => true,
            'message' => 'Status changed successfully.',
        ];

    } catch (\Exception $e) {
        // Catch any exceptions and return an error message
        return [
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage(),
        ];
    }
}
}

?>
