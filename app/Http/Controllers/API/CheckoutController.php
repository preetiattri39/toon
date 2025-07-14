<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CheckoutController extends APIBaseController
{
    /**
     * Handle the checkout process for a user.
     *
     * This method processes the checkout request, which includes validating 
     * the provided data, calculating the total, and processing the payment.
     * It then returns a JSON response with the status of the checkout process.
     * If an error occurs, a detailed error message is returned.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing checkout data.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response with the result of the checkout process. 
     *         If successful, it contains the checkout response; otherwise, an error message is returned.
     * 
     */
    public function checkoutProcess(Request $request)
    {
        try {  
            // Process the checkout by calling the checkout service
            $response = $this->checkout_service->checkout($request);

             // Return a successful JSON response with the checkout data
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

}
