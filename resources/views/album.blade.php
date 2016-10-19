@extends('layouts.app')

@section('title')
    {{$album->title}} -
@endsection

@section('content')

    <article class="page--album">

        <header class="page-header header--small">
            <div class="bg-elements">
                <div class="bg--gradient"></div>
                <div class="bg--img"
                     @if($album->cover->photo)
                         style="background-image: url({{ $album->cover->photo  }})"
                    @endif
                ></div>
            </div>
            <div class="container">
                <h1>{{$album->title}}</h1>
            </div>
        </header>

        @if($game)
            <section class="page-section results">
                <div class="bg-elements bg--white">
                    <div class="bg--inner-shadow"></div>
                    <div class="bg--grid"></div>
                </div>
                <div class="container">
                    <header class="divider--bottom text-align--center">
                        <h1><span class="text--muted">@lang('misc.game')</span> @lang('misc.Results')</h1>
                    </header>

                    <div class="results-wrapper container">
                        <div class="row center-xs">
                            <div class="col-xs-12 col-md-3">
                                @include('partials.result', ['result' => $game])
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="page-section container">
            <div class="album-photos full-gallery" data-gallery-path="@route('gallery.album', ['album' => $album->id])">
                <div class="recap-loader">
                    <div class="loading bg--dark bg--grid-small">
                        <div class="loader"></div>
                        <h1>@lang('misc.loading')</h1>
                    </div>
                </div>
            </div>
        </div>

    </article>

@endsection