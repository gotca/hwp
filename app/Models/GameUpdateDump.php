<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameUpdateDump extends Model
{
    protected $casts = [
        'json' => 'array'
    ];

    public function game()
    {
        return $this->belongsTo('App\Models\Game');
    }
}
