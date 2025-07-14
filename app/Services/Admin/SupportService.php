<?php 

namespace App\Services\Admin;

use Illuminate\Support\Facades\Auth;
use App\Models\HelpAndSupport;

// Notification events
use App\Events\SolvedTicketNotification;

// Models
use App\Models\User;

class SupportService
{
    /**
     * Resolves a user support ticket by marking it as closed and saving the provided solution.
     *
     * @param \Illuminate\Http\Request $request The request object containing ticket ID and solution.
     * @return array An array with success status and message.
     */
    public function resolvedUserTicket($request)
    {
        try {
            $admin = Auth::guard('admin')->user();

            $ticket_id = $request->ticket_id;
            $solution = $request->solution;

            // Find the support ticket by ID
            $support_ticket = HelpAndSupport::find($ticket_id);

            // If the ticket does not exist, return an error response
            if (!$support_ticket) {
                return [
                    'success' => false,
                    'message' => 'Ticket not found.',
                ];
            }

            // Update the ticket with the solution and mark it as closed
            $support_ticket->update([
                'admin_id' => $admin->id,
                'solutions' => $solution,
                'status' => 'closed'
            ]);

            $user = User::find($support_ticket->user_id);

            event(new SolvedTicketNotification([
                'title' => 'Solved Ticket',
                'notification_type' => 'solved_ticket',
                'type' => 'admin',
                'message' => 'A ticket has been solved by '.$admin->name.'.',
                'user_id' => $user->id,
                'admin_id' => $admin->id,
                'name' => $user->name,
                'email' => $user->email,
            ]));  

            return [
                'success' => true,
                'message' => 'Ticket resolved successfully.',
            ];

        } catch (\Exception $e) {
            // Handle any exceptions and return the error message
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

}
?>
