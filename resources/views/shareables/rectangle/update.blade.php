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

    <?php
    $lineHeight = 150;
    $padding = 50;
    $offset = 160;
    $stripeHeight = ($lineHeight * count($lines) + $padding * 2);
    $stripeY = (($dimensions['height'] - $stripeHeight) / 2) - $offset;
    ?>
    @include('shareables.parts.stripe', [
        'y' => $stripeY,
        'height' => $stripeHeight
    ])

    <g transform="translate(0 {!! $stripeY + $lineHeight + ($padding / 2) !!})">
        @foreach($lines as $line)
            <text class="update update--bigger"
                  x="50%" y="{!! $loop->index * $lineHeight !!}"
                  filter="url(#shadow)"
            >{!! $line !!}</text>
        @endforeach
    </g>

    <text class="meta" x="50%" y="{!! $stripeY + $stripeHeight + $padding !!}">
        <tspan class="meta-opponent">{!! $opponent !!}</tspan>
        <tspan class="meta-score">&nbsp;&nbsp;{!! implode(' - ', $score) !!}&nbsp;&nbsp;</tspan>
        <tspan class="meta-$quarter">{!! $quarter !!}</tspan>
    </text>

@endsection