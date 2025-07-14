<?php 
namespace App\Services\Admin;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Category;
use App\Models\CategoryTranslations;

class CategoryService
{
    /**
     * Store a new category.
     *
     * This method handles the process of storing a new category. It accepts the 
     * category details from the request, including an optional thumbnail file,
     * and creates both the category and its corresponding translation.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing category data.
     * @return array Response with success status and a message.
     */
    public function store($request)
    {
        try {
            $admin = Auth::guard('admin')->user();
            $admin_id = $admin->id ?? null;

            $parent_id = $request->parent_category;
            $name = $request->name;
            $description = $request->description;

            // Convert the name into a slug
            $slug = Str::slug($name);

            $country_code = defaultCountryCode();
            $lang_code = defaultLangCode();
            
            $data = [
                'created_by' => $admin_id,
                'parent_id' => $parent_id,
                'slug' => $slug,
            ];

            if ($request->hasFile('thumbnail')) {
                $file_name = uniqid() . "." . $request->file('thumbnail')->getClientOriginalExtension();
                $request->file('thumbnail')->storeAs('category', $file_name);
                $data['thumbnail'] = $file_name;
            }

            $category = Category::create($data);

            if ($category) {
                $category_id = $category->id ?? null;

                $translation_data = [
                    'category_id' => $category_id,
                    'country_code' => $country_code,
                    'lang_code' => $lang_code,
                    'name' => $name,
                    'description' => $description,
                ];

                CategoryTranslations::create($translation_data);

                return [
                    'success' => true,
                    'message' => 'Category created successfully.',
                ];
            }

            return [
                'success' => false,
                'message' => 'Category not created.',
            ];
        
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieve all parent categories.
     *
     * This method fetches all categories that do not have a parent category, 
     * including their translations based on the current country and language codes.
     *
     * @return array Response containing success status, a message, and the list of parent categories.
     */
    public function getParentCategories()
    {
        try {
            $country_code = defaultCountryCode();
            $lang_code = defaultLangCode();

            $categories = Category::with([
                'categoryTranslations' => function ($query) use ($country_code, $lang_code) {
                    if ($country_code) {
                        $query->where('country_code', $country_code);
                    }
                    if ($lang_code) {
                        $query->where('lang_code', $lang_code);
                    }
                }
            ])
            ->whereNull('parent_id')
            ->orderBy('id', 'desc')
            ->get();

            return [
                'success' => true,
                'message' => 'Parent categories retrieved successfully.',
                'data' => $categories
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieve categories with optional search and status filters.
     *
     * This method fetches categories, allowing optional filters for searching 
     * by name and filtering by status. It includes nested sub-categories and 
     * their translations.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing search and status filters.
     * @return array Response containing success status, a message, and the list of categories.
     */
    public function getCategories($request)
    {
        try {
            $country_code = defaultCountryCode();
            $lang_code = defaultLangCode();
            $page_number = defaultPaginateNumber();
            $search_by = $request->search_by;
            $status = $request->status;

            $query = Category::query();

            if ($search_by) {
                $query->whereHas('categoryTranslations', function ($query) use ($country_code, $lang_code, $search_by) {
                    if ($search_by) {
                        $query->where('name', 'like', '%' . $search_by . '%');
                    }
                    if ($country_code) {
                        $query->where('country_code', $country_code);
                    }
                    if ($lang_code) {
                        $query->where('lang_code', $lang_code);
                    }
                });
            }

            if ($status !== null) {
                $query->where('status', $status);
            }

            $categories = $query->with([
                'categoryTranslations' => function ($query) use ($country_code, $lang_code, $search_by) {
                    if ($search_by) {
                        $query->where('name', 'like', '%' . $search_by . '%');
                    }
                    if ($country_code) {
                        $query->where('country_code', $country_code);
                    }
                    if ($lang_code) {
                        $query->where('lang_code', $lang_code);
                    }
                },
                'subCategories' => function ($query) use ($country_code, $lang_code) {
                    $query->with([
                        'categoryTranslations' => function ($query) use ($country_code, $lang_code) {
                            if ($country_code) {
                                $query->where('country_code', $country_code);
                            }
                            if ($lang_code) {
                                $query->where('lang_code', $lang_code);
                            }
                        },
                    ]);
                }
            ])
            ->whereNull('parent_id')
            ->orderBy('id', 'desc')
            ->paginate($page_number);

            return [
                'success' => true,
                'message' => 'Categories retrieved successfully.',
                'data' => $categories
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Search categories by a given query.
     *
     * This method searches categories based on the provided search term and 
     * filters by status, returning a paginated list of matching categories.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing search criteria and filters.
     * @return array Response containing success status, a message, and the search results.
     */
    public function categorySearchBy($request)
    {
        return $this->getCategories($request); // This method essentially reuses `getCategories()`.
    }

    /**
     * Update an existing category.
     *
     * This method updates the details of an existing category, including its 
     * parent category, slug, and optional thumbnail. It also updates the category's 
     * translation data.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing updated category data.
     * @return array Response with success status and a message.
     */
    public function update($request)
    {
        try {
            $admin = Auth::guard('admin')->user();
            $admin_id = $admin->id ?? null;

            $parent_id = $request->parent_category;
            $category_id = $request->category_id;
            $name = $request->name;
            $description = $request->description;

            // Convert the name into a slug
            $slug = Str::slug($name);

            $country_code = defaultCountryCode();
            $lang_code = defaultLangCode();

            $data = [
                'created_by' => $admin_id,
                'parent_id' => $parent_id,
                'slug' => $slug,
            ];

            if ($request->hasFile('thumbnail')) {
                $file_name = uniqid() . "." . $request->file('thumbnail')->getClientOriginalExtension();
                $request->file('thumbnail')->storeAs('category', $file_name);
                $data['thumbnail'] = $file_name;
            }

            $category = Category::find($category_id);

            if ($category) {
                $category->update($data);

                $translation_data = [
                    'country_code' => $country_code,
                    'lang_code' => $lang_code,
                    'name' => $name,
                    'description' => $description,
                ];

                $category_translation = CategoryTranslations::where('category_id', $category->id)
                    ->where('country_code', $country_code)
                    ->where('lang_code', $lang_code)
                    ->first();

                if ($category_translation) {
                    $category_translation->update($translation_data);
                } else {
                    $category_translation['category_id'] = $category->id;
                    CategoryTranslations::create($translation_data);
                }

                return [
                    'success' => true,
                    'message' => 'Category updated successfully.',
                ];
            }

            return [
                'success' => false,
                'message' => 'Category not found.',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update the status of a category.
     *
     * This method toggles the `status` field of a category between active and inactive.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing the category ID.
     * @return array Response with success status and a message.
     */
    public function updateStatus($request)
    {
        try {
            $category_id = $request->category_id;

            $category = Category::find($category_id);

            if (!$category) {
                return [
                    'success' => false,
                    'message' => 'Category not found.',
                ];
            }

            $status = $category->status;
            $status = $status == true ? 0 : 1;

            $category->update(['status' => $status]);

            return [
                'success' => true,
                'message' => 'Status changed successfully.',
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
