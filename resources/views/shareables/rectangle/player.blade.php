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
        'y' => 1754
    ])

    @include('shareables.parts.url', [
        'x' => 50,
        'y' => 1836
    ])

    <text class="player-name player-name--large" filter="url(#shadow)">
        <tspan class="name--first" x="50" y="200" >{!! $player->player->first_name !!}</tspan>
        <tspan class="name--last" x="50" y="370" >{!! $player->player->last_name !!}</tspan>
    </text>

    @if($badges)
        @foreach($badges as $badge)
            @include('shareables.parts.badge', [
                'x' => (50 + ($loop->index * 95)),
                'y' => 400,
                'showTitle' => false,
                'badge' => $badge
            ])
        @endforeach
    @endif


    <rect x="784" y="0" width="304" height="100%" fill="url(#blue-trans-right)"></rect>

    @if($charts)
        @foreach($charts as $chartData)
            @include('shareables.parts.stat', array_merge($chartData, [
                'x' => 845,
                'y' => (790 + ($loop->index * 280)),
                'scale' => .9
            ]))
        @endforeach
    @endif



@endsection