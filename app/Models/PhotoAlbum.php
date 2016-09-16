<?php

namespace App\Models;

use HipsterJazzbo\Landlord\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PhotoAlbum extends Model
{
    use BelongsToTenant;
    
    protected $table = 'albums';

    public function cover()
    {
        return $this->belongsTo('App\Models\Photo');
    }

    public function photos()
    {
        return $this->belongsToMany('App\Models\Photo', 'album_photo', 'album_id');
    }

    public function game()
    {
        return $this->hasOne('App\Models\Game', 'album_id');
    }
}
