<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $fillable = [
        'user_id',
        'admin_id',
        'title',
        'message',
        'type',
        'notification_type',
        'read_status',
        'ordering',
        'read_at',
        'name',
        'email',
        'order_number'
    ];
}
