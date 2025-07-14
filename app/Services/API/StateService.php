<?php 

namespace App\Services\API;

use App\Models\CountryState;

class StateService
{

    /**
     * Retrieves the list of states for a given country.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return array  Returns an array with success status, message, and data.
     */
    public function getCountryStateListing($request)
    {
        try{
            // Extracting country_id from the request
            $country_id = $request->country_id;

            // Initialize the query builder for the CountryStates model
            $query = CountryState::query();

            // Check if a country_id is provided in the request
            if($country_id){
                // Add a condition to filter states by the given country_id
                $query->where('country_id', $country_id);
            }

            // Execute the query and fetch the states
            $states = $query->get();

            return [
                'success' => true,
                'message' => 'Country State retrieved successfully.', // Success message
                'data' => $states // Return the list of states
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
