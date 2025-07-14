<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRating extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'review',
        'status',
        'ordering'
    ];

    public function users()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }
}
