<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends APIBaseController
{
    /**
     * Retrieves a list of notifications for a customer.
     *
     * This method calls the `getCustomerNotificationsListing` service to fetch the list of
     * notifications for a given customer based on the request data. If successful, it returns
     * the notifications in a JSON response. In case of an error, an error message is returned.
     *
     * @param Request $request The HTTP request instance, which may contain query parameters.
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing the list of notifications
     *         or an error message.
     */
    public function getCustomerNotifications(Request $request)
    {
        try {
            $response = $this->notifications_service->getCustomerNotificationsListing($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Retrieves a single notification for a customer.
     *
     * This method calls the `singleCustomerNotification` service to fetch a specific notification
     * for a given customer based on the request data. If successful, it returns the notification 
     * in a JSON response. In case of an error, an error message is returned.
     *
     * @param Request $request The HTTP request instance, which contains the customer identifier
     *                         and the notification identifier.
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing the single notification 
     *         or an error message.
     */
    public function getSingleCustomerNotification(Request $request)
    {
        try {
            $response = $this->notifications_service->singleCustomerNotification($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Retrieves the most recent notifications for a customer.
     *
     * This method calls the `recentCustomerNotifications` service to fetch the recent notifications
     * for a given customer based on the request data. If successful, it returns the recent notifications 
     * in a JSON response. In case of an error, an error message is returned.
     *
     * @param Request $request The HTTP request instance, which may contain pagination or filtering data.
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing the recent notifications 
     *         or an error message.
     */
    public function getCustomerRecentNotifications(Request $request)
    {
        try {
            $response = $this->notifications_service->recentCustomerNotifications($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }
}
