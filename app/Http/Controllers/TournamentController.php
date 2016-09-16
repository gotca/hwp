<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;

use App\Http\Requests;

class TournamentController extends Controller
{
    public function tournament(Tournament $tournament)
    {
        $games = $tournament->games()
            ->withCount(['album', 'boxStats', 'updates'])
            ->orderBy('start', 'asc')
            ->get();

        $upcoming = $games->where('end', '>=', time())
            ->groupByDate('start', 'Ymd');

        return view('tournament', compact('tournament', 'games', 'upcoming'));
    }
}
