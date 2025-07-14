<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends APIBaseController
{
    /**
     * Retrieve a list of categories.
     *
     * @return \Illuminate\Http\JsonResponse The response containing the status, message, and category data or error information.
     */
    public function getCategories(Request $request)
    {
        try {  
            // Call the category service to get the categories list
            $response = $this->category_service->categories($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }
    
}
