<?php

namespace App\Services\API;

use Illuminate\Support\Facades\Auth;
use App\Models\Notifications;


class NotificationsService
{
    /**
     * Retrieves a list of notifications for the authenticated customer.
     *
     * This method fetches all notifications of type 'admin' for the authenticated user, ordered
     * by notification ID in descending order. It can also handle an optional read status filter (currently commented out).
     * 
     * @param \Illuminate\Http\Request $request The HTTP request instance which may contain filters (e.g., read status).
     *
     * @return array The result of the operation, including success status, message, and data (notifications).
     */
    public function getCustomerNotificationsListing($request)
    {
        try {
            $user = Auth::user();  // Get authenticated user
            $user_id = $user->id;  // Get user ID

            // Create query for fetching notifications
            $query = Notifications::query();

            // Uncomment to add read status filter
            // if (!is_null($read_status)) {
            //     $query->where('read_status', $read_status);
            // }

            // Retrieve notifications of type 'admin' for the authenticated user, ordered by ID descending
            $notifications = $query->where('type', 'admin')
                ->where('user_id', $user_id)
                ->orderBy('id', 'desc')
                ->get();

            return [
                'success' => true,
                'message' => 'Notifications retrieved successfully.',
                'data' => $notifications
            ];

        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieves a single notification for the authenticated customer.
     *
     * This method fetches a specific notification by its ID for the authenticated user. 
     * It ensures that the notification exists and belongs to the user.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance which contains the notification ID.
     *
     * @return array The result of the operation, including success status, message, and the notification data.
     */
    public function singleCustomerNotification($request)
    {
        try {
            $user = Auth::user();  // Get authenticated user
            $user_id = $user->id;  // Get user ID

            $notification_id = $request->notification_id;  // Get notification ID from request

            // Find the notification by ID for the authenticated user
            $notification = Notifications::where('type', 'admin')
                ->where('user_id', $user_id)
                ->find($notification_id);

            // If the notification does not exist, return an error message
            if (!$notification) {
                return [
                    'success' => false,
                    'message' => 'Notification not exist.',
                ];
            }

            return [
                'success' => true,
                'message' => 'Notification retrieved successfully.',
                'data' => $notification
            ];

        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieves the most recent notifications for the authenticated customer.
     *
     * This method fetches the latest notifications of type 'admin' for the authenticated user,
     * ordered by notification ID in descending order. It can also handle an optional read status filter (currently commented out).
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance.
     *
     * @return array The result of the operation, including success status, message, and data (recent notifications).
     */
    public function recentCustomerNotifications($request)
    {
        try {
            $user = Auth::user();  // Get authenticated user
            $user_id = $user->id;  // Get user ID

            // Retrieve the most recent notifications of type 'admin' for the user, ordered by ID descending
            $latest_notifications = Notifications::where('type', 'admin')
                ->where('user_id', $user_id)
                ->orderBy('id', 'desc')
                ->limit(5) 
                ->get();

            return [
                'success' => true,
                'message' => 'Notifications retrieved successfully.',
                'data' => $latest_notifications
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
