@extends('game')

@section('title')
    @lang('game.photos') - {{$game->title}} -
@endsection

@section('game-content')

    <div class="page-section game-section container">
        <div class="album-photos full-gallery" data-gallery-path="@route('gallery.album', ['album' => $game->album->id])">
            <div class="recap-loader">
                <div class="loading bg--dark bg--grid-small">
                    <div class="loader"></div>
                    <h1>@lang('misc.loading')</h1>
                </div>
            </div>
        </div>
    </div>


@endsection