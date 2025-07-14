<?php

namespace App\Services\API;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

use App\Mail\OrderConfirmationMail;

// Models
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use App\Models\ShippingAddress;
use App\Models\Transactions;
use App\Events\OrderStatusChangeNotification;
use Illuminate\Support\Facades\Log;



class OrderService
{

    /**
     * Retrieve the current pending order for the authenticated user.
     *
     * This method fetches the most recent pending order for the authenticated user. It checks if the user has a pending order
     * and returns it along with the associated order items and their product translations. If no pending order is found,
     * an appropriate error message is returned.
     *
     * @return array The response array containing:
     *               - `success` (bool): Whether the request was successful or not.
     *               - `message` (string): A message describing the result of the request.
     *               - `data` (mixed): The order data if successful, or null in case of an error or no pending order.
     */
    public function getCurrentPendingOrders()
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Retrieve the most recent active order with pending status
            $order = Order::where('user_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->with([
                    'orderItem.product.productTranslation',
                ])->first();

            // Check if there's a valid order
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'No pending order found for placement!',
                ];
            }

            if (!empty($order->orderItem)) {
                foreach ($order->orderItem as $orderItem) {
                    if (isset($orderItem->product) && $orderItem->product->cover_image) {
                        $orderItem->product->cover_image = asset('storage/product/' . $orderItem->product->cover_image);
                    }
                }
            }

            return [
                'success' => true,
                'message' => 'Pending Orders retrieved successfully.',
                'data' => $order,
            ];
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }


    public function addNewShippingAdddress($request)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();
            $shipping_address_data = [
                'address_line_1' => $request->shipping_address_line_1,
                'address_line_2' => $request->shipping_address_line_2,
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
                'zip_code' => $request->shipping_zip,
                'country' => $request->shipping_country,
                'phone_number' => $request->shipping_phone_number,
                'name' => $request->name,
                'phone_code' => $request->phone_code,
                'phone_country_code' => $request->phone_country_code,
            ];

            $shipping_address_data['type'] = 'shipping';
            $shipping_address_data['user_id'] = $user->id;
            $shipping_address = ShippingAddress::create($shipping_address_data);

            return [
                'success' => true,
                'message' => 'Your address added successfully.',
                'data' => $shipping_address
            ];
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }


    public function getShippingAddress($request)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Get the user ID of the authenticated user
            $user_id = $user->id;

            // Retrieve the shipping addresses for the user, ordered by 'id' in descending order
            $shipping_address = ShippingAddress::where('user_id', $user_id)
                ->orderBy('id', 'desc')
                ->get(); // Get all matching shipping addresses
                foreach ($shipping_address as $address) {
                    if (is_null($address->email)) {
                        $address->email = $user->email;
                    }
                }

            // Return a successful response with the retrieved shipping addresses
            return [
                'success' => true,
                'message' => 'Your shipping address retrieved successfully.',
                'data' => $shipping_address // Pass the retrieved addresses in the response
            ];
        } catch (\Exception $e) {
            // If an error occurs, return an error response with the exception message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(), // Include the error message
            ];
        }
    }


    /**
     * Confirm the payment for an order and update its status.
     *
     * This method processes the payment confirmation by retrieving the 
     * `payment_intent` from Stripe and verifying its status. If the payment 
     * is successful, it updates the order's status to "paid" and "confirmed" 
     * and stores the payment transaction in the database. If the payment fails, 
     * it updates the order's payment status to "failed".
     * 
     * @param \Illuminate\Http\Request $request The incoming request containing payment intent and order details.
     * 
     * @return array A response indicating the success or failure of the payment confirmation.
     */
    public function confirmPayment($request)
    {
        try {
            // Extract payment intent ID and order ID from the request
            $paymentIntentId = $request->payment_intent;
            $order_id = $request->order_id;

            // Set Stripe API key from the configuration
            $stripe_key = config('global-constant.STRIPE_KEYS.STRIPE_SECRET_KEY');
            Stripe::setApiKey($stripe_key);

            // Retrieve payment intent from Stripe
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            // Log the order_id for debugging
            Log::info("order_id: " . $order_id);

            // Fetch the order by ID
            $order = Order::find($order_id); // Using find() to retrieve a single record
            $user_id = $order->user_id ?? null;
            $user = User::find($user_id);

            // Check if the payment was successful
            if ($paymentIntent->status === "succeeded") {
                // Update the order status to "paid" and "confirmed"
                if ($order) {
                    $order->update([
                        'payment_method' => 'stripe',
                        'payment_status' => 'paid',
                        'status' => 'confirmed',
                    ]);

                    // Generate a unique transaction ID
                    $transactionId = 'TXN' . date('mdHi'); // Format: TXNMMDDHHMM

                    // Create a new transaction record in the database
                    $transaction = Transactions::create([
                        'order_id' => $order_id,
                        'user_id' => $order->user_id,
                        'amount' => $order->total,
                        'payment_method' => 'stripe',
                        'transaction_id' => $transactionId,
                        'status' => 'completed',
                        'paid_at' => now(),
                    ]);

                    $user = User::find($order->user_id);
                    $orderNumber = $order->order_number;
                    $cart = Cart::where('user_id', $order->user_id)->first();

                        if ($cart) {
                            // Delete all cart items
                            CartItem::where('cart_id', $cart->id)->delete();

                            // Delete the cart itself
                            $cart->delete();
                        }

                    event(new OrderStatusChangeNotification([
                        'title' => 'Order  confirmed',
                        'notification_type' => 'order',
                        'type' => 'customer',
                        'message' => 'Your order with ORDER ID ' . $orderNumber . ' has been  confirmed.',
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'order_number' => $orderNumber
                    ]));
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

                    $order_details = Order::with([
                        'user.roles',
                        'shippingAddress',
                        'billingAddress',
                        'orderItem.product.productTranslation' => $filter,
                    ])->find($order->id);

                    //Mail::to($user->email)->send(new OrderConfirmationMail($order_details));
                }

                // Return success response with order details
                return [
                    'success' => true,
                    'message' => 'Payment was successful and order has been confirmed.',
                    'data' => [
                        'order_id' => $order->order_number,
                        'subtotal' => $order->subtotal,
                        'paymentIntent' => $transaction->transaction_id,
                    ]
                ];
            } else {
                // Update the order status to "failed" if payment failed
                if ($order) {
                    $order->update([
                        'payment_status' => 'failed',
                    ]);
                }
            }

            // Return failure response if payment did not succeed
            return [
                'success' => false,
                'message' => 'Payment failed.',
            ];
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }


    /**
     * Retrieve all available shipping methods.
     *
     * This method fetches all the available shipping methods from the 
     * `ShippingMethod` model and returns them in a structured response. 
     * If no shipping methods are found, it will return a failure message.
     * In case of an exception, an error message with the exception details 
     * will be returned for debugging purposes.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing order or shipping details (if applicable).
     *
     * @return array A response array indicating success or failure and the list of shipping methods (if successful).
     */
    public function getShippingMethod($request)
    {
        try {
            // Retrieve all available shipping methods from the ShippingMethod model
            $shipping_method = ShippingMethod::all();

            // Check if no shipping methods are found
            if ($shipping_method->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Shipping method not exist.',
                ];
            }

            // Return the response with the retrieved shipping methods
            return [
                'success' => true,
                'message' => 'Shipping method retrieved successfully.',
                'data' => $shipping_method,
            ];
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }
}
