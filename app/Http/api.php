<?php

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

    return Response::json($data);
});