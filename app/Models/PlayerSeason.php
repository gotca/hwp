<?php

namespace App\Models;

use App\Models\Traits\HasStats;
use App\Models\Traits\UsesCustomCollection;
use HipsterJazzbo\Landlord\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PlayerSeason extends Model
{
    use BelongsToTenant, UsesCustomCollection, HasStats;

    protected $table = 'player_season';

    /**
     * Specify the tenant columns to use for this model
     * This always ignores the season tenant check
     *
     * @var array
     */
    protected $tenantColumns = ['site_id'];

    public function getNameAttribute()
    {
        return $this->player->name;
    }

    public function getNameKeyAttribute()
    {
        return $this->player->name_key;
    }

    public function player()
    {
        return $this->belongsTo('App\Models\Player');
    }

    public function season()
    {
        return $this->belongsTo('App\Models\Season');
    }

    public function stats()
    {
        return $this->player->stats()->where('season_id', '=', $this->season_id);
    }

}
