<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'admin_id',
        'shipping_address_id',
        'billing_address_id',
        'order_number',
        'status',
        'subtotal',
        'discount',
        'tax',
        'total',
        'shipping_cost',
        'payment_method',
        'payment_status',
        'notes',
        'tracking_number'
    ];

    public function orderItem()
    {
        return $this->hasMany(OrderItem::class,'order_id','id');
    }
    
    public function transactions()
    {
        return $this->hasOne(Transactions::class,'order_id','id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class,'shipping_address_id','id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(ShippingAddress::class,'billing_address_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function shippingMethod()
    {
        return $this->belongsToMany(ShippingMethod::class,'order_shippings','order_id','shipping_method_id')
        ->withPivot('shipping_cost');
    }

}
