<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Auth::routes();



Route::get('/', ['uses' => 'HomeController@index', 'as' => 'home']);

Route::get('/recent', ['uses' => 'HomeController@recent', 'as' => 'recent']);

Route::get('/rankings', ['uses' => 'HomeController@rankings', 'as' => 'rankings']);



Route::get('/players', ['uses' => 'PlayerController@playerList', 'as' => 'playerlist']);

Route::get('players/{player}', ['uses' => 'PlayerController@player', 'as' => 'players']);



Route::get('schedule', ['uses' => 'ScheduleController@index', 'as' => 'schedule']);

Route::get('schedule/subscribe', ['uses' => 'ScheduleController@subscribe', 'as' => 'schedule.subscribe']);

Route::get('ical.ics', function() {
    return redirect()->route('schedule.subscribe');
});



Route::get('game/{game}/recap', ['uses' => 'GameController@recap', 'as' => 'game.recap']);

Route::get('game/{game}/photos', ['uses' => 'GameController@photos', 'as' => 'game.photos']);


Route::get('game/{game}/stats', ['uses' => 'StatController@view', 'as' => 'game.stats']);

Route::get('game/{game}/stats/edit', ['uses' => 'StatController@edit', 'as' => 'game.stats.edit'])->middleware('auth');

Route::post('game/{game}/stats/edit', ['uses' => 'StatController@save', 'as' => 'game.stats.edit'])->middleware('auth');


Route::get('photos', ['uses' => 'AlbumController@index', 'as' => 'albumlist']);

Route::get('photos/{album}', ['uses' => 'AlbumController@photos', 'as' => 'album']);



Route::get('tournaments/{tournament}', ['uses' => 'TournamentController@tournament', 'as' => 'tournament']);



Route::get('notes/{note}', ['uses' => 'NotesController@note', 'as' => 'notes']);



Route::get('gallery/recent/{recent}', ['uses' => 'GalleryController@recent', 'as' => 'gallery.recent']);

Route::get('gallery/album/{album}', ['uses' => 'GalleryController@album', 'as' => 'gallery.album']);

Route::get('gallery/player/{player}', ['uses' => 'GalleryController@playerCareer', 'as' => 'gallery.playerCareer']);

Route::get('gallery/player/{player}/season/{season}', ['uses' => 'GalleryController@playerSeason', 'as' => 'gallery.playerSeason']);


Route::get('parents', ['as' => 'parents', function() {
    return view('parents', [
        'googleFolderID' => resolve('App\Models\ActiveSite')->settings->get('google.folder.id')
    ]);
}]);


Route::group(['middleware' => 'cors'], function() {

    Route::get('shareables/{shape}/game{ext}', ['uses' => 'ShareableController@game', 'as' => 'shareables.game']);

    Route::get('shareables/{shape}/player{ext}', ['uses' => 'ShareableController@player', 'as' => 'shareables.player']);

    Route::get('shareables/{shape}/update{ext}', ['uses' => 'ShareableController@update', 'as' => 'shareables.update']);

});