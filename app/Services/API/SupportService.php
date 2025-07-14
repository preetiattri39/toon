<?php 
namespace App\Services\API;
use Illuminate\Support\Facades\Auth;

// Notification events
use App\Events\CustomerTicketSentNotification;

// Models
use App\Models\User;
use App\Models\HelpAndSupport;

class SupportService
{
    /**
     * Retrieve all support tickets associated with the authenticated user.
     *
     * This method fetches all the tickets submitted by the currently authenticated user. 
     * If tickets exist, it returns them along with a success message. If no tickets are 
     * found, it returns a failure message.
     *
     * @return array The response indicating success or failure and the tickets (if any).
     */
    public function getUserTickets()
    {
        try {
            // Fetch the currently authenticated user
            $user = Auth::user();

            // Get user ID, or set to null if not available
            $user_id = $user->id ?? null;

            // Retrieve all tickets associated with the user
            $get_tickets = HelpAndSupport::where('user_id', $user_id)
            ->orderBy('id','desc')
            ->get();

            // Check if tickets exist and return appropriate response
            if ($get_tickets->isNotEmpty()) {
                return [
                    'success' => true,
                    'message' => 'User tickets retrieved successfully',
                    'data' => $get_tickets
                ];
            }

            return [
                'success' => false,
                'message' => 'No tickets found for this user',
            ];

        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create and send a new support ticket for the authenticated user.
     *
     * This method allows the authenticated user to submit a new support ticket with 
     * details such as name, email, phone number, and message. If the user is not 
     * authenticated, it returns an error response. If the ticket is successfully 
     * created, it returns a success message along with the ticket data.
     *
     * @param \Illuminate\Http\Request $request The request containing the ticket details.
     * @return array The response indicating success or failure and the created ticket.
     */
    public function sendUserTicket($request)
    {
        try {

            // Fetch the currently authenticated user
            $user = Auth::user();

            // If user is not authenticated, return an error message
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not authenticated.',
                ];
            }

            // Prepare ticket data
            $data = [
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'phone_country_code' => $request->phone_country_code,
                'phone_code' => $request->phone_code,
                'message' => $request->message,
            ];

            // Create a new help and support ticket
            $store_tickets = HelpAndSupport::create($data);

            event(new CustomerTicketSentNotification([
                'title' => 'New Ticket',
                'notification_type' => 'ticket_sent',
                'type' => 'customer',
                'message' => 'A new ticket sent by '.$user->name.'.',
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]));  

            return [
                'success' => true,
                'message' => 'Ticket sent successfully.',
                'data' => $store_tickets
            ];

        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieve a specific support ticket by its ID.
     *
     * This method retrieves the details of a specific support ticket using the 
     * ticket ID provided in the request. If the user is not authenticated, an 
     * error message is returned. If the ticket is found, the ticket data is returned 
     * with a success message.
     *
     * @param \Illuminate\Http\Request $request The request containing the ticket ID.
     * @return array The response indicating success or failure and the ticket data (if found).
     */
    public function singleTicket($request)
    {
        try {

            // Fetch the currently authenticated user
            $user = Auth::user();

            // If user is not authenticated, return an error message
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not authenticated.',
                ];
            }

            // Get the ticket ID from the request
            $ticket_id = $request->ticket_id;

            // Find the ticket by ID
            $get_ticket = HelpAndSupport::find($ticket_id);

            return [
                'success' => true,
                'message' => 'Ticket retrieved successfully.',
                'data' => $get_ticket
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
?>
