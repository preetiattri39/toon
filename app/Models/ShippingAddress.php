<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip_code',
        'country',
        'phone_number',
        'phone_code',
        'phone_country_code',
        'type'
    ];
}
