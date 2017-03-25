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
