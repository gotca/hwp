<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    public function players()
    {
        return $this->belongsToMany('App\Models\Player');
    }

    public function seasons()
    {
        return $this->belongsToMany('App\Models\Season');
    }

}
