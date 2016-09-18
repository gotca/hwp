<?php

namespace App\Models;

use HipsterJazzbo\Landlord\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    use BelongsToTenant;
    
    protected $fillable = ['rank', 'team', 'tied', 'self'];

    public $timestamps = false;
}
