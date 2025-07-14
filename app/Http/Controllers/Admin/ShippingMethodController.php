<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingMethod;

class ShippingMethodController extends AdminBaseController
{
    /**
     * Display the shipping methods index page.
     *
     * This method renders the view that lists all shipping methods. It does not fetch or modify any data.
     * 
     * @return \Illuminate\View\View The view for the shipping methods index page.
     */
    public function index()
    {
        return view('admin.shipping-method.index');
    }

    /**
     * Fetches shipping method data with search, filter, and sorting.
     *
     * This method accepts a request with optional parameters for custom search, status filter, 
     * and sorting. It returns a JSON response in DataTables format containing the filtered and 
     * sorted shipping method data, including pagination information.
     * 
     * @param \Illuminate\Http\Request $request The request object containing filters, search, and sorting information.
     * 
     * @return \Illuminate\Http\JsonResponse JSON response with shipping method data, pagination, and sorting info.
     */
    public function getShippingMethodData(Request $request)
    {
        $query = ShippingMethod::query();

        // Apply custom search if present
        if ($request->has('custom_search') && $request->custom_search) {
            $searchTerm = $request->custom_search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                ->orWhere('price', 'like', "%{$searchTerm}%");
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
     * Display the form to add a new shipping method.
     *
     * This method renders the form view for adding a new shipping method.
     * 
     * @return \Illuminate\View\View The view for adding a shipping method.
     */
    public function addShippingMethodForm()
    {
        return view('admin.shipping-method.add');
    }

    /**
     * Store a new shipping method.
     *
     * This method validates the incoming request and calls the service to store a new shipping method.
     * If successful, it redirects to the shipping method list with a success message.
     * If there is an error, it redirects back with an error message.
     * 
     * @param \Illuminate\Http\Request $request The request object containing shipping method data.
     * 
     * @return \Illuminate\Http\RedirectResponse Redirect response to the shipping method list or back with status messages.
     */
    public function addShippingMethod(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:shipping_methods,name|max:255',
        ]);

        $response = $this->shipping_method_service->storeShippingMethod($request);

        if ($response['success']) {
            $request->session()->flash('success', $response['message']);
            return redirect()->route('shipping-list');
        }

        $request->session()->flash('error', $response['message']);
        return redirect()->back();
    }

    /**
     * Display the details of a shipping method for editing.
     *
     * This method fetches the details of the shipping method identified by the 
     * given ID and displays it in the edit view for modification.
     * 
     * @param int $shipping_method_id The ID of the shipping method to be edited.
     * 
     * @return \Illuminate\View\View The view for editing the shipping method.
     */
    public function shippingMethodDetails($shipping_method_id)
    {
        $shipping_method = ShippingMethod::find($shipping_method_id);
        return view('admin.shipping-method.edit', compact('shipping_method'));
    }

    /**
     * Update an existing shipping method.
     *
     * This method validates the incoming request and updates the shipping method in the database.
     * If successful, it redirects to the shipping method list with a success message.
     * If there is an error, it redirects back with an error message.
     * 
     * @param \Illuminate\Http\Request $request The request object containing updated shipping method data.
     * 
     * @return \Illuminate\Http\RedirectResponse Redirect response to the shipping method list or back with status messages.
     */
    public function updateShippingMethod(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:shipping_methods,name,' . $request->shipping_method_id . '|max:255',
        ]);

        $response = $this->shipping_method_service->updateShipping($request);

        if ($response['success']) {
            $request->session()->flash('success', $response['message']);
            return redirect()->route('shipping-list');
        }

        $request->session()->flash('error', $response['message']);
        return redirect()->back();
    }

    /**
     * Delete a shipping method.
     *
     * This method deletes the shipping method identified by the given ID from the database.
     * If the deletion is successful, it redirects to the shipping method list with a success message.
     * If the shipping method is not found, it returns an error message.
     * 
     * @param \Illuminate\Http\Request $request The request object for the deletion process.
     * @param int $shipping_method_id The ID of the shipping method to be deleted.
     * 
     * @return \Illuminate\Http\RedirectResponse Redirect response with status messages.
     */
    public function shippingMethodDelete(Request $request, $shipping_method_id)
    {
        $shipping_method = ShippingMethod::find($shipping_method_id);

        if (!$shipping_method) {
            $request->session()->flash('error', 'Failed to delete the shipping method. Please try again');
            return redirect()->back();
        }

        $shipping_method->delete();
        $request->session()->flash('success', 'The shipping method was successfully deleted.');
        return redirect()->route('shipping-list');
    }

    /**
     * Update the status of a shipping method.
     *
     * This method toggles the status (active/inactive) of the shipping method based on the provided request.
     * It returns a JSON response indicating success or failure.
     * 
     * @param \Illuminate\Http\Request $request The request object containing the shipping method ID.
     * 
     * @return \Illuminate\Http\JsonResponse JSON response indicating success or failure with a message.
     */
    public function updateShippingMethodStatus(Request $request)
    {
        $response = $this->shipping_method_service->updateStatus($request);

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
}
?>
