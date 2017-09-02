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

    public function advantages()
    {
        return $this->hasMany('App\Models\Advantage');
    }

    public function boxscores()
    {
        return $this->hasMany('App\Models\Boxscore');
    }

    public function updates()
    {
        return $this->hasOne('App\Models\GameUpdateDump');
    }

    public function badge()
    {
        return $this->belongsTo('App\Models\Badge');
    }

    /**
     * @param $status
     * @return string
     */
    public static function oppositeStatus($status) {
        switch ($status) {
            case Game::WIN:
                return Game::LOSS;

            case Game::LOSS:
                return Game::WIN;

            default:
                return Game::TIE;
        }
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function boxStats()
    {
        return $this->hasOne('App\Models\GameStatDump');
    }
}
