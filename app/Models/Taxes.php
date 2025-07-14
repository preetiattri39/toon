<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taxes extends Model
{
    protected $fillable = [
        'tax_name',
        'rate',
        'region',
        'tax_type'
    ];
}
