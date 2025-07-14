<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'status'
    ];

    public function orders()
    {
        return $this->belongsToMany(Orders::class,'order_shippings','shipping_method_id','order_id')->withPivot('shipping_cost');
    }
    
}
