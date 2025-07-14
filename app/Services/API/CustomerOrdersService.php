<?php 

namespace App\Services\API;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// PDF
use Dompdf\Dompdf;
use Dompdf\Options;

// Models
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderShipping;
use App\Models\ShippingAddress;

class CustomerOrdersService
{
    /**
     * Retrieve all orders for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @return array Response containing order details or an error message.
     */
    public function getOrderDetails($request)
    {
        try {
            // Retrieve authenticated user
            $user = Auth::user();
            $country_code = defaultCountryCode();
            $lang_code = defaultLangCode();

            // Check if the user exists
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User does not exist.',
                ];
            }

            // Closure to apply country and language code filters
            $filter = function ($subquery) use ($country_code, $lang_code) {
                if ($country_code) {
                    $subquery->where('country_code', $country_code);
                }
                if ($lang_code) {
                    $subquery->where('lang_code', $lang_code);
                }
            };

            // Retrieve user orders with necessary relationships
            $user_id = $user->id;
            $my_orders = Order::with([
                'user.roles',
                'transactions',
                'OrderItem.product',
                'OrderItem.product.productTranslation' => $filter,
                'shippingAddress',
                'billingAddress',
            ])->where('user_id', $user_id)->orderBy('created_at', 'desc')->get();

            return [
                'success' => true,
                'message' => 'User orders retrieved successfully.',
                'data' => $my_orders,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieve details of a single order for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request The request object containing the order ID.
     * @return array Response containing order details or an error message.
     */
    public function getSingleOrder($request)
    {
        try {
            // Retrieve authenticated user
            $user = Auth::user();
            $country_code = defaultCountryCode();
            $lang_code = defaultLangCode();
            $order_id = $request->order_id;

            // Check if the user exists
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User does not exist.',
                ];
            }

            // Closure to apply country and language code filters
            $filter = function ($subquery) use ($country_code, $lang_code) {
                if ($country_code) {
                    $subquery->where('country_code', $country_code);
                }
                if ($lang_code) {
                    $subquery->where('lang_code', $lang_code);
                }
            };

            // Retrieve user orders with necessary relationships
            $user_id = $user->id;
            $order = Order::with([
                'user.roles',
                'transactions',
                'OrderItem.product',
                'OrderItem.product.productTranslation' => $filter,
                'shippingAddress',
                'billingAddress',
            ])->where('user_id', $user_id)->find($order_id);

            // Handle case where the order is not found
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'Order not found.',
                ];
            }

            // Append product cover image with full URL if available
            if (isset($order->OrderItem)) {
                foreach ($order->OrderItem as $OrderItem) {
                    if ($OrderItem->product->cover_image) {
                        $OrderItem->product->cover_image = asset('/storage/product/' . $OrderItem->product->cover_image);
                    }
                }
            }

            return [
                'success' => true,
                'message' => 'User order retrieved successfully.',
                'data' => $order,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }
}
?>