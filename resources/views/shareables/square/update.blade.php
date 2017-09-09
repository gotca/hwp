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
        'y' => '964.653'
    ])

    @include('shareables.parts.url', [
        'x' => '701.167',
        'y' => '1001.226'
    ])

    <?php
    $lineHeight = 110;
    $padding = 45;
    $offset = 80;
    $stripeHeight = ($lineHeight * count($lines) + $padding * 2);
    $stripeY = (($dimensions['height'] - $stripeHeight) / 2) - $offset;
    ?>
    @include('shareables.parts.stripe', [
        'y' => $stripeY,
        'height' => $stripeHeight
    ])


    <g transform="translate(0 {!! $stripeY + $lineHeight + ($padding / 2) !!})">
        @foreach($lines as $line)
            <text class="update"
                  x="50%" y="{!! $loop->index * $lineHeight !!}"
                  filter="url(#shadow)"
            >{!! $line !!}</text>
        @endforeach
    </g>

    <text class="meta" x="50%" y="{!! $stripeY + $stripeHeight + $padding !!}">
        <tspan class="meta-opponent">{!! $opponent !!}</tspan>
        <tspan class="meta-score">&#160;&#160;{!! implode(' - ', $score) !!}&#160;&#160;</tspan>
        <tspan class="meta-$quarter">{!! $quarter !!}</tspan>
    </text>

@endsection