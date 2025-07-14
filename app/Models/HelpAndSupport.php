<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpAndSupport extends Model
{
    protected $fillable = [
        'user_id',
        'admin_id',
        'name',
        'email',
        'phone_country_code',
        'phone_code',
        'phone_number',
        'subject',
        'message',
        'status',
        'ordering',
        'solutions'
    ];
}
