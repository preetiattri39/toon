<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShippingMethodController extends APIBaseController
{
    /**
     * Retrieve a list of all active shipping methods.
     *
     * @param \Illuminate\Http\Request $request The incoming request that may contain additional parameters (e.g., filters).
     * 
     * @return \Illuminate\Http\JsonResponse The JSON response with shipping method data or error message.
     */
    public function getShippingMethod(Request $request)
    {
        try {  
            // Call the service method to get the active shipping methods, passing the request data
            $response = $this->shipping_method_service->showShippingMethod($request);

            // Return a standardized JSON response with success status
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            // Return error response in case of an exception, with error message and optional details for debugging
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional: useful for debugging during development
            ], $this->internal_server_status);
        }
    }

    /**
     * Retrieve a specific shipping method by its ID.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing the shipping method ID.
     * 
     * @return \Illuminate\Http\JsonResponse The JSON response with the specific shipping method data or error message.
     */
    public function getSingleShippingMethod(Request $request)
    {
        try {  
            // Call the service method to get the specific shipping method, passing the request data
            $response = $this->shipping_method_service->specificShippingMethod($request);

            // Return a standardized JSON response with success status
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            // Return error response in case of an exception, with error message and optional details for debugging
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional: useful for debugging during development
            ], $this->internal_server_status);
        }
    }
}
