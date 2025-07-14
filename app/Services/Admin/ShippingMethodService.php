<?php 

namespace App\Services\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\ShippingMethod;

class ShippingMethodService
{
    /**
     * Store a new shipping method.
     *
     * This method accepts a request containing shipping method details, creates a new shipping
     * method in the database, and returns a success message with the created shipping method data.
     * If an exception occurs, it catches the error and returns a failure message.
     *
     * @param \Illuminate\Http\Request $request The request object containing shipping method data.
     * 
     * @return array The response indicating success or failure with relevant messages and data.
     */
    public function storeShippingMethod($request)
    {
        try {
            $shipping_method = ShippingMethod::create([
                'name' => $request->name,
                'price' => $request->price
            ]);

            return [
                'success' => true,
                'message' => 'Shipping method store successfully',
                'data' => $shipping_method
            ];

        } catch (\Exception $e) {
            // Handle unexpected exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update an existing shipping method.
     *
     * This method accepts a request containing the shipping method ID and updated details.
     * It updates the existing shipping method record in the database and returns the updated
     * data along with a success message. If the shipping method is not found, it returns a failure
     * message. Any exceptions are caught and a failure message is returned.
     *
     * @param \Illuminate\Http\Request $request The request object containing the shipping method ID and updated data.
     * 
     * @return array The response indicating success or failure with relevant messages and data.
     */
    public function updateShipping($request)
    {
        try {
            $shipping_method = ShippingMethod::find($request->shipping_method_id);

            if (!$shipping_method) {
                return [
                    'success' => false,
                    'message' => 'Shipping method not exists',
                ];                
            }

            $shipping_method->update([
                'name' => $request->name,
                'price' => $request->price
            ]);

            return [
                'success' => true,
                'message' => 'Shipping method updated successfully',
                'data' => $shipping_method
            ];

        } catch (\Exception $e) {
            // Handle unexpected exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update the status of a shipping method.
     *
     * This method toggles the status (active/inactive) of a shipping method. It checks if the shipping
     * method exists based on the provided shipping ID and updates its status accordingly. A success 
     * message is returned if the status is updated successfully, otherwise, an error message is returned.
     * 
     * @param \Illuminate\Http\Request $request The request object containing the shipping method ID.
     * 
     * @return array The response indicating success or failure with relevant messages.
     */
    public function updateStatus($request)
    {
        try {
            $shipping = ShippingMethod::find($request->shipping_id);

            if (!$shipping) {
                return [
                    'success' => false,
                    'message' => 'Shipping method not found.',
                ];
            }

            $status = ($shipping->status == true) ? 0 : 1;
            $shipping->update(['status' => $status]);

            return [
                'success' => true,
                'message' => 'Status changed successfully.',
            ];

        } catch (\Exception $e) {
            // Handle unexpected exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }
}
?>
