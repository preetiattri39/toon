<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookmarkProduct extends Model
{
    protected $fillable = [
        'user_id',
        'product_id'
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
