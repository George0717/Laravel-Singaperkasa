<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class SuperAdmin extends Model
{
    use HasFactory, HasUuids, HasApiTokens;

    protected $fillable = ['nama', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];
    protected $cast = [
        'email_verified_at' => 'datetime', 
        'password' => 'hashed'
    ];
    protected $guard = 'super-admin';
}
