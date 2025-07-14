<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\ProductImage;

class ProductController extends AdminBaseController
{
    /**
     * Display the product listing page.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin.product.index');
    }

    /**
     * Show the form to add a new product.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function addProductForm(Request $request)
    {
        $parent_categories = $this->category_service->getParentCategories();
        $categories = $parent_categories['data'] ?? null;

        return view('admin.product.add', compact('categories'));
    }

    /**
     * Fetch product data for DataTables with filtering, searching, and sorting.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductData(Request $request)
    {
        $country_code = defaultCountryCode();
        $lang_code = defaultLangCode();
        $page_number = defaultPaginateNumber();

        // Reusable filter closure for country and language filtering
        $filter = function ($subquery) use ($country_code, $lang_code) {
            if ($country_code) {
                $subquery->where('country_code', $country_code);
            }

            if ($lang_code) {
                $subquery->where('lang_code', $lang_code);
            }
        };

        $query = Product::query();

        // Apply search filter
        if ($request->has('custom_search') && $request->custom_search) {
            $searchTerm = $request->custom_search;
            $query->where(function ($query) use ($searchTerm) {
                $query->whereHas('productTranslation', function ($subquery) use ($searchTerm) {
                    $subquery->where('name', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('category.categoryTranslations', function ($subquery) use ($searchTerm) {
                    $subquery->where('name', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('sku', 'like', '%' . $searchTerm . '%')
                ->orWhere('slug', 'like', '%' . $searchTerm . '%');
            });
        }

        // Apply status and publish status filters
        if ($request->has('status_filter') && $request->status_filter != '') {
            $query->where('status', $request->status_filter);
        }
        if ($request->has('publish_status_filter') && $request->publish_status_filter != '') {
            $query->where('publish', $request->publish_status_filter);
        }

        // Handle sorting
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $columns = $request->input('columns');
        $orderColumn = $columns[$orderColumnIndex]['name'];

        // If sorting by product name (from productTranslation table)
        if ($orderColumn == 'name') {
            $query->join('product_translations', 'product_translations.product_id', '=', 'products.id')
                ->where('product_translations.lang_code', $lang_code) // Ensure it's the correct language
                ->orderBy('product_translations.name', $orderDirection);
        }elseif($orderColumn == 'Category'){
            $query->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('category_translations', 'category_translations.category_id', '=', 'categories.id')
            ->where('category_translations.lang_code', $lang_code)
            ->orderBy('category_translations.name', $orderDirection);
        }else {
            $query->orderBy($orderColumn, $orderDirection); // Default sorting
        }
  

        // Get filtered record count
        $totalFilteredRecords = $query->count();

        // Fetch paginated data with necessary relationships
        $products = $query->skip($request->start)
            ->take($request->length)
            ->with([
                'productTranslation' => $filter,
                'category.categoryTranslations' => $filter,
            ])->get();

        // Return response in DataTables format
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => Product::count(),
            'recordsFiltered' => $totalFilteredRecords,
            'data' => $products,
        ]);
    }

    /**
     * Update the status of a product (active/inactive).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProductStatus(Request $request)
    {
        $response = $this->product_service->updateProductStatus($request);

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Status changed successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please check and try again.',
        ]);
    }

    /**
     * Store a new product in the database.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addProduct(Request $request)
    {
        // Validate request data
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'stock_quantity' => 'required',
            'regular_price' => 'required',
            'message' => 'required|unique:product_translations,message',
        ]);

        $result = $this->product_service->storeProduct($request);

        if ($result['success']) {
            $request->session()->flash('success', $result['message']);
            return redirect()->route('product-list');
        }

        $request->session()->flash('error', $result['message']);
        return redirect()->back();
    }

    /**
     * Show the edit form for a specific product.
     *
     * @param int $product_id
     * @return \Illuminate\View\View
     */
    public function editProduct($product_id)
    {
        $single_product = $this->product_service->getSingleProduct($product_id);
        $product = $single_product['data'] ?? null;

        $parent_categories = $this->category_service->getParentCategories();
        $categories = $parent_categories['data'] ?? null;

        return view('admin.product.edit', compact('product', 'categories'));
    }

    /**
     * Update the product details in the database.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProduct(Request $request)
    {
        // Validate request data
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'stock_quantity' => 'required',
            'regular_price' => 'required',
        ]);

        $result = $this->product_service->updateProductData($request);

        if ($result['success']) {
            $request->session()->flash('success', $result['message']);
            return redirect()->route('product-list');
        }

        $request->session()->flash('error', $result['message']);
        return redirect()->back();
    }

    /**
     * Delete a product from the database.
     *
     * @param Request $request
     * @param int $product_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteProduct(Request $request, $product_id)
    {
        $product = Product::find($product_id);

        if (!$product) {
            $request->session()->flash('error', 'Product not found');
            return redirect()->back();
        }

        $product->delete();

        $request->session()->flash('success', 'Product deleted successfully');
        return redirect()->route('product-list');
    }




   /**
     * Delete a product image from the database and the storage directory.
     * 
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing the image ID.
     * @return \Illuminate\Http\JsonResponse JSON response with success or error message.
     */
    public function deleteProductImage(Request $request)
    {
        // Get the product image ID from the request
        $product_image_id = $request->image_id;

        // Attempt to find the product image record in the database using the provided ID
        $product_image = ProductImage::find($product_image_id);

        // If the product image is not found, return an error response
        if (!$product_image) {
            return response()->json([
                'success' => false,
                'message' => 'This image does not exist.',
            ], 404); // Return a 404 status code as the image was not found
        }

        // Check if the product image record has an associated image
        if ($product_image->images) {
            // Construct the file path to the image in the storage directory
            $filePath = public_path('storage/product/' . $product_image->images);
            // Check if the image file exists before trying to delete it
            if (file_exists($filePath)) {
                unlink($filePath);
            } 
        }

        // If no image file is set (null or empty), proceed to delete the product image record from the database
        $product_image->delete();

        // Return a success response indicating that the database record has been deleted
        return response()->json([
            'success' => true,
            'message' => 'Image record deleted successfully.',
        ]);
    }


}
