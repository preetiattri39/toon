<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    protected $fillable = [
        'product_id',
        'country_code',
        'lang_code',
        'name',
        'description',
        'short_description',
        'features',
        'specifications',
        'message'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }
}
