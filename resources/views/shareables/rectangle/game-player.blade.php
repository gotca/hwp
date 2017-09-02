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
        'y' => 603,
        'height' => 240
    ])

    @include('shareables.parts.scores', [
        'x' => 208,
        'y' => 530,
        'game' => $game
    ])

    @if($game->badge)
        @include('shareables.parts.badge', [
            'x' => 126,
            'y' => 865.5,
            'showTitle' => true,
            'badge' => $game->badge
        ])
    @endif

    <rect x="0" y="1662" width="100%" height="258" fill="url(#blue-trans-bottom)"></rect>

    @if(strlen($player->name) >= 18)
        <text class="player-name" filter="url(#shadow)">
            <tspan class="name--first" x="54" y="1415" >{!! $player->player->first_name !!}</tspan>
            <tspan class="name--last" x="54" y="1545" >{!! $player->player->last_name !!}</tspan>
        </text>
    @else
        <text class="player-name" x="54" y="1545" filter="url(#shadow)">
            <tspan class="name--first">{!! $player->player->first_name !!}</tspan>
            <tspan class="name--last">{!! $player->player->last_name !!}</tspan>
        </text>
    @endif

    @if($charts)
        @foreach($charts as $chartData)
            @include('shareables.parts.stat', array_merge($chartData, [
                'x' => (37 + ($loop->index * 251.5)),
                'y' => 1590
            ]))
        @endforeach
    @endif


@endsection