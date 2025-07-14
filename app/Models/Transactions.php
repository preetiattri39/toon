<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'paid_at'
    ];

    public function orders()
    {
        return $this->belongsTo(Orders::class,'order_id','id');
    }
    
}
