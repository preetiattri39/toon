<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends AdminBaseController
{
    /**
     * Display a listing of the categories.
     *
     * This method fetches a list of categories based on the request
     * parameters and passes them to the view for displaying the
     * category list in the admin panel.
     *
     * @param Request $request The request instance.
     * @return \Illuminate\View\View The view with the category list.
     */
    public function index(Request $request)
    {
        $category_listing = $this->category_service->getCategories($request);
        $categories = $category_listing['data'] ?? null;
        return view('admin.category.index', compact('categories'));
    }

    /**
     * Show the form to add a new category.
     *
     * This method fetches the list of parent categories and passes them
     * to the view where the admin can select a parent category while
     * adding a new category.
     *
     * @param Request $request The request instance.
     * @return \Illuminate\View\View The view to add a new category.
     */
    public function addCategoryForm(Request $request)
    {
        $parent_categories = $this->category_service->getParentCategories();
        $categories = $parent_categories['data'] ?? null;
        return view('admin.category.add', compact('categories'));
    }

    /**
     * Store a newly created category.
     *
     * This method validates the category data and then calls the category service
     * to store the new category. If the category is successfully added, it redirects
     * to the category list page with a success message. If it fails, it redirects
     * back with an error message.
     *
     * @param Request $request The request instance containing the form data.
     * @return \Illuminate\Http\RedirectResponse Redirect to the category list or back with a message.
     */
    public function addCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Call the profile service to update the profile
        $response = $this->category_service->store($request);

        // Handle the response from the service
        if ($response['success']) {
            // Flash a success message and redirect to the category list page
            $request->session()->flash('success', $response['message']);
            return redirect()->route('category-list');
        }

        // Flash an error message and redirect back to the add category page
        $request->session()->flash('error', $response['message']);
        return redirect()->back();
    }

    /**
     * Filter and display categories based on the given filters.
     *
     * This method fetches the filtered list of categories based on the request
     * parameters and passes them to the view for displaying the filtered list
     * in the admin panel.
     *
     * @param Request $request The request instance containing the filter data.
     * @return \Illuminate\View\View The view with the filtered category list.
     */
    public function categoryFilter(Request $request)
    {
        $category_listing = $this->category_service->getCategories($request);
        $categories = $category_listing['data'] ?? null;
        return view('admin.category.category-filter', compact('categories'));
    }

    /**
     * Delete a specified category.
     *
     * This method finds the category by its ID, deletes it, and then redirects
     * the user to the category list page with a success message. If the category
     * is not found, it redirects back with an error message.
     *
     * @param Request $request The request instance.
     * @param int $cat_id The ID of the category to be deleted.
     * @return \Illuminate\Http\RedirectResponse Redirect to the category list or back with a message.
     */
    public function deleteCategory(Request $request, $cat_id)
    {
        $category = Category::find($cat_id);

        if (!$category) {
            $request->session()->flash('error', 'Category not found');
            return redirect()->back();
        }

        $category->delete();

        $request->session()->flash('success', 'Category deleted successfully');
        return redirect()->route('category-list');
    }

    /**
     * Show the form to edit a specific category.
     *
     * This method fetches the category details to be edited along with the list
     * of parent categories, and passes them to the view for updating the category.
     *
     * @param Request $request The request instance.
     * @param int $cat_id The ID of the category to be edited.
     * @return \Illuminate\View\View The view to edit the category.
     */
    public function editCategory(Request $request, $cat_id)
    {
        $edit_category = Category::with(['categoryTranslations'])->find($cat_id);
        $parent_categories = $this->category_service->getParentCategories();
        $categories = $parent_categories['data'] ?? null;
        return view('admin.category.edit', compact('edit_category', 'categories'));
    }

    /**
     * Update the specified category.
     *
     * This method validates the updated category data and calls the category
     * service to update the category details. After the update, it redirects
     * to the category list page with a success message or redirects back with an error message.
     *
     * @param Request $request The request instance containing the form data.
     * @return \Illuminate\Http\RedirectResponse Redirect to the category list or back with a message.
     */
    public function updateCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Call the profile service to update the profile
        $response = $this->category_service->update($request);

        // Handle the response from the service
        if ($response['success']) {
            // Flash a success message and redirect to the category list page
            $request->session()->flash('success', $response['message']);
            return redirect()->route('category-list');
        }

        // Flash an error message and redirect back to the edit category page
        $request->session()->flash('error', $response['message']);
        return redirect()->back();
    }

    /**
     * Update the status of a category.
     *
     * This method updates the status of the specified category, and returns a
     * JSON response indicating the success or failure of the operation.
     *
     * @param Request $request The request instance.
     * @return \Illuminate\Http\JsonResponse The JSON response with success or error message.
     */
    public function updateCategoryStatus(Request $request)
    {
        // Call the profile service to update the profile
        $response = $this->category_service->updateStatus($request);

        // Handle the response from the service
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
}
