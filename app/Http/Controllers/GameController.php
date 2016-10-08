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

        $players = $game->stats()->with('player.seasons')->get();
        $goalies = $players->filter(function($stat) use($namesByKey) {
            return $stat->saves > 0 || $stat->goals_allowed > 0;
        });

        return view('partials.game.stats', compact('game', 'headerPhoto', 'stats', 'players', 'goalies', 'statusUs', 'statusThem'));
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
