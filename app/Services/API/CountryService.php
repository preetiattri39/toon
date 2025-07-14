<?php 

namespace App\Services\API;

use App\Models\Country;

class CountryService
{
    /**
     * Retrieves a list of all countries.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return array  Returns a structured array with success status, message, and data.
     */
    public function getCountryListing($request)
    {
        try{
            // Fetch all countries from the 'countries' table
            $country = Country::all();

            // Check if the countries collection is not empty
            if($country->isNotEmpty()){
                // Return a success response with the list of countries
                return [
                    'success' => true,
                    'message' => 'Countries retrieved successfully.',
                    'data' => $country 
                ];

            }

            // If no countries were found, return a failure response
            return [
                'success' => false, 
                'message' => 'Country not exist.'
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
