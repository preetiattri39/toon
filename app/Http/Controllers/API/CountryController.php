<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CountryController extends APIBaseController
{
    /**
     * Retrieves a list of countries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse  Returns a JSON response with the country data or error message.
     */
    public function getCountries(Request $request)
    {
        try {  
            // Call the service method to get the country listing, passing the request data
            $response = $this->country_service->getCountryListing($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }
}
