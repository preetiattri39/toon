<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FaqController extends APIBaseController
{
    /**
     * Retrieve a list of active FAQs via the API.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request instance.
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing the list of FAQs or an error message.
     */
    public function getFaqs(Request $request)
    {
        try {  
            // Call the service method to fetch FAQs
            $response = $this->faq_service->faqData($request);

            // Return a formatted JSON response using the response helper
            return $this->response_helper::jsonListingResponse(
                $response,
                $this->success_status,
                $this->not_found_status
            );

        } catch (\Exception $e) {
            // Handle unexpected exceptions and return an error response
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional: Comment this out in production
            ], $this->internal_server_status);
        }
    }   
}
