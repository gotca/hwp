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
        'x' => 50,
        'y' => 50
    ])

    @include('shareables.parts.url', [
        'x' => 700,
        'y' => 50
    ])

    @include('shareables.parts.stripe', [
        'y' => 339,
        'height' => 172
    ])

    @include('shareables.parts.scores', [
        'scale' => 0.8399,
        'x' => 254,
        'y' => 257,
        'game' => $game
    ])

    @if($game->badge)
        @include('shareables.parts.badge', [
            'scale' => .84,
            'x' => 192.5,
            'y' => 538.5,
            'showTitle' => true,
            'badge' => $game->badge
        ])
    @endif

    <rect x="0" y="822" width="100%" height="258" fill="url(#blue-trans-bottom)"></rect>

    @if($charts)
        @foreach($charts as $chartData)
            @include('shareables.parts.stat', array_merge($chartData, [
                'x' => (37 + ($loop->index * 251.5)),
                'y' => 744
            ]))
        @endforeach
    @endif



@endsection