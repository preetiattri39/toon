<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FAQ;

class FAQController extends AdminBaseController
{
    /**
     * Display the FAQ listing page.
     *
     * This method loads the main FAQ listing view for the admin panel.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.faqs.index');
    }
    
    /**
     * Display the add FAQ page.
     *
     * This method loads the page where administrators can add a new FAQ.
     * 
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('admin.faqs.add');
    }

    /**
     * Get the list of FAQ data with optional search, status filter, and sorting.
     *
     * This method handles the server-side logic for returning FAQ data in a paginated format,
     * including search and filtering functionality. The data is returned in the format needed
     * for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request containing the search, filter, and sorting parameters.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the filtered, sorted, and paginated FAQ data.
     */
    public function faqData(Request $request)
    {
        $query = FAQ::query();

        // Apply custom search if present
        if ($request->has('custom_search') && $request->custom_search) {
            $searchTerm = $request->custom_search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('question', 'like', "%{$searchTerm}%")
                ->orWhere('answer', 'like', "%{$searchTerm}%");
            });
        }

        // Apply status filter if present
        if ($request->has('status_filter') && $request->status_filter != '') {
            $status = $request->status_filter;
            $query->where('is_active', $status);
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
     * Display the edit FAQ page.
     *
     * This method loads the edit page for an FAQ, pre-filled with the existing FAQ data
     * for the specified FAQ ID.
     * 
     * @param int $faq_id The ID of the FAQ to edit.
     * 
     * @return \Illuminate\View\View
     */
    public function edit($faq_id)
    {
        $faq = FAQ::find($faq_id);
        return view('admin.faqs.edit', compact('faq'));
    }

    /**
     * Add a new FAQ.
     *
     * This method handles the request to add a new FAQ, validates the input data,
     * and calls the service method to store the FAQ. The result is then returned as a response.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request containing the FAQ data.
     * 
     * @return \Illuminate\Http\RedirectResponse Redirects back to the FAQ list with success or error message.
     */
    public function addFaqs(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        $result = $this->faq_service->storeFaqs($request);

        if ($result['success']) {
            $request->session()->flash('success', $result['message']);
            return redirect()->route('faq-list');
        }

        $request->session()->flash('error', $result['message']);
        return redirect()->back();
    }

    /**
     * Update an existing FAQ.
     *
     * This method handles the request to update an existing FAQ, validates the input data,
     * and calls the service method to update the FAQ. The result is then returned as a response.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request containing the FAQ data.
     * 
     * @return \Illuminate\Http\RedirectResponse Redirects back to the FAQ list with success or error message.
     */
    public function updateFaqs(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        $result = $this->faq_service->updateFaqsData($request);

        if ($result['success']) {
            $request->session()->flash('success', $result['message']);
            return redirect()->route('faq-list');
        }

        $request->session()->flash('error', $result['message']);
        return redirect()->back();
    }

    /**
     * Delete an FAQ.
     *
     * This method deletes an FAQ from the database based on the given FAQ ID. If the FAQ exists,
     * it is removed; otherwise, a message is shown indicating that the FAQ doesn't exist.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request containing the FAQ ID.
     * 
     * @return \Illuminate\Http\RedirectResponse Redirects back to the FAQ list with success or error message.
     */
    public function deleteFaq(Request $request)
    {
        $faq_id = $request->faq_id;
        $faq = FAQ::find($faq_id);

        if (!$faq) {
            $request->session()->flash('success', 'Faq does not exist');
            return redirect()->back();
        }

        $faq->delete();
        $request->session()->flash('success', 'Faq deleted successfully');
        return redirect()->route('faq-list');
    }

    /**
     * Update the status of an FAQ (active/inactive).
     *
     * This method toggles the active status of an FAQ based on its current state.
     * It returns a JSON response indicating whether the status change was successful.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request containing the FAQ ID.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the success status and message.
     */
    public function updateFQAStatus(Request $request)
    {
        $response = $this->faq_service->updateFQAStatus($request);

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
