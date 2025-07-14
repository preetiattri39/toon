<?php 
namespace App\Services\API;

use Illuminate\Support\Facades\Auth;
use App\Models\ShippingMethod;

class ShippingMethodService
{
    /**
     * Retrieve all active shipping methods.
     * 
     * This method retrieves a list of shipping methods that are currently marked as active 
     * (i.e., `status` is `true`). It orders the results in descending order based on their 
     * ID, and returns a success message with the data if the methods are found, or an error 
     * message if no methods exist.
     *
     * @return array
     */
    public function showShippingMethod()
    {
        try {
            // Fetch active shipping methods ordered by ID in descending order
            $shipping_methods = ShippingMethod::where('status', true)
                ->orderBy('id', 'desc')
                ->get();

            // Check if any shipping methods exist
            if ($shipping_methods->isNotEmpty()) {
                return [
                    'success' => true,
                    'message' => 'Shipping methods retrieved successfully',
                    'data' => $shipping_methods
                ];
            }

            // Return error response if no shipping methods exist
            return [
                'success' => false,
                'message' => 'Shipping methods do not exist',
            ];

        } catch (\Exception $e) {
            // Catch any exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieve a specific shipping method by its ID.
     * 
     * This method accepts a request with a shipping method ID and attempts to retrieve the 
     * corresponding shipping method from the database. If the shipping method exists, it 
     * returns the data along with a success message. If the shipping method does not exist, 
     * an error message is returned.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing the shipping method ID
     * 
     * @return array
     */
    public function specificShippingMethod($request)
    {
        try {
            // Retrieve the shipping method ID from the request
            $shipping_method_id = $request->shipping_method_id;

            // Find the shipping method by its ID
            $shipping_method = ShippingMethod::find($shipping_method_id);

            // Check if the shipping method was found
            if (!$shipping_method) {
                return [
                    'success' => false,
                    'message' => 'Shipping method does not exist',
                ];
            }

            // Return success response with the shipping method data
            return [
                'success' => true,
                'message' => 'Shipping method retrieved successfully',
                'data' => $shipping_method
            ];

        } catch (\Exception $e) {
            // Catch any exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }
}
?>
