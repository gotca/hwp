<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Stat;
use Illuminate\Http\Request;

use App\Http\Requests;

class GameController extends Controller
{
    
    public function recap(Game $game)
    {
        $headerPhoto = $this->getCover($game);
        return view('partials.game.recap', compact('game', 'headerPhoto'));
    }

    public function stats(Game $game)
    {
        $headerPhoto = $this->getCover($game);
        $stats = $game->boxStats->json;
        $namesByKey = [];

        switch($game->status()) {
            case Game::WIN:
                $statusUs = Game::WIN;
                $statusThem = Game::LOSS;
                break;

            case Game::LOSS:
                $statusUs = Game::LOSS;
                $statusThem = Game::WIN;
                break;

            case Game::TIE:
            default:
                $statusUs = $statusThem = Game::TIE;
        }

        // convert the player stats to actual stat objects for the generated values
        foreach($stats->stats as $k => &$v) {
            $namesByKey[$k] = '<a href="'.route('players', ['name_key'=>$v->name_key]).'">#' . $v->number. ' ' . $v->first_name . ' ' .$v->last_name.'</a>';
            $v = get_object_vars($v);
            $v = new Stat($v);
        }

        $goalies = [];
        foreach($stats->stats as $namekey => $player) {
            if ($player->saves > 0 || $player->goals_allowed > 0) {
                $goalies[$namekey] = $player;
            }
        }

        return view('partials.game.stats', compact('game', 'headerPhoto', 'stats', 'goalies', 'namesByKey', 'statusUs', 'statusThem'));
    }

    public function photos(Game $game)
    {
        $headerPhoto = $this->getCover($game);

        return view('partials.game.photos', compact('game', 'headerPhoto'));
    }

    protected function getCover(Game $game)
    {
        try {
            $headerPhoto = $game->album->cover->photo;
        } catch(\Exception $e) {
            $headerPhoto = null;
        }

        return $headerPhoto;
    }
}
