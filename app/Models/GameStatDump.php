<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameStatDump extends Model
{

    public function getJsonAttribute($val)
    {
        return json_decode($val, false);
    }

    public function game()
    {
        return $this->belongsTo('App\Models\Game');
    }
}
