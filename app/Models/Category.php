<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'created_by',
        'parent_id',
        'thumbnail',
        'status',
        'ordering',
        'slug'
    ];

    public function categoryTranslations()
    {
        return $this->hasOne(CategoryTranslations::class,'category_id','id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class,'created_by','id');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function product()
    {
        return $this->hasMany(Product::class, 'product_id','id');
    }

}
