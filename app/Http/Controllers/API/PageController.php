<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends APIBaseController
{
    /**
     * Retrieve the details of a specific page.
     *
     * @param Request $request The request object that may contain parameters such as the page ID or other details.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the page details or an error message.
     *
     * @throws \Exception If an error occurs during the retrieval of page details, an exception is thrown 
     * and an error response is returned to the client.
     */
    public function pageDetails(Request $request)
    {
        try {  
            // Call to the service layer to get the page details based on the request parameters
            $response = $this->pages_service->getPageDetails($request);

            // Return a successful JSON response with the page details
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }
}
