<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends APIBaseController
{
    /**
     * Adds a product to the user's cart.
     * 
     * This method receives a request with the product details and attempts to add the product
     * to the user's shopping cart by invoking the `addToCart` method from the CartService.
     * It returns a JSON response indicating the success or failure of the operation.
     * 
     * @param \Illuminate\Http\Request $request The incoming request containing the product ID and quantity.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the operation.
     */
    public function addProductToCart(Request $request)
    {
        try {  
            
            $response = $this->cart_service->addToCart($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Retrieve the products in the authenticated user's cart.
     *
     * This method calls the `getCartProducts` method from the `cart_service` to retrieve all products 
     * in the authenticated user's cart. It returns a JSON response based on the result of the operation.
     *
     * @param \Illuminate\Http\Request $request The request object that may contain necessary data for filtering or retrieving cart products.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating whether the cart retrieval was successful.
     */
    public function getUserCart(Request $request)
    {
        try {  
            
            $response = $this->cart_service->getCartProducts($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Remove a specific item from the authenticated user's cart.
     *
     * This method calls the `removeUserCartItem` method from the `cart_service` to remove a specific 
     * item from the authenticated user's cart. It returns a JSON response based on the result of the operation.
     *
     * @param \Illuminate\Http\Request $request The request object that contains necessary data to identify the cart item to be removed.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating whether the operation was successful.
     */
    public function removeItem(Request $request)
    {
        try {  
            
            $response = $this->cart_service->removeUserCartItem($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Remove all items from the authenticated user's cart.
     *
     * This method invokes the `removeUserAllCartItems` method from the `cart_service` 
     * to remove all items from the authenticated user's cart. It then returns the appropriate 
     * response based on whether the operation was successful or not.
     *
     * @param \Illuminate\Http\Request $request The request object containing necessary data for removing items from the cart.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the operation.
     */
    public function removeAllItems(Request $request)
    {
        try {  
            
            $response = $this->cart_service->removeUserAllCartItems($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Increase the quantity of a product in the user's cart.
     *
     * This method invokes the `increaseProductQuantity` method from the `cart_service` 
     * to increase the quantity of a product in the authenticated user's cart. 
     * It then returns the appropriate response based on whether the operation was successful.
     *
     * @param \Illuminate\Http\Request $request The request object containing data such as `cart_item_id` for the product whose quantity needs to be increased.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the operation.
     */
    public function increaseQuantity(Request $request)
    {
        try {  
            
            $response = $this->cart_service->increaseProductQuantity($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Decrease the quantity of a product in the user's cart.
     *
     * This method invokes the `decreaseProductQuantity` method from the `cart_service` 
     * to decrease the quantity of a product in the authenticated user's cart. 
     * It then returns the appropriate response based on whether the operation was successful.
     *
     * @param \Illuminate\Http\Request $request The request object containing data such as `cart_item_id` for the product whose quantity needs to be decreased.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the operation.
     */
    public function decreaseQuantity(Request $request)
    {
        try {  
            
            $response = $this->cart_service->decreaseProductQuantity($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

}
