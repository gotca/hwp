<?php

namespace App\Models;

use HipsterJazzbo\Landlord\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use BelongsToTenant;

    const PATH = 'http://photos.hudsonvillewaterpolo.com/';

    const THUMB_PATH = 'http://photos.hudsonvillewaterpolo.com/thumbs/';

    

    public function getPhotoAttribute()
    {
        return self::PATH . $this->file . '.jpg';
    }

    public function getThumbAttribute()
    {
        return self::THUMB_PATH . $this->file . '.jpg';
    }

    public function getJSONData(Player $player = null)
    {
        $json = ['main'=>null, 'also'=>[]];
        $playersTemp = $this->players;

        if($player != null){
            $playersTemp = $playersTemp->reject(function($p) use($player) {
                return $p->id === $player->id;
            });

            $json['main'] = $player->toArray();
        }

        if($playersTemp->count()){
            $json['also'] = $playersTemp->toArray();
        }

        return json_encode($json);
    }

    public function albums()
    {
        return $this->belongsToMany('App\Models\PhotoAlbum', 'album_photo');
    }

    public function players()
    {
        return $this->belongsToMany('App\Models\Player');
    }
}
