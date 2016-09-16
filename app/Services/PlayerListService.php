<?php
/**
 * Created by PhpStorm.
 * User: Duby
 * Date: 8/17/2016
 * Time: 10:22 PM
 */

namespace App\Services;


use App\Models\ActiveSeason;
use App\Models\PlayerSeason;
use Illuminate\Support\Facades\Cache;

class PlayerListService
{

    /**
     * @var [PlayerSeason] players
     */
    protected $playerList;

    /**
     * PlayerListService constructor.
     * @param PlayerSeason $players
     */
    public function __construct(PlayerSeason $players, ActiveSeason $activeSeason)
    {
        $this->playerList = Cache::rememberForever('playerlist-'.$activeSeason->id, function() use ($players, $activeSeason) {
            return $players->with('player')
                ->select('player_season.*')
                ->join('players', 'players.id', '=', 'player_season.player_id')
                ->where('season_id', '=', $activeSeason->id)
                ->orderByRaw('sort IS NULL')
                ->orderBy('sort', 'asc')
                ->orderBy('players.first_name', 'asc')
                ->get()
                ->groupBySet('team');
        });
    }

    public function team($team)
    {
        return $this->playerList->get(strtoupper($team));
    }

    public function all()
    {
        return $this->playerList;
    }


}