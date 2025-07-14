<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'created_by',
        'is_featured',
        'product_type',
        'status',
        'ordering',
        'publish',
        'cover_image',
        'stock_quantity',
        'regular_price',
        'discounted_price',
        'slug',
        'sku'
    ];

    public function admin(){
        return $this->belongsTo(Admin::class,'created_by','id');
    }

    public function productTranslation()
    {
        return $this->hasOne(ProductTranslation::class,'product_id','id');
    }

    public function productImage()
    {
        return $this->hasMany(ProductImage::class,'product_id','id');
    }

    public function productRating()
    {
        return $this->hasMany(ProductRating::class,'product_id','id');
    }

    public function cartItem()
    {
        return $this->hasOne(CartItem::class,'product_id','id');
    }

    public function bookmarkProduct()
    {
        return $this->hasMany(BookmarkProduct::class,'product_id','id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }

}
