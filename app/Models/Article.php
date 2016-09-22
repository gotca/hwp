<?php

namespace App\Models;

use HipsterJazzbo\Landlord\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use BelongsToTenant;

    protected $casts = [
        'published' => 'datetime'
    ];

    protected $fillable = [
        'title',
        'url',
        'photo',
        'description',
        'published'
    ];


    public function players()
    {
        return $this->belongsToMany('App\Models\Player')
            ->withPivot('highlight')
            ->withTimestamps();
    }


}
