@extends('master')

@section('content')
    <article class="page--game">

        @include('partials.game.header')

        @yield('game-content')

    </article>
@endsection