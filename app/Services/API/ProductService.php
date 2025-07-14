<?php 

namespace App\Services\API;
use Auth;
use DB;

// Models
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\ProductImage;
use App\Models\ProductRating;
use App\Models\BookmarkProduct;


class ProductService
{
    /**
     * Retrieves a list of products based on search filters.
     *
     * This method fetches products based on various parameters such as search term, 
     * category, product type (top, new, on sale), and sorting by price. It returns 
     * a collection of products with their ratings and other related information.
     *
     * @param \Illuminate\Http\Request $request The request object containing the search filters.
     * 
     * @return array The response array with success status, message, and the list of products.
     * 
     */
    public function getProductData($request)
    {
        try {
            $user = Auth::guard('api')->user();
            $user_id = $user->id ?? null;

            // Retrieve default country and language codes
            $country_code = defaultCountryCode();
            $lang_code = defaultLangCode();
            $page_number = defaultPaginateNumber();
            $search_by = $request->search_by;
            $category = $request->category;

            // Custom filters for product attributes
            $is_top = $request->is_top;
            $is_new = $request->is_new;
            $is_on_sale = $request->is_on_sale;
            $price_order = $request->price_order;

            // Closure for reusable filtering logic based on country and language
            $filter = function($subquery) use ($country_code, $lang_code) {
                if ($country_code) {
                    $subquery->where('country_code', $country_code);
                }

                if ($lang_code) {
                    $subquery->where('lang_code', $lang_code);
                }
            };

            $query = Product::query();

            // Search products by name, SKU, or slug
            if ($search_by) {
                $query->where(function($query) use ($search_by) {
                    $query->whereHas('productTranslation', function ($subquery) use ($search_by) {
                        // Search in the product translation name field
                        $subquery->where('name', 'like', '%' . $search_by . '%');
                    })
                    ->orWhere('sku', 'like', '%' . $search_by . '%')  // Search in SKU
                    ->orWhere('slug', 'like', '%' . $search_by . '%');  // Search in slug
                });
            }

            // Filter by category if provided
            if($category){
                $query->where('category_id', $category);
            }

            // Filter for "Top" products based on average rating
            if ($is_top) {
                $query->join('product_ratings', 'products.id', '=', 'product_ratings.product_id')
                    ->select('products.id',
                    'products.category_id',
                    'products.created_by',
                    'products.is_featured',
                    'products.status',
                    'products.publish',
                    'products.cover_image',
                    'products.stock_quantity',
                    'products.regular_price',
                    'products.discounted_price',
                    'products.sku', 
                    'products.slug', 
                    'products.created_at', 
                    'products.updated_at', 
                    \DB::raw('AVG(product_ratings.rating) as avg_rating'))
                    ->groupBy('products.id')
                    ->havingRaw('AVG(product_ratings.rating) >= ?', [2.5]);  // Example threshold for "top" products
            }
            
            // Filter for "New" products created in the last 30 days
            if ($is_new) {
                $query->where('created_at', '>=', now()->subDays(30)); // Products created in the last 30 days
            }

            // Filter for "Sale" products with discounted price
            if ($is_on_sale) {
                $query->where('discounted_price', '<', \DB::raw('regular_price')); // Products with a discount
            }

            // Sorting products by price (ascending or descending)
            if ($price_order) {
                if ($price_order === 'low_to_high') {
                    $query->orderBy('regular_price', 'asc');  // Sort by regular price ascending
                } elseif ($price_order === 'high_to_low') {
                    $query->orderBy('regular_price', 'desc');  // Sort by regular price descending
                }
            }

            // Get the products with related translations and ratings
            $products = $query->with([
                'productTranslation' => $filter,
                'category.categoryTranslations' => $filter,
                'productRating',
                'bookmarkProduct',
                'cartItem' => function($query) use ($user_id) {
                    $query->whereHas('Cart', function($q) use ($user_id) {
                        $q->where('user_id', $user_id); 
                    })
                    ->select('id','product_id','quantity'); 
                }
            ])
            ->orderBy('products.id', 'desc')  
            ->where('products.publish', 'published') 
            ->where('products.status', true) 
            ->whereHas('category', function ($query) {
                $query->where('status', true); // Assuming the category also has a 'status' field
            })
            ->get();

            // Add average rating and rating count to each product
            foreach($products as $product){

                $averageRating = $product->productRating->avg('rating');
                $ratingCount = $product->productRating->count();

                $product->average_rating = $averageRating;
                $product->rating_count = $ratingCount;

                // product image path
                if($product->cover_image){
                    $product->cover_image = asset('storage/product/' . $product->cover_image);
                }

                // category image path
                if(isset($product->category) && $product->category->thumbnail){
                    $product->category->thumbnail = asset('storage/category/' . $product->category->thumbnail);
                }
                $bookmark = $product->bookmarkProduct
                    ->firstWhere('user_id', $user_id);

                // Boolean flag
                $product->is_bookmarked = $bookmark ? 1 : 0;

                // Return the actual bookmark id (or null if not bookmarked)
                $product->bookmark_id = $bookmark
                    ? $bookmark->id
                    : null;

            // clean up raw relation if you want
            unset($product->bookmarkProduct);

            }

            return [
                'success' => true,
                'message' => 'Products retrieved successfully.',
                'data' => $products
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
     * Retrieves detailed information about a single product.
     *
     * This method fetches a product by its ID, including related translations, images, 
     * ratings, and similar products from the same category. It returns product data 
     * with rating statistics and details of similar products.
     *
     * @param int $product_id The ID of the product to retrieve.
     * 
     * @return array The response array with success status, message, and product data.
     *
     */
   public function getSingleProductData($product_id)
    {
        try {
            $user_id = Auth::guard('api')->id();

            // Default codes
            $country_code = defaultCountryCode();
            $lang_code    = defaultLangCode();

            // Translation filter
            $filter = function($q) use ($country_code, $lang_code) {
                if ($country_code) $q->where('country_code', $country_code);
                if ($lang_code)    $q->where('lang_code', $lang_code);
            };

            // Fetch single product
            $product = Product::with([
                'productTranslation'             => $filter,
                'category.categoryTranslations'  => $filter,
                'productRating.users',
                'productImage',
                'bookmarkProduct',
                'cartItem' => fn($q) => $q
                    ->whereHas('Cart', fn($c) => $c->where('user_id', $user_id))
                    ->select('id','product_id','quantity'),
            ])
            ->where('publish', 'published')
            ->where('status', true)
            ->find($product_id);

            if (! $product) {
                return [ 'success' => false, 'message' => 'Product not found.' ];
            }

            // Images URLs
            if ($product->cover_image) {
                $product->cover_image = asset("storage/product/{$product->cover_image}");
            }
            if (isset($product->category->thumbnail)) {
                $product->category->thumbnail = asset("storage/category/{$product->category->thumbnail}");
            }
            foreach ($product->productImage as $img) {
                if ($img->images) {
                    $img->images = asset("storage/product/{$img->images}");
                }
            }

            // Ratings
            $product->average_rating = $product->productRating->avg('rating');
            $product->rating_count   = $product->productRating->count();

            // Bookmark info
            $bookmark = $product->bookmarkProduct->firstWhere('user_id', $user_id);
            $product->is_bookmarked = $bookmark ? 1 : 0;
            $product->bookmark_id   = $bookmark ? $bookmark->id : null;
            unset($product->bookmarkProduct);

            // Similar products
            $similar = Product::with([
                'productTranslation'            => $filter,
                'category.categoryTranslations' => $filter,
                'productRating',
                'bookmarkProduct',
                'cartItem' => fn($q) => $q
                    ->whereHas('Cart', fn($c) => $c->where('user_id', $user_id))
                    ->select('id','product_id','quantity'),
            ])
            ->where('category_id', $product->category_id)
            ->where('publish', 'published')
            ->where('status', true)
            ->where('id', '!=', $product_id)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

            foreach ($similar as $item) {
                if ($item->cover_image) {
                    $item->cover_image = asset("storage/product/{$item->cover_image}");
                }
                if (isset($item->category->thumbnail)) {
                    $item->category->thumbnail = asset("storage/category/{$item->category->thumbnail}");
                }

                $item->average_rating = $item->productRating->avg('rating');
                $item->rating_count   = $item->productRating->count();

                $bm = $item->bookmarkProduct->firstWhere('user_id', $user_id);
                $item->is_bookmarked = $bm ? 1 : 0;
                $item->bookmark_id   = $bm ? $bm->id : null;
                unset($item->bookmarkProduct);
            }

            return [
                'success' => true,
                'message' => 'Product retrieved successfully.',
                'data'    => [
                    'product'          => $product,
                    'similar_products' => $similar,
                ],
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }
    /**
     * Posts a product rating and comment from the user.
     * 
     * This method allows the user to post a rating and review for a specific product. It checks if the user has already 
     * posted a rating for the product, and if not, creates a new rating record. It returns a structured response with 
     * success or failure status.
     * 
     * @param \Illuminate\Http\Request $request The incoming request containing the product ID, rating, and comment.
     * 
     * @return array A structured array with success status, message, and rating data if successful, or an error message.
     */
    public function postProductComments($request)
    {
        try {
            // Retrieve the product ID and user details from the request and authenticated user
            $product_id = $request->product_id;
            $user = Auth::user();
            $user_id = $user->id ?? null;

            // Find the product by ID
            $product = Product::find($product_id);
            
            // If the product doesn't exist, return an error response
            if(!$product){
                return [
                    'success' => false,
                    'message' => 'Product not exist.',
                ];
            }

            // Check if the user has already posted a rating for the product
            $product_rating_check = ProductRating::where('product_id',$product_id)->where('user_id',$user_id)->first();

            // If the user has already rated the product, prevent additional ratings
            if($product_rating_check){
                return [
                    'success' => false,
                    'message' => 'You cannot post more than one comment.'
                ];
            }

            // Create a new rating entry
            $rating = ProductRating::create([
                'product_id'=>$product_id,
                'user_id'=>$user_id,
                'rating'=>$request->rating,
                'review'=>$request->comments,
            ]);

            // Return a success response with the newly created rating data
            return [
                'success' => true,
                'message' => 'Product rating post successfully.',
                'data' => $rating
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
     * Adds a product to the user's bookmarks.
     * 
     * This method allows a user to bookmark a product. It first checks if the user is authenticated and if the
     * product exists. If the user has already bookmarked the product, it prevents adding the bookmark again.
     * If successful, the new bookmark is saved and a response with the bookmark data is returned.
     * 
     * @param \Illuminate\Http\Request $request The incoming request containing the product ID.
     * 
     * @return array A structured response containing success status, message, and bookmark data if successful,
     *               or an error message in case of failure.
     */
    public function addBookMark($request)
    {
        try{
            // Retrieve the authenticated user's ID
            $user = Auth::user();
            $userId = $user->id ?? null;

            // If no user is logged in, return an error response
            if (!$userId) {
                return [
                    'success' => false,
                    'message' => 'User is not logged in.',
                ];
            }

            // Retrieve the product ID from the request
            $productId = $request->product_id;

            // Find the product by its ID
            $product = Product::find($productId);

            // If the product doesn't exist, return an error response
            if (!$product) {
                return [
                    'success' => false,
                    'message' => 'Product not found.',
                ];
            }

            // Check if the product has already been bookmarked by the user
            $existingBookmark = BookmarkProduct::where('user_id', $userId)
                                                ->where('product_id', $productId)
                                                ->first();

            // If the product is already bookmarked, return a message indicating that
            if ($existingBookmark) {
                return [
                    'success' => false,
                    'message' => 'You have already bookmarked this product.',
                ];
            }

            // Create a new bookmark for the product
            $newBookmark = BookmarkProduct::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);

            return [
                'success' => true,
                'message' => 'Product bookmarked successfully.',
                'data' => $newBookmark,
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
     * Retrieves all the bookmarked products for a user.
     * 
     * This method fetches all products bookmarked by a user along with their associated product translation
     * and ratings. It also calculates the average rating and rating count for each product.
     * 
     * @param \Illuminate\Http\Request $request The incoming request, containing user information.
     * 
     * @return array A structured response containing success status, message, and bookmarked products data.
     */
    public function retrieveBookMarkProduct($request)
    {
        try {
            // Get the authenticated user's ID
            $user = Auth::user();
            $userId = $user->id ?? null;
            $country_code = defaultCountryCode();
            $lang_code = defaultLangCode();

            // Define a filter to apply country and language constraints
            $filter = function($subquery) use ($country_code, $lang_code) {
                if ($country_code) {
                    $subquery->where('country_code', $country_code);
                }

                if ($lang_code) {
                    $subquery->where('lang_code', $lang_code);
                }
            };

            // Retrieve all the bookmarks for the authenticated user
            $bookmarks = BookmarkProduct::with([
                'users',
                'product.productTranslation' => $filter,
                'product.productRating'
            ])
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

            // Add average rating and rating count to each product
            foreach ($bookmarks as $bookmark) {
                // Check if productRating relationship exists and is not empty
                if ($bookmark->product->productRating && $bookmark->product->productRating->isNotEmpty()) {
                    $averageRating = $bookmark->product->productRating->avg('rating');
                    $ratingCount = $bookmark->product->productRating->count();
                    $bookmark->product->average_rating = $averageRating;
                    $bookmark->product->rating_count = $ratingCount;
                } else {
                    // If no ratings, set to 0 or appropriate value
                    $bookmark->product->average_rating = 0;
                    $bookmark->product->rating_count = 0;
                }

                if(isset($bookmark->product) && $bookmark->product->cover_image){
                    $bookmark->product->cover_image = asset('storage/product/' . $bookmark->product->cover_image);
                }
            }

            return [
                'success' => true,
                'message' => 'Product bookmarked retrieved successfully.',
                'data' => $bookmarks,
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
     * Deletes a bookmarked product for a user.
     * 
     * This method removes a product from the user's bookmarked list by its bookmark ID.
     * It checks if the bookmark exists and ensures the provided bookmark ID is valid.
     * 
     * @param \Illuminate\Http\Request $request The incoming request, containing bookmark ID to be deleted.
     * 
     * @return array A structured response containing success status, message, and deleted bookmark data.
     */
    public function deleteBookMarkProduct($request)
    {
        try {
            // Get the bookmark ID from the request
            $bookmark_id = $request->bookmark_id;

            // If no bookmark ID is provided, return an error response
            if (!$bookmark_id) {
                return [
                    'success' => false,
                    'message' => 'Bookmark ID is required.',
                ];
            }

            // Find the bookmark product by its ID
            $bookmark_product = BookmarkProduct::find($bookmark_id);
            
            // If the bookmark does not exist, return an error response
            if (!$bookmark_product) {
                return [
                    'success' => false,
                    'message' => 'Bookmark does not exist.',
                ];
            }

            // Delete the bookmark product
            $bookmark_product->delete();

            // Return a success response with the deleted bookmark product data
            return [
                'success' => true,
                'message' => 'Bookmarked product removed successfully.',
                'data' => $bookmark_product,
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
