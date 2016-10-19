@extends('layouts.app')

@section('title')
    Photo Albums -
@endsection

@section('content')
    <article class="page--album-list">

        <header class="page-header header--small">
            <div class="bg-elements">
                <div class="bg--gradient"></div>
                <div class="bg--img"
                    @if($cover)
                        style="background-image: url({{ $cover->photo  }})"
                    @endif
                ></div>
            </div>
            <div class="container">
                <h1>@lang('photos.photoAlbums')</h1>
            </div>
        </header>

        <div class="page-section container">
            <div class="album-list">
                @each('partials.photo-album', $albums, 'album', 'partials.nothing-here-yet')
            </div>
        </div>

    </article>
@endsection