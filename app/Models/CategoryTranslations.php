<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryTranslations extends Model
{
    protected $fillable= [
        'category_id',
        'country_code',
        'lang_code',
        'name',
        'description'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }
}
