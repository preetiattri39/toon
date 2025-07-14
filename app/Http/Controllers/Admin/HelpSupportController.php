<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HelpAndSupport;

class HelpSupportController extends AdminBaseController
{
    public function index()
    {
        return view('admin.ticket.index');
    }

    public function getTicketData(Request $request)
    {
        $query = HelpAndSupport::query();

        // Apply custom search if present
        if ($request->has('custom_search') && $request->custom_search) {
            $searchTerm = $request->custom_search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                ->orWhere('phone_number', 'like', "%{$searchTerm}%")
                ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Apply status filter if present
        if ($request->has('status_filter') && $request->status_filter != '') {
            $status = $request->status_filter;
            $query->where('status', $status);
        }

        // Handle sorting
        $orderColumnIndex = $request->input('order.0.column');  // Get the column index to order by
        $orderDirection = $request->input('order.0.dir');  // Get the order direction (asc/desc)
        $columns = $request->input('columns');  // Columns data (from DataTable)

        // Map column index to actual column name (based on your DataTable definition)
        $orderColumn = $columns[$orderColumnIndex]['name'];

        // Apply sorting to query
        $query->orderBy($orderColumn, $orderDirection);

        // Pagination (server-side)
        $totalRecords = $query->count();
        $users = $query->skip($request->start)
                    ->take($request->length)
                    ->get();

        // Return DataTables format response
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,  // Adjust if filtering is applied
            'data' => $users,
        ]);
    }


    /**
     * Display the details of a specific ticket.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param int $ticket_id The ID of the ticket.
     * @return \Illuminate\View\View The view displaying the ticket details.
     */
    public function ticketDetails(Request $request, $ticket_id)
    {
        $ticket_detail = HelpAndSupport::find($ticket_id);
        return view('admin.ticket.view', compact('ticket_detail'));
    }

  

    /**
     * Mark a ticket as resolved by updating its solution and status.
     *
     * @param \Illuminate\Http\Request $request The request containing ticket resolution details.
     * @return \Illuminate\Http\RedirectResponse A redirect response after resolving the ticket.
     */
    public function resolvedTicket(Request $request)
    {
        $request->validate([
            'solution' => 'required',
        ]);

        $result = $this->support_service->resolvedUserTicket($request);

        if ($result['success']) {
            $request->session()->flash('success', $result['message']);
            return redirect()->route('tickets');
        }

        $request->session()->flash('error', $result['message']);
        return redirect()->back();
    }

    /**
     * Delete a specific ticket.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param int $ticket_id The ID of the ticket to delete.
     * @return \Illuminate\Http\RedirectResponse A redirect response after deleting the ticket.
     */
    public function deleteTicket(Request $request, $ticket_id)
    {
        $ticket = HelpAndSupport::find($ticket_id);

        if (!$ticket) {
            $request->session()->flash('error', 'Ticket not found');
            return redirect()->back();
        }

        $ticket->delete();

        $request->session()->flash('success', 'Ticket deleted successfully');
        return redirect()->route('tickets');
    }
}
