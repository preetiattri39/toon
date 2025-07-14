<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;

use App\Models\Order;

class OrderController extends AdminBaseController
{
    /**
     * Display the order listing page.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin.order.index');
    }


    /**
     * Fetch order data for DataTables with filtering, searching, and sorting.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderData(Request $request)
    {
        $country_code = defaultCountryCode();
        $lang_code = defaultLangCode();
        $page_number = defaultPaginateNumber();

        // Reusable filter closure for country and language filtering
        $filter = function ($subquery) use ($country_code, $lang_code) {
            if ($country_code) {
                $subquery->where('country_code', $country_code);
            }

            if ($lang_code) {
                $subquery->where('lang_code', $lang_code);
            }
        };

        $query = Order::query();
        $query->where('status', '!=', 'pending');

        // Apply search filter
        if ($request->has('custom_search') && $request->custom_search) {
            $searchTerm = $request->custom_search;
            $query->where(function ($query) use ($searchTerm) {
                $query->whereHas('user', function ($subquery) use ($searchTerm) {
                    $subquery->where('name', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('order_number', 'like', '%' . $searchTerm . '%')
                ->orWhere('total', 'like', '%' . $searchTerm . '%');
            });
        }

        // Apply status and publish status filters
        if ($request->has('status_filter') && $request->status_filter != '') {
            $query->where('status', $request->status_filter);
        }

        // Handle sorting
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $columns = $request->input('columns');
        $orderColumn = $columns[$orderColumnIndex]['name'];

        $query->orderBy($orderColumn, $orderDirection);

        // Get filtered record count
        $totalFilteredRecords = $query->count();

        // Fetch paginated data with necessary relationships
        $orders = $query->skip($request->start)
            ->take($request->length)
            ->with(['user'])
            ->get();

        // Return response in DataTables format
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => Order::count(),
            'recordsFiltered' => $totalFilteredRecords,
            'data' => $orders,
        ]);
    }


    public function generateOrderPDF(Request $request, $order_id)
    {
        // Get default country code and language code
        $country_code = defaultCountryCode();
        $lang_code = defaultLangCode();

        // Reusable filter closure
        $filter = function($subquery) use ($country_code, $lang_code) {
            if ($country_code) {
                $subquery->where('country_code', $country_code);
            }

            if ($lang_code) {
                $subquery->where('lang_code', $lang_code);
            }
        };

        // Fetch the order with related data
        $order = Order::with([
            'user.roles',
            'shippingAddress',
            'billingAddress',
            'orderItem.product.productTranslation'=> $filter,
        ])->find($order_id); 
    
        if (!$order) {
            return [
                'success' => false,
                'message' => 'No order found for the specified criteria.',
            ];
        }
    
        // Initialize Dompdf with options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);  // Enable HTML5 parser for better HTML support
        $options->set('isPhpEnabled', true); // Allow PHP in HTML (if needed for complex data)
        $dompdf = new Dompdf($options);
    
        // Generate the HTML content from Blade template
        $orderHtml = view('order.template', compact('order'))->render();
    
        // Load HTML into Dompdf
        $dompdf->loadHtml($orderHtml);
    
        // Set paper size (A4, portrait or landscape)
        $dompdf->setPaper('A4', 'portrait');
    
        // Render the PDF
        $dompdf->render();
    
        // Generate the PDF as a string (not saved to the server)
        $pdfOutput = $dompdf->output();
    
        // Return the PDF as a download without saving it to the server
        return response($pdfOutput)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="order_invoice_' . now()->format('Ymd_His') . '.pdf"');
    }

    /**
     * Display the details of a specific order.
     *
     * This method retrieves a single order by its ID using the order service
     * and passes the order data to the 'admin.order.detail' view.
     *
     * @param int $order_id The unique identifier of the order to be retrieved.
     * @return \Illuminate\View\View The view displaying the order details.
     */
    public function viewOrder($order_id)
    {
        // Retrieve the order details using the order service
        $single_order = $this->order_service->getSingleOrder($order_id);
        
        // Extract the order data from the response, defaulting to null if not found
        $order = $single_order['data'] ?? null;

        // Return the admin order detail view with the retrieved order data
        return view('admin.order.detail', compact('order'));
    }


    /**
     * Update the status of an order.
     *
     * This method calls the order service to update the order status based on the provided request data.
     * It returns a JSON response indicating whether the update was successful or not.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing order status update details.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating success or failure.
     */
    public function updateOrderStatus(Request $request)
    {
        // Call the order service to update the order status
        $response = $this->order_service->orderStatus($request);

        // Return a success response if the update was successful
        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Status changed successfully.',
            ]);
        }

        // Return an error response if the update failed
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please check and try again.',
        ]);
    }


}
