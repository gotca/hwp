<?php

use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('vr-data/{player}', function(\App\Models\Player $player) {
    $data = DB::table('stats')
        ->leftJoin('games', 'stats.game_id', '=', 'games.id')
        ->leftJoin('seasons', 'stats.season_id', '=', 'seasons.id')
        ->select('stats.*', 'games.*', 'seasons.title AS season_title', 'seasons.short_title AS season_short_title')
        ->where('stats.player_id', $player->id)
        ->get();

    $data->each(function($stat) {
        if ($stat->score_us > $stat->score_them) {
            $stat->game_status = Game::WIN;
        } elseif($stat->score_us < $stat->score_them) {
            $stat->game_status = Game::LOSS;
        } else {
            $stat->game_status = Game::TIE;
        }

        $stat->start_ts = strtotime($stat->start);
        if ($stat->start_ts === false) {
            $stat->start_ts = strtotime('August 1 20' . $stat->season_short_title);
        }

        $stat->start_format = date('M jS, Y', $stat->start_ts);
    });

    return Response::json($data);
});