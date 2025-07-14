<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerOrderController extends APIBaseController
{
    /**
     * Retrieve a list of orders for the authenticated user.
     *
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Http\JsonResponse JSON response containing order details or an error message.
     */
    public function orderDetails(Request $request)
    {
        try {  
            // Fetch order details from the CustomerOrdersService
            $response = $this->customer_orders_service->getOrderDetails($request);

            // Return a formatted JSON response
            return $this->response_helper::jsonListingResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional: Used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Retrieve details of a specific order for the authenticated user.
     *
     * @param Request $request The HTTP request object containing the order ID.
     * @return \Illuminate\Http\JsonResponse JSON response containing the order details or an error message.
     */
    public function singleOrder(Request $request)
    {
        try {  
            // Fetch a single order's details from the CustomerOrdersService
            $response = $this->customer_orders_service->getSingleOrder($request);

            // Return a formatted JSON response
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional: Used for debugging purposes
            ], $this->internal_server_status);
        }
    }
}
