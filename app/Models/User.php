<?php

namespace App\Models;

use HipsterJazzbo\Landlord\BelongsToTenant;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
