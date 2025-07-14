<?php 

namespace App\Services\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Events\OrderStatusChangeNotification;
use Dompdf\Dompdf;
use Dompdf\Options;

// Models
use App\Models\Order;
use App\Models\User;

class OrderService
{
    /**
     * Retrieve a single order along with its related data.
     *
     * This method fetches an order by its ID and includes related data such as user roles,
     * shipping and billing addresses, and product translations. It also applies country
     * and language filters if available. If the order is not found, it returns an error response.
     *
     * @param int $order_id The unique identifier of the order to retrieve.
     * @return array An associative array containing success status, message, and order data if found.
     */
    public function getSingleOrder($order_id)
    {
        try {
            // Get default country code and language code
            $country_code = defaultCountryCode();
            $lang_code = defaultLangCode();

            // Reusable filter closure for country and language filtering
            $filter = function($subquery) use ($country_code, $lang_code) {
                if ($country_code) {
                    $subquery->where('country_code', $country_code);
                }

                if ($lang_code) {
                    $subquery->where('lang_code', $lang_code);
                }
            };

            // Fetch the order with related data and apply the filter
            $order = Order::with([
                'user.roles',
                'shippingAddress',
                'billingAddress',
                'orderItem.product.productTranslation' => $filter, // Apply the filter
            ])->find($order_id); 

            // Check if the order exists
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'No order found for the specified criteria.',
                ];
            }

            // Return success response with order data
            return [
                'success' => true,
                'message' => 'Order retrieved successfully.',
                'data' => $order
            ];

        } catch (\Exception $e) {
            // Handle unexpected exceptions and return an error response
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }


    /**
     * Update the status of an order.
     *
     * This method retrieves an order by its ID and updates its status.
     * If the order is not found, it returns an error response.
     * In case of an exception, it catches the error and provides a failure response.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing the order ID and new status.
     * @return array An associative array indicating success or failure with a message.
     */
    public function orderStatus($request)
    { 
        try {

            // Extract order ID and new status from the request
            $order_status = $request->order_status;
            $order_id = $request->order_id;

            // Find the order by ID
            $order = Order::find($order_id);

            // If the order does not exist, return an error response
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'Order not found.',
                ];
            }

            // Update the order status
            $order->update(['status' => $order_status]);

            $user = User::find($order->user_id);
            $orderNumber = $order->order_number;

            event(new OrderStatusChangeNotification([
                'title' => 'Order '.$order_status,
                'notification_type' => 'order',
                'type' => 'admin',
                'message' => 'Your order with ORDER ID ' . $orderNumber . ' has been ' . $order_status . '.',
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'order_number'=>$orderNumber
            ]));  

            // Return a success response
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
