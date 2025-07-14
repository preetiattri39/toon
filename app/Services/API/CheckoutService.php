<?php 

namespace App\Services\API;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Auth;

use App\Events\NewOrderGenerateNotifications;

// Models
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;

class CheckoutService
{   
    /**
     * Process the checkout for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing checkout data.
     * 
     * @return array A response array indicating the success of the checkout process, with order details.
     * 
     */
    public function checkout($request)
    {
        try{
            // Get the authenticated user
            $user = Auth::user();

            // Retrieve the user's cart
            $user_cart = Cart::where('user_id', $user->id)
            ->where('cart_status', 'active')
            ->first();

            // Return an error if no active cart is found
            if (!$user_cart) {
                return [
                    'success' => false,
                    'message' => 'No active cart found!',
                ];
            }

            // Get the cart items
            $user_cart_ids = $user_cart->id;
            $cart_items = CartItem::where('cart_id', $user_cart->id)->get();

            if($cart_items->isEmpty()){
                return [
                    'success' => false,
                    'message' => 'No products found in the cart!',
                ];
            }

            // Check stock for each item in the cart
            $subtotal_price = 0;

            $order = Order::where('user_id',$user->id)
            ->where('status','pending')
            ->where('payment_status','unpaid')
            ->first();

            foreach ($cart_items as $cart_item) {

                $product = Product::find($cart_item->product_id);
        
                // If the product is not found return an error
                if (!$product) {
                    return [
                        'success' => false,
                        'message' => 'Product not found or removed from inventory!',
                    ];
                }

                // Check if the product has enough stock
                $product_stock = $product->stock_quantity ?? 0;

                if ($product_stock < $cart_item->quantity) {
                    return [
                        'success' => false,
                        'message' => "Not enough stock for product {$product->name}. Available: {$product_stock}.",
                    ];
                }

                $discounted_price = $product->discounted_price ?? 0;

                $orderItem = OrderItem::where('product_id',$cart_item->product_id)
                ->where('order_id',$order->id ?? null)
                ->first();

                if($orderItem){
                    $subtotal_price = 0;
                }else{
                    if ($discounted_price) {
                        $subtotal_price += $cart_item->quantity * $product->discounted_price;
                    } else {
                        $subtotal_price += $cart_item->quantity * $product->regular_price;
                    }
                }

            }

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

            $tax = $request->tax;

            $orderNumber = 'ORD' . date('YmdHis'); // Generates the format ORDYYYYMMDD
            // Proceed with creating an order
            
            if($order){

                $new_total_price = $order->total + $subtotal_price;

                // Add tax only if it's different
                if ($order->tax != $tax) {
                    // Step 2: Calculate difference in tax (zyada ya kam ho sakta hai)
                    $tax_difference = $tax - $order->tax;
                    $new_total_price += $tax_difference;
                }
                
                // Add shipping cost
                if ($order->shipping_cost != $shipping_cost) {
                    // Step 3: Calculate difference in shipping cost
                    $shipping_difference = $shipping_cost - $order->shipping_cost;
                    $new_total_price += $shipping_difference;
                }
                
                $new_subtotal_price = $order->subtotal + $subtotal_price;

                $order_data = [
                    'total' => $new_total_price,
                    'subtotal' => $new_subtotal_price,
                    'tax'=>$tax,
                    'shipping_cost'=>$shipping_cost,
                ];

                $order->update($order_data);

            }else{
                $total_price = $subtotal_price + $tax;
                
                $order_data = [
                    'user_id' => $user->id,
                    'total' => $total_price,
                    'subtotal' => $subtotal_price,
                    'tax'=>$tax,
                    'status' => 'pending', 
                    'payment_status' => 'unpaid',
                    'order_number' =>  $orderNumber
                ];

                $order = Order::create($order_data);
            }

            if (!$order->shippingMethod()->wherePivot('shipping_method_id', $shipping_method_id)->exists()) {
                $order->shippingMethod()->attach($shipping_method_id, ['shipping_cost' => $shipping_cost]);
                $total = $order->total + $shipping_cost;
                $order->update([
                    'total'=>$total,
                    'shipping_cost'=>$shipping_cost,
                ]);
            }

            foreach ($cart_items as $cart_item) {

                $product = Product::find($cart_item->product_id);

                $order_items = [
                    'quantity' => $cart_item->quantity,
                    'regular_price' => $product->regular_price,
                    'discounted_price' => $product->discounted_price,
                ];

                $orderItem = OrderItem::where('product_id',$cart_item->product_id)
                ->where('order_id',$order->id)
                ->first();

                if($orderItem){
                    $orderItem->update($order_items);
                }else{
                    $order_items['product_id'] =  $cart_item->product_id;
                    $order->orderItem()->create($order_items);
                }

                // $order->orderItem()->create($order_items);
            }

            // Clear the cart after successful checkout
            // $user_cart->cartItem()->delete();
            // $user_cart->delete();

            // event(new NewOrderGenerateNotifications([
            //     'title' => 'New Order Received',
            //     'notification_type' => 'order',
            //     'type' => 'customer',
            //     'message' => 'You have received new order from '.$user->email.' with ORDER ID '.$orderNumber,
            //     'user_id' => $user->id,
            //     'name' => $user->name,
            //     'email' => $user->email,
            //     'order_number'=>$orderNumber
            // ]));   

            return [
                'success' => true,
                'message' => 'Checkout completed successfully.',
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

}
?>
