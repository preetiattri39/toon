<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VatRate extends Model
{
    protected $fillable = [
        'country_id',
        'region_id',
        'vat_name',
        'vat_rate',
        'description',
        'status',
        'ordering'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class,'country_id','id');
    }
}
