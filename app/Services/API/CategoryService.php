<?php 

namespace App\Services\API;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Auth;

// Models
use App\Models\Category;

class CategoryService
{   
    /**
     * Retrieve a list of active categories.
     *
     * @return array The response containing the status of the request, message, and the retrieved categories data.
     */
    public function categories($request)
    {   
        $country_code = defaultCountryCode();
        $lang_code = defaultLangCode();

        $query = Category::query();

        $query->whereHas('categoryTranslations',function($query) use ($country_code, $lang_code) {
        
            if ($country_code) {
                $query->where('country_code', $country_code);
            }
            if ($lang_code) {
                $query->where('lang_code', $lang_code);
            }
        });
        
        $categories = $query->with([
            'categoryTranslations' => function($query) use ($country_code, $lang_code) {
                if ($country_code) {
                    $query->where('country_code', $country_code);
                }
                if ($lang_code) {
                    $query->where('lang_code', $lang_code);
                }
            },
            'subCategories' => function ($query) use ($country_code, $lang_code) {
                $query->with([
                    'categoryTranslations' => function($query) use ($country_code, $lang_code) {
                        if ($country_code) {
                            $query->where('country_code', $country_code);
                        }
                        if ($lang_code) {
                            $query->where('lang_code', $lang_code);
                        }
                    },
                    'subCategories' => function ($query) use ($country_code, $lang_code) {
                        $query->with([
                            'categoryTranslations' => function($query) use ($country_code, $lang_code) {
                                if ($country_code) {
                                    $query->where('country_code', $country_code);
                                }
                                if ($lang_code) {
                                    $query->where('lang_code', $lang_code);
                                }
                            },
                            'subCategories' => function ($query) use ($country_code, $lang_code) {
                                $query->with([
                                    'categoryTranslations' => function($query) use ($country_code, $lang_code) {
                                        if ($country_code) {
                                            $query->where('country_code', $country_code);
                                        }
                                        if ($lang_code) {
                                            $query->where('lang_code', $lang_code);
                                        }
                                    },
                                    'subCategories' => function ($query) use ($country_code, $lang_code) {
                                        $query->with([
                                            'categoryTranslations' => function($query) use ($country_code, $lang_code) {
                                                if ($country_code) {
                                                    $query->where('country_code', $country_code);
                                                }
                                                if ($lang_code) {
                                                    $query->where('lang_code', $lang_code);
                                                }
                                            },
                                            'subCategories' => function ($query) use ($country_code, $lang_code) {
                                                $query->with([
                                                    'categoryTranslations' => function($query) use ($country_code, $lang_code) {
                                                        if ($country_code) {
                                                            $query->where('country_code', $country_code);
                                                        }
                                                        if ($lang_code) {
                                                            $query->where('lang_code', $lang_code);
                                                        }
                                                    }
                                                ]);
                                            }
                                        ]);
                                    }
                                ]);
                            }
                        ]);
                    }
                ]);
            }
        ])->whereNull('parent_id')
        ->where('status', true)
        ->orderBy('id', 'desc')
        ->get();



        
        // Check if any categories are found
        if($categories->isNotEmpty()){
        
            // Call the function on categories
            $this->updateThumbnails($categories);
            
            return [
                'success' => true,
                'message' => 'Categories retrieved successfully.',
                'data'=>$categories
            ];
        }

        return [
            'success' => false,
            'message' => 'Categories not found.',
        ];

    }


    protected function updateThumbnails($categories) {

        foreach($categories as $category) {
            // Update category thumbnail
            if (isset($category->thumbnail) && $category->thumbnail) {
                $category->thumbnail = asset('storage/category/' . $category->thumbnail);   
            }
            
            // Update subcategory thumbnails
            if (isset($category->subCategories) && $category->subCategories->isNotEmpty()) {
                foreach ($category->subCategories as $subCategory) {
                    if (isset($subCategory->thumbnail) && $subCategory->thumbnail) {
                        $subCategory->thumbnail = asset('storage/category/' . $subCategory->thumbnail);   
                    }
    
                    // Recursive call for nested subcategories
                    if (isset($subCategory->subCategories) && $subCategory->subCategories->isNotEmpty()) {
                        $this->updateThumbnails($subCategory->subCategories);
                    }
                }
            }
        }
    }
    
  
    
}
?>
