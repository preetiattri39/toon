<?php 
    namespace App\Services\Admin;
    
    use Spatie\Permission\Models\Role;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;

    use App\Models\Product;
    use App\Models\ProductTranslation;
    use App\Models\ProductImage;
    
    class ProductService
    {
        /**
         * Retrieves a paginated list of products with optional filters.
         *
         * @param \Illuminate\Http\Request $request
         * @return array
         */
        public function getProducts($request)
        {
            try {
                $country_code = defaultCountryCode();
                $lang_code = defaultLangCode();
                $page_number = defaultPaginateNumber();
                $search_by = $request->search_by;
                $status = $request->status;
                $publish_status = $request->publish_status;
    
                // Reusable filter closure
                $filter = function($subquery) use ($country_code, $lang_code) {
                    if ($country_code) {
                        $subquery->where('country_code', $country_code);
                    }

                    if ($lang_code) {
                        $subquery->where('lang_code', $lang_code);
                    }
                };

                $query = Product::query();
    
                // Search products by name or SKU
                if ($search_by) {
                    $query->where(function($query) use ($search_by) {
                        $query->whereHas('productTranslation', function ($subquery) use ($search_by) {
                            // Search in the product translation name field
                            $subquery->where('name', 'like', '%' . $search_by . '%');
                        })
                        ->orWhere('sku', 'like', '%' . $search_by . '%')  // Search in sku
                        ->orWhere('slug', 'like', '%' . $search_by . '%');  // Search in slug
                    });
                }
                
                if ($status !== null) {
                    $query->where('status', $status);
                }

                if($publish_status){
                    $query->where('publish', $publish_status);
                }
    
                $products = $query->with([
                    'productTranslation' => $filter,
                    'category.categoryTranslations' => $filter,
                ])->orderBy('id','desc')->paginate($page_number);
    
                return [
                    'success' => true,
                    'message' => 'Products retrieved successfully.',
                    'data' => $products
                ];
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ];
            }
        }

         /**
         * Toggles the status of a given product.
         *
         * @param \Illuminate\Http\Request $request
         * @return array
         */
        public function updateProductStatus($request)
        {
            
            try {

                $product_id = $request->product_id;
    
                $product = Product::find($product_id);
            
                if (!$product) {
                    return [
                        'success' => false,
                        'message' => 'Product not found.',
                    ];
                }
    
                $status = $product->status;
    
                $status = ($status == true) ? 0 : 1;
    
                $product->update(['status' => $status]);
    
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

        /**
         * Stores a new product along with its translations and images.
         *
         * @param \Illuminate\Http\Request $request
         * @return array
         */
        public function storeProduct($request)
        {
            try {
                // Get the authenticated admin user
                $admin_user = Auth::guard('admin')->user();
                $admin_id = $admin_user->id;

                $country_code = defaultCountryCode();
                $lang_code = defaultLangCode();

                $category = $request->category;
                $stock_quantity = $request->stock_quantity;
                $regular_price = $request->regular_price;
                $discounted_price = $request->discounted_price;

                $product_data = [
                    'category_id'=>$category,
                    'created_by'=>$admin_id,
                    'product_type'=>'simple',
                    'publish'=>'published',
                    'stock_quantity'=>$stock_quantity,
                    'regular_price'=>$regular_price,
                    'discounted_price'=>$discounted_price,
                    'slug'=>Str::slug($request->name),
                ];

                if ($request->hasFile('cover_image')) {
                    $file_name = uniqid() . "." . $request->file('cover_image')->getClientOriginalExtension();
                    $request->file('cover_image')->storeAs('product', $file_name);
                    $product_data['cover_image'] = $file_name;
                }
    
                $product = Product::create($product_data);

                if (!$product) {
                    return [
                        'success' => false,
                        'message' => 'Product could not be created.',
                    ];
                }

                $product_id = $product->id;

                $product_translation_data = [
                    'product_id' => $product_id,
                    'country_code' => $country_code,
                    'lang_code' => $lang_code,
                    'name' => $request->name,
                    'description' => $request->description,
                    'short_description' => $request->short_description,
                    'message' => $request->message,
                ];

                ProductTranslation::create($product_translation_data);
                
                // Step 3: Store multiple images
                if ($request->hasFile('media')) {
                    foreach ($request->file('media') as $image) {
                        $file_name = uniqid() . "." . $image->getClientOriginalExtension();
                        $image->storeAs('product', $file_name);
        
                        ProductImage::create([
                            'product_id' => $product_id,
                            'images' => $file_name,
                        ]);
                    }
                }

                // Return a success response
                return [
                    'success' => true,
                    'message' => 'Product created successfully.',
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
         * Retrieves a single product by ID, including translations.
         *
         * @param int $product_id
         * @return array
         */
        public function getSingleProduct($product_id)
        {
            try {
                // Get default country code and language code
                $country_code = defaultCountryCode();
                $lang_code = defaultLangCode();

                // Reusable filter closure
                $filter = function($subquery) use ($country_code, $lang_code) {
                    if ($country_code) {
                        $subquery->where('country_code', $country_code);
                    }

                    if ($lang_code) {
                        $subquery->where('lang_code', $lang_code);
                    }
                };

                // Retrieve product with filtered relationships
                $product = Product::with([
                    'productTranslation' => $filter,
                    'productImage'
                ])->find($product_id);
 
                // Check if product is found
                if (!$product) {
                    return [
                        'success' => false,
                        'message' => 'Product not found.',
                    ];
                }
                
                // Return success response with product data
                return [
                    'success' => true,
                    'message' => 'Product retrieved successfully.',
                    'data' => $product
                ];

            } catch (\Exception $e) {
                // Return error response in case of exception
                return [
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ];
            }
        }

        /**
         * Updates a product and its related data.
         *
         * @param \Illuminate\Http\Request $request
         * @return array
         */
        public function updateProductData($request)
        {
            try {
                // Get the authenticated admin user
                $admin_user = Auth::guard('admin')->user();
                $admin_id = $admin_user->id;

                // Default country and language codes
                $country_code = defaultCountryCode();
                $lang_code = defaultLangCode();

                // Extract request data
                $product_id = $request->product_id;
                $product = Product::find($product_id);

                if (!$product) {
                    return [
                        'success' => false,
                        'message' => 'Product not found.',
                    ];
                }

                // Prepare product data for update
                $product_data = [
                    'category_id'      => $request->category,
                    'created_by'       => $admin_id,
                    'stock_quantity'   => $request->stock_quantity,
                    'regular_price'    => $request->regular_price,
                    'discounted_price' => $request->discounted_price,
                    'slug'            => Str::slug($request->name),
                    'publish'         => $request->publish_status,
                ];

                // Handle cover image upload
                if ($request->hasFile('cover_image')) {
                    
                    if ($product->cover_image) {
                        // Check if the brand has a logo and unlink it
                        $profilepath = public_path('storage/product/' . $product->cover_image);
                        if ($product->cover_image && file_exists($profilepath)) {
                            unlink($profilepath);
                        }
                    }

                    $file_name = uniqid() . "." . $request->file('cover_image')->getClientOriginalExtension();
                    $request->file('cover_image')->storeAs('product', $file_name);
                    $product_data['cover_image'] = $file_name;
                }

                // Update product details
                $product->update($product_data);

                // Prepare product translation data
                $product_translation_data = [
                    'name'              => $request->name,
                    'description'       => $request->description,
                    'short_description' => $request->short_description,
                    'message' => $request->message,
                ];

                // Fetch existing translation or create a new one
                $product_translation = ProductTranslation::where('product_id', $product_id)
                    ->where('country_code', $country_code)
                    ->where('lang_code', $lang_code)
                    ->first();

                if ($product_translation) {
                    $product_translation->update($product_translation_data);
                } else {
                    ProductTranslation::create(array_merge($product_translation_data, [
                        'product_id'    => $product_id,
                        'lang_code'     => $lang_code,
                        'country_code'  => $country_code,
                    ]));
                }

                // Store multiple product images
                if ($request->hasFile('media')) {
                    foreach ($request->file('media') as $image) {
                        $file_name = uniqid() . "." . $image->getClientOriginalExtension();
                        $image->storeAs('product', $file_name);

                        ProductImage::create([
                            'product_id' => $product_id,
                            'images'     => $file_name,
                        ]);
                    }
                }

                return [
                    'success' => true,
                    'message' => 'Product updated successfully.',
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