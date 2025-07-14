<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'publish',
        'status',
        'type',
        'ordering'
    ];

    public function pageContent()
    {
        return $this->hasOne(PageContent::class,'page_id','id');
    }
}
