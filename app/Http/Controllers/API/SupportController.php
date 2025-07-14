<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupportController extends APIBaseController
{
    /**
     * Retrieve all support tickets for the authenticated user.
     *
     * This method calls the `getUserTickets` service method to fetch all tickets 
     * associated with the currently authenticated user. It returns a JSON response 
     * with either the tickets or a failure message.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object.
     * @return \Illuminate\Http\JsonResponse JSON response containing the user tickets or an error message.
     */
    public function userTickets(Request $request)
    {
        try { 

            $response = $this->support_service->getUserTickets($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

     /**
     * Submit a new support ticket for the authenticated user.
     *
     * This method calls the `sendUserTicket` service method to submit a new ticket 
     * with the user's provided details (e.g., name, email, message). It returns 
     * a JSON response with either the newly created ticket or an error message.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object containing ticket data.
     * @return \Illuminate\Http\JsonResponse JSON response indicating the success or failure of ticket submission.
     */
    public function sentTicket(Request $request)
    {
        try { 

            $response = $this->support_service->sendUserTicket($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

     /**
     * Retrieve a specific support ticket by its ID.
     *
     * This method calls the `singleTicket` service method to retrieve the details of a 
     * specific ticket using the ticket ID provided in the request. It returns a JSON 
     * response with either the ticket data or an error message.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object containing the ticket ID.
     * @return \Illuminate\Http\JsonResponse JSON response containing the ticket data or an error message.
     */
    public function getSingleTicket(Request $request)
    {
        try { 

            $response = $this->support_service->singleTicket($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }
    

}
