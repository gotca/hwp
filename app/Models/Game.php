<?php

namespace App\Models;

use App\Models\Traits\Event;
use App\Models\Traits\UsesCustomCollection;
use HipsterJazzbo\Landlord\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{

    use BelongsToTenant, Event, UsesCustomCollection;

    const WIN = 'win';
    const LOSS = 'loss';
    const TIE = 'tie';

    /**
     * Force start an end to be datetimes/carbon
     *
     * @var array
     */
    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime'
    ];

    public function getResultAttribute()
    {
        return $this->status();
    }

    public function getTitleAttribute()
    {
        return trans('misc.'.$this->team) . ' vs ' . $this->opponent;
    }

    public function tournament()
    {
        return $this->belongsTo('App\Models\Tournament');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location');
    }

    public function album()
    {
        return $this->belongsTo('App\Models\PhotoAlbum');
    }

    public function stats()
    {
        return $this->hasMany('App\Models\Stat');
    }

    public function updates()
    {
        return $this->hasOne('App\Models\GameUpdateDump');
    }

    public function boxStats()
    {
        return $this->hasOne('App\Models\GameStatDump');
    }
}
