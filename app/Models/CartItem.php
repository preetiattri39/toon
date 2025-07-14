<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'country_code',
        'lang_code',
        'quantity',
        'regular_price',
        'discounted_price',
        'status',
        'ordering'
    ];


    public function cart()
    {
        return $this->belongsTo(Cart::class,'cart_id','id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

}
