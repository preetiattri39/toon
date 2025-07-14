<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends APIBaseController
{
    /**
     * Get the current pending orders.
     * 
     * This method handles the request to fetch all orders that are currently pending. 
     * It calls the `getCurrentPendingOrders` method of the `order_service` to retrieve 
     * the necessary order data. In case of a successful response, it returns the 
     * data in JSON format with a success status. In case of an error, it returns 
     * a detailed error message for debugging purposes.
     * 
     * @param \Illuminate\Http\Request $request The request instance containing 
     * necessary parameters to retrieve the pending orders.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response with the result of the 
     * pending orders retrieval or an error message if something goes wrong.
     */
    public function currentPendingOrders(Request $request)
    {
        try {  
            // Fetch the current pending orders using the order service
            $response = $this->order_service->getCurrentPendingOrders($request);

            // Return the response with a success status
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Place an order for a user.
     *
     * This method is responsible for processing the placement of an order for a user. It interacts with the `order_service`
     * to initiate the order placement process. On success, the response from the service is returned with a success status.
     * In case of an exception or error, a JSON response with an error message and exception details is returned.
     *
     * @param \Illuminate\Http\Request $request The request instance containing the data required to place the order.
     *                                        This typically includes order details like product IDs, quantities, 
     *                                        shipping information, etc.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the outcome of the order placement.
     *                                      If the operation is successful, it returns a success message with the appropriate data.
     *                                      If there's an error, it returns an error message with exception details.
     */
    public function addShippingAddress(Request $request)
    {
        try {  
            // Attempt to place the user order by passing the request to the order service
            $response = $this->order_service->addNewShippingAdddress($request);

            // Return a JSON response indicating a successful operation
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }


    public function getShippingAddress(Request $request)
    {
        try {  
            // Attempt to place the user order by passing the request to the order service
            $response = $this->order_service->getShippingAddress($request);

            // Return a JSON response indicating a successful operation
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Confirm the payment for an order.
     *
     * This method is responsible for confirming the payment of an order
     * by calling the `confirmPayment` method from the order service.
     * It handles the response and returns a JSON response indicating
     * whether the payment confirmation was successful or failed.
     * If an exception occurs during the process, it will catch it and return
     * a generic error message with the exception details.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing payment confirmation details.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the payment confirmation.
     */
    public function confirmOrderPayment(Request $request)
    {
        try {  
            // Call the order service to confirm the payment
            $response = $this->order_service->confirmPayment($request);
            
            // Return the response in JSON format
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
   
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    
    /**
     * Get the available shipping methods for an order.
     *
     * This method is responsible for fetching the shipping methods
     * available for an order by calling the `getShippingMethod` 
     * method from the order service. It returns a JSON response 
     * indicating whether the operation was successful or if there were 
     * any issues. In case of an exception, a generic error message 
     * with exception details is returned for debugging purposes.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing order details for shipping.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the shipping methods or an error message.
     */
    public function shippingMethod(Request $request)
    {
        try {  
            // Call the order service to get available shipping methods
            $response = $this->order_service->getShippingMethod($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }


    
}
