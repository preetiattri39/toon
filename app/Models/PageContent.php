<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageContent extends Model
{
    protected $fillable = [
        'page_id',
        'country_code',
        'lang_code',
        'name',
        'page_content',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class,'page_id','id');
    }
    
}
