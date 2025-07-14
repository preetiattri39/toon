<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'currency_code',
        'session_id',
        'cart_status',
        'status',
        'ordering'
    ];

    public function cartItem()
    {
        return $this->hasMany(CartItem::class,'cart_id','id');
    }
    
}
