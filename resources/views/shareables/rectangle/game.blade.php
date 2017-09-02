@extends('shareables.base')

@section('content')

    @if($photo)
        @include('shareables.parts.bg-image', [
            'photo' => $photo,
            'canvas' => $dimensions
        ])
    @else
        @include('shareables.parts.bg-pattern', [
            'pattern' => $pattern,
            'dimensions' => $dimensions
        ])
    @endif

    @include('shareables.parts.logo', [
        'x' => '57.23',
        'y' => '1793'
    ])

    @include('shareables.parts.url', [
        'x' => '699.5',
        'y' => '1836.22'
    ])

    @include('shareables.parts.stripe', [
        'y' => 720,
        'height' => 240
    ])

    @include('shareables.parts.scores', [
        'scale' => 1.189,
        'x' => 144.06,
        'y' => 617,
        'game' => $game
    ])

    @if($game->badge)
        @include('shareables.parts.badge', [
            'x' => 120,
            'y' => 598,
            'showTitle' => true,
            'badge' => $game->badge
        ])
    @endif

@endsection