<?php 

namespace App\Services\API;

use Auth;
use DB;

// Models
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Cart;

class CartService
{
    /**
     * Adds a product to the user's cart.
     * 
     * This method first validates the product's availability in stock, checks if the requested quantity is valid,
     * and then either creates a new cart item or updates the quantity of an existing item in the cart.
     * 
     * @param \Illuminate\Http\Request $request The incoming request containing the product ID and quantity to add.
     * 
     * @return array An associative array containing the success status, a message, and the cart data or error message.
     */
    public function addToCart($request)
    {
        try {
            // Retrieve the authenticated user
            $user = Auth::user();        
            
            // Find the product by its ID
            $product = Product::find($request->product_id);
        
            // Check if the product exists
            if (!$product) {
                return [
                    'success' => false,
                    'message' => 'Product not found!',
                ];
            }

            // Get the product stock quantity
            $product_stock_in = $product->stock_quantity ? $product->stock_quantity : 0;
        
            // Check if the product is out of stock
            if ($product_stock_in == 0) {
                return [
                    'success' => false,
                    'message' => 'Product is out of stock!',
                ];
            }

            // Get the quantity requested by the user
            $cart_quantity = $request->quantity ?? null;

            // Validate the quantity requested by the user
            if ($cart_quantity < 1) {
                return [
                    'success' => false,
                    'message' => "Please enter a quantity of one or greater."
                ];
            }            
            
            // Check if the requested quantity exceeds available stock
            if ($product_stock_in < $cart_quantity) {
                return [
                    'success' => false,
                    'message' => "Available quantity is {$product_stock_in}, Please enter a value less than or equal to {$product_stock_in}!"
                ];
            }

            // Retrieve the user's existing cart or create a new one if it doesn't exist
            $user_cart = Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['cart_status' => 'active']
            );

            // Prepare the data for the cart item
            $cart_data = [
                'cart_id' => $user_cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'regular_price' => $product->regular_price,
                'discounted_price' => $product->discounted_price,
            ];

            // Check if the product already exists in the user's cart
            $user_cart_items = CartItem::where('cart_id', $user_cart->id)
                ->where('product_id', $request->product_id)
                ->first();

            // Get the current quantity of the product in the cart
            $user_cart_quantity = $user_cart_items->quantity ?? 0;

            // Check if the requested quantity exceeds the available stock in the cart
            if ($product_stock_in <= $user_cart_quantity) {
                return [
                    'success' => false,
                    'message' => "You cannot add more of this product to your cart because the available quantity is {$product_stock_in}. You have already reached the maximum quantity of {$user_cart_quantity} for this product."
                ];
            }            

            // If the product already exists in the cart, update its quantity
            if ($user_cart_items) {
                $user_cart_items->quantity += $request->quantity;
                $user_cart_items->save();
                return [
                    'success' => true,
                    'message' => 'Item quantity updated in cart successfully.',
                    'data' => $user_cart_items,
                ];
            }

            // If the product doesn't exist in the cart, create a new cart item
            $user_cart_items_new = CartItem::create($cart_data);
        
            return [
                'success' => true,
                'message' => 'Item added into cart successfully.',
                'data' => $user_cart_items_new,
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
     * Retrieve the products in the authenticated user's cart.
     *
     * This method fetches the products added to the cart of the currently authenticated user. 
     * It also filters the product translations based on the user's default country and language codes.
     * If no cart items are found, an appropriate error message is returned.
     *
     * @param \Illuminate\Http\Request $request The request object (not used directly in this method but required by convention).
     *
     * @return array The response array containing:
     */
    public function getCartProducts($request)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Get default country code and language code
            $country_code = defaultCountryCode();
            $lang_code = defaultLangCode();

            // Reusable filter closure for translations
            $filter = function($subquery) use ($country_code, $lang_code) {
                if ($country_code) {
                    $subquery->where('country_code', $country_code);
                }

                if ($lang_code) {
                    $subquery->where('lang_code', $lang_code);
                }
            };
        
            // Retrieve user cart with relationships
            $user_cart_items = Cart::with([
                'cartItem',
                'cartItem.product.productTranslation'=> $filter,
            ])->where('user_id', $user->id)->where('cart_status', 'active')->first();

            // Check if the user cart items collection is empty
            if (!$user_cart_items) {
                return [
                    'success' => false,
                    'message' => 'Cart is empty or not found.',
                ];
            }

            foreach($user_cart_items->cartItem as $cartItem){
                
                if(isset($cartItem->product) && $cartItem->product->cover_image)
                {
                    $cartItem->product->cover_image = asset('storage/product/' . $cartItem->product->cover_image);
                }
            }
            
            return [
                'success' => true,
                'message' => 'Cart retrieved successfully.',
                'data' => $user_cart_items,
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
     * Remove a specific product from the user's cart.
     *
     * This method allows the authenticated user to remove a specific product 
     * from their cart by providing the product ID. If no cart is found, or the 
     * product does not exist in the user's cart, an appropriate error message 
     * is returned. The method is wrapped in a try-catch block to handle any 
     * potential exceptions that might occur during the deletion process.
     *
     * @param \Illuminate\Http\Request $request The request object containing the product ID to be removed from the cart.
     *
     * @return array The response array containing:
     */
    public function removeUserCartItem($request)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();
        
            // Validate product ID from the request
            $product_id = $request->product_id ?? null;
            if (!$product_id) {
                return [
                    'success' => false,
                    'message' => 'Please provide a valid product ID.',
                ];
            }
        
            // Find the user's cart
            $user_cart = Cart::where('user_id', $user->id)->first();
        
            if (!$user_cart) {
                return [
                    'success' => false,
                    'message' => 'No cart found for the user.',
                ];
            }
        
            // Find the cart item for the given product
            $cart_item = CartItem::where('cart_id', $user_cart->id)
                ->where('product_id', $product_id)
                ->first();
        
            if (!$cart_item) {
                return [
                    'success' => false,
                    'message' => 'The specified product does not exist in the cart.',
                ];
            }
        
            // Delete the cart item
            $cart_item->delete();
        
            return [
                'success' => true,
                'message' => 'Cart item removed successfully.',
                'data' => $cart_item,
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
     * Remove all items from the user's cart and delete the associated carts.
     *
     * This method removes all cart items for the authenticated user from the cart, 
     * including deleting the cart records themselves. If the user has no cart items, 
     * an appropriate message is returned. The method is wrapped in a try-catch block to 
     * handle potential exceptions.
     *
     * @param \Illuminate\Http\Request $request The request object (not used in this case, but required for the function signature).
     *
     * @return array The response array containing:
     */
    public function removeUserAllCartItems($request)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();
        
            // Retrieve all carts for the user
            $user_carts = Cart::where('user_id', $user->id)->get();
        
            if ($user_carts->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No items found in the user\'s cart.',
                ];
            }
        
            // Delete all cart items associated with the user's carts
            $cart_ids = $user_carts->pluck('id'); // Get all cart IDs for the user
            CartItem::whereIn('cart_id', $cart_ids)->delete();
        
            // delete the cart itself if needed
            Cart::whereIn('id', $cart_ids)->delete();
        
            return [
                'success' => true,
                'message' => 'All cart items for the user have been successfully deleted.',
                'data' => null,
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
     * Increase the quantity of a specific product in the user's cart.
     *
     * This method increases the quantity of a product in the user's cart by 1, as long as the product is not out of stock 
     * and the requested quantity does not exceed the available stock. It also checks if the product exists in the cart 
     * and handles cases where the product's stock quantity is insufficient.
     *
     * @param \Illuminate\Http\Request $request The request object containing the cart item ID.
     *
     * @return array The response array containing:
     */
    public function increaseProductQuantity($request)
    {
        try{
            // Get the authenticated user
            $user = Auth::user();

            // Retrieve the user's cart
            $user_cart = Cart::where('user_id', $user->id)->first();
            
            // If no cart is found for the user, return an error response
            if (!$user_cart) {
                return [
                    'success' => false,
                    'message' => 'No items found in the users cart.',
                ];
            }

            // Retrieve the cart item from the request
            $cart_item_id = $request->cart_item_id;
            $cart_item = CartItem::where('cart_id',$user_cart->id)->find($cart_item_id);

            // If the cart item is not found, return an error response
            if(!$cart_item){
                return [
                    'success' => false,
                    'message' => 'No items found in the users cart items.',
                ];
            }

            // Find the product by its ID
            $product = Product::find($cart_item->product_id);
            // Get the product stock quantity
            $product_stock_in = $product->stock_quantity ? $product->stock_quantity : 0;

            // Check if the product is out of stock
            if ($product_stock_in == 0) {
                return [
                    'success' => false,
                    'message' => 'Product is out of stock!',
                ];
            }

            // Check if the requested quantity exceeds the available stock in the cart
            if ($product_stock_in <= $cart_item->quantity) {
                    return [
                        'success' => false,
                        'message' => "You cannot add more of this product to your cart because the available quantity is {$product_stock_in}. You have already reached the maximum quantity of {$cart_item->quantity} for this product."
                    ];
            }    

            $quantity = $cart_item->quantity+1;

            $cart_item->update([
                'quantity'=>$quantity
            ]);
        
            return [
                'success' => true,
                'message' => 'Product quantity has been successfully increased.',
                'data' => $cart_item,
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
     * Decrease the quantity of a specific product in the user's cart.
     *
     * This method decreases the quantity of a product in the user's cart by 1, provided that the current quantity is greater than 1. 
     * It handles validation checks to ensure that the quantity cannot go below the minimum required quantity of 1. 
     * If the cart or cart item does not exist, or if the quantity is already at the minimum, an appropriate response is returned.
     *
     * @param \Illuminate\Http\Request $request The request object containing the cart item ID.
     *
     * @return array The response array containing:
     */
    public function decreaseProductQuantity($request)
    {
        try{
            // Get the authenticated user
            $user = Auth::user();

            // Check if the user is a "jobber" (manufacturer)
            $is_jobber = $user->hasRole('jobber');

            $user_cart = Cart::where('user_id', $user->id)->first();
        
            if (!$user_cart) {
                return [
                    'success' => false,
                    'message' => 'No items found in the user\'s cart.',
                ];
            }

            $cart_item_id = $request->cart_item_id;

            $cart_item = CartItem::where('cart_id',$user_cart->id)->find($cart_item_id);

            if(!$cart_item){
                return [
                    'success' => false,
                    'message' => 'No items found in the users cart.',
                ];
            }

             // Prevent decreasing the quantity below 1
            if ($cart_item->quantity <= 1) {
                return [
                    'success' => false,
                    'message' => "You cannot decrease the quantity below the minimum required quantity of 1.",
                ];
            }

            // Decrease the quantity of the cart item
            $cart_item->quantity -= 1;

            // Save the updated quantity to the database
            $cart_item->save();

            return [
                'success' => true,
                'message' => 'Product quantity has been successfully decreased.',
                'data' => $cart_item,
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
