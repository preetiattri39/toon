<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = "admins";
    protected $guard = 'admin';

    protected $fillable = [
        'name',
        'gender',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'phone_code',
        'phone_number',
        'status',
        'profile_pic',
        'is_email_verified',
        'google_id',
        'google_email',
        'facebook_id',
        'facebook_email',
        'apple_id',
        'apple_email',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function category()
    {
        return $this->hasMany(Category::class,'created_by','id');
    }

    public function product()
    {
        return $this->hasMany(Product::class,'product_id','id');
    }
    
}
