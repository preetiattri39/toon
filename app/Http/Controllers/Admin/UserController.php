<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use App\Models\User;

class UserController extends AdminBaseController
{
    /**
     * Display the list of users.
     *
     * This method renders the view where the list of users will be displayed.
     * It doesn't handle any data fetching, but just returns the initial view.
     *
     * @param \Illuminate\Http\Request $request The request instance.
     * @return \Illuminate\View\View The view to display the list of users.
     */
    public function index(Request $request)
    {
        return view('admin.user.index');
    }

    /**
     * Fetch the user data for DataTable.
     *
     * This method retrieves user data based on custom search, status filter, 
     * and sorting parameters from the DataTable request. It applies filtering, 
     * sorting, and pagination (server-side) to the query and returns the result
     * in a format that DataTable expects.
     *
     * @param \Illuminate\Http\Request $request The request object containing filtering, sorting, and pagination parameters.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the user data for DataTable.
     */
    public function getUserData(Request $request)
    {
        $query = User::query();

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
     * Update the status of a user.
     *
     * This method updates the status of the user based on the request data.
     * It interacts with the user service to update the user's status in the database.
     * The result is returned as a JSON response indicating success or failure.
     *
     * @param \Illuminate\Http\Request $request The request object containing the user's status update details.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating success or failure of the status update.
     */
    public function updateUserStatus(Request $request)
    {
        // Call the user service to update the user's status
        $response = $this->user_service->updateStatus($request);
       
        // Handle the response from the service
        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Status changed successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please check and try again.',
        ]);
    }

    /**
     * Display the details of a specific user.
     *
     * This method fetches the user details by the user ID and renders the 
     * view displaying detailed information about the user.
     *
     * @param int $user_id The ID of the user whose details are to be fetched.
     * @return \Illuminate\View\View The view displaying the user's details.
     */
    public function userDetails($user_id)
    {
        // Fetch the user details based on the given user ID
        $user = User::find($user_id);

        // Return the view with the user details
        return view('admin.user.details', compact('user'));
    }
}
