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
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use App\Models\ShippingAddress;
use App\Models\Transactions;

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
        try{
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

            if(!empty($order->orderItem)){
                foreach($order->orderItem as $orderItem){
                    if(isset($orderItem->product) && $orderItem->product->cover_image)
                    {
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
    
    
    /**
     * Place an order for the authenticated user.
     * 
     * This method places an order for the authenticated user, verifies the availability of shipping methods,
     * processes the payment using Stripe, and updates stock quantities based on the order items.
     * The method also handles shipping address creation or updating and confirms the order status.
     * 
     * @param \Illuminate\Http\Request $request The incoming request containing order details including shipping information.
     * 
     * @return array A response indicating the success or failure of the order placement, 
     *               along with relevant order details and payment intent client secret for the front-end to confirm the payment.
     */
    public function placeUserOrder($request)
    {
        try{
            // Get the authenticated user
            $user = Auth::user();
            $user_email = $user->email;

            $shipping_method_id = $request->shipping_method_id;
            $shipping_method = ShippingMethod::find($shipping_method_id);
            if(!$shipping_method){
                return [
                    'success' => false,
                    'message' => 'Shipping method not exist!',
                ];
            }
            
            $shipping_cost = $shipping_method->price ?? 0;
            $shipping_method_id = $shipping_method->id;

            // Retrieve the most recent active order with pending status
            $order = Order::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

            // Check if there's a valid order to place
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'No pending order found for placement!',
                ];
            }

            if (!$order->shippingMethod()->wherePivot('shipping_method_id', $shipping_method_id)->exists()) {
                $order->shippingMethod()->attach($shipping_method_id, ['shipping_cost' => $shipping_cost]);
                $total = $order->total + $shipping_cost;
                $order->update([
                    'total'=>$total,
                    'shipping_cost'=>$shipping_cost,
                ]);
            }

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

            if($request->shipping_addss_id){
                $shipping_address_id = $request->shipping_add_id;
                $shipping_address = ShippingAddress::find($shipping_address_id);
                if($shipping_address){
                    $shipping_address->update($shipping_address_data);
                }
            }else{
                $shipping_address_data['type'] = 'shipping';
                $shipping_address_data['user_id'] = $user->id;
                $shipping_address = ShippingAddress::create($shipping_address_data);
                $shipping_address_id = $shipping_address->id;
            }

            $amount = $order->subtotal * 100;  // Convert to cents (Stripe requires amount in cents)

            // Loop through the order items to update stock levels
            foreach ($order->orderItem as $orderItem) {

                $product = Product::find($orderItem->product_id);
                if (!$product) {
                    return [
                        'success' => false,
                        'message' => "Product {$orderItem->product_id} not found during order placement.",
                    ];
                }
            
                $productStock = $product->stock_quantity ?? 0;
            
                // Ensure sufficient stock is available
                if ($productStock < $orderItem->quantity) {
                    return [
                        'success' => false,
                        'message' => "Insufficient stock for product {$product->id}. Available: {$productStock}.",
                    ];
                }
            
                // Deduct stock
                $product->stock_quantity -= $orderItem->quantity;
            
                // Save the updated product record
                $product->save();
            }            

            $country_code = defaultCountryCode();
            $lang_code = defaultLangCode();

            // Reusable filter closure for country and language codes
            $filter = function($subquery) use ($country_code, $lang_code) {
                if ($country_code) {
                    $subquery->where('country_code', $country_code);
                }
    
                if ($lang_code) {
                    $subquery->where('lang_code', $lang_code);
                }
            };
            
            $stripe_key = config('global-constant.STRIPE_KEYS.STRIPE_SECRET_KEY');

            // Set your secret key from Stripe
            Stripe::setApiKey($stripe_key);

            // Create a PaymentIntent with the amount and currency
            try {
                
                $paymentIntent = PaymentIntent::create([
                    'amount' => $amount,  // in cents
                    'currency' => 'usd',
                    'metadata' => [
                        'order_id' => $order->id,
                    ],
                ]);
                  
                // Generate a unique tracking ID (you can customize the format)
                // Pattern 2: Example "CNAOG0000077749"
                $tracking_number = strtoupper(Str::random(3)) . strtoupper(Str::random(3)) . rand(1000000000, 9999999999);

                // Mark the order as pending payment
                $order->update([
                    'status' => 'confirmed',
                    'shipping_address_id' => $shipping_address_id,
                    'billing_address_id' => $shipping_address_id,
                    'payment_status' => 'pending',
                    'tracking_number' => $tracking_number,
                ]);

                $order_details = Order::with([
                    'user.roles',
                    'shippingAddress',
                    'billingAddress',
                    'orderItem.product.productTranslation' => $filter,
                ])->find($order->id); 

                Mail::to($user_email)->send(new OrderConfirmationMail($order_details));

                return [
                    'success' => true,
                    'message' => 'Order placed successfully. Please complete your payment.',
                    'data' => [
                        'order' => $order->id,
                        'client_secret' => $paymentIntent->client_secret, // Send this to the client-side to confirm payment
                    ]
                ];

            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => 'Payment intent creation failed. ' . $e->getMessage(),
                ];
            }
    
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
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
?>
