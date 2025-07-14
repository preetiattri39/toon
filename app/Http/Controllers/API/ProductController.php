<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends APIBaseController
{
    /**
     * Retrieves a list of products based on the provided filters.
     *
     * This method calls the product service to fetch products based on various filters 
     * such as search term, category, and other attributes. It then returns a JSON response 
     * containing the products or an error message if something goes wrong.
     *
     * @param \Illuminate\Http\Request $request The request object containing the filters and parameters for fetching products.
     * 
     * @return \Illuminate\Http\JsonResponse The JSON response with the list of products or error details.
     * 
     */
    public function getProducts(Request $request)
    {
        try {  
            // Fetch products based on request parameters
            $response = $this->product_service->getProductData($request);

            // Return the JSON response using the response helper
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            // Return an error response if something went wrong
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional: for debugging purposes, may expose error details
            ], $this->internal_server_status);
        }
    }

    /**
     * Retrieves detailed information about a single product.
     *
     * This method calls the product service to fetch detailed information about a 
     * product based on its ID, including related data such as ratings and images. 
     * It returns a JSON response containing the product details or an error message 
     * if something goes wrong.
     *
     * @param int $product_id The ID of the product to retrieve.
     * 
     * @return \Illuminate\Http\JsonResponse The JSON response with the product details or error information.
     * 
     */
    public function getSingleProduct($product_id)
    {
        try {  
            // Fetch the single product data based on the product ID
            $response = $this->product_service->getSingleProductData($product_id);

            // Return the JSON response using the response helper
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional: for debugging purposes, may expose error details
            ], $this->internal_server_status);
        }
    }

    /**
     * Posts a product rating and comment.
     * 
     * This method receives a request containing product ratings and comments from the user and forwards them
     * to the `postProductComments` method of the `ProductService`. It then returns a JSON response indicating the success or failure
     * of the operation.
     * 
     * @param \Illuminate\Http\Request $request The incoming request containing the rating and comment for a product.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response indicating whether the rating and comment were successfully posted.
     */
    public function productRating(Request $request)
    {
        try {  
            $response = $this->product_service->postProductComments($request);

            // Return the JSON response using the response helper
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional: for debugging purposes, may expose error details
            ], $this->internal_server_status);
        }
    }

    /**
     * Adds a product to the user's bookmarks.
     * 
     * This method receives a request containing product details from the user and forwards them
     * to the `addBookMark` method of the `ProductService`. It then returns a JSON response indicating the success or failure
     * of the operation.
     * 
     * @param \Illuminate\Http\Request $request The incoming request containing the product details to be bookmarked.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response indicating whether the product was successfully added to bookmarks.
     */
    public function addBookMarkProduct(Request $request)
    {
        try {  
            
            $response = $this->product_service->addBookMark($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Retrieves the bookmarked products for the user.
     * 
     * This method receives a request containing the user’s details and retrieves the list of products 
     * that the user has previously bookmarked. It forwards the request to the `retrieveBookMarkProduct` method 
     * of the `ProductService` and returns a JSON response indicating the success or failure of the operation.
     * 
     * @param \Illuminate\Http\Request $request The incoming request containing the user’s details.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response with the list of bookmarked products or an error message.
     */
    public function getBookMarkProduct(Request $request)
    {
        try {  
            // Call the ProductService to retrieve the user's bookmarked products
            $response = $this->product_service->retrieveBookMarkProduct($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Removes a product from the user's bookmarks.
     * 
     * This method receives a request containing the product details that need to be removed from the user's bookmarks.
     * It forwards the request to the `deleteBookMarkProduct` method of the `ProductService` and returns a JSON response 
     * indicating the success or failure of the operation.
     * 
     * @param \Illuminate\Http\Request $request The incoming request containing the product details to be removed from bookmarks.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response indicating whether the product was successfully removed from bookmarks.
     */
    public function removeBookMarkProduct(Request $request)
    {
        try {  
            
            $response = $this->product_service->deleteBookMarkProduct($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }


}
