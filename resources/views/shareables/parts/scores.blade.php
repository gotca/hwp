<?php
$scale = isset($scale) ? $scale : 1;
?>
<g id="scores" transform="matrix({!! $scale !!} 0 0 {!! $scale !!} {!! $x !!} {!! $y !!})">
    @include('shareables.parts.scorebox', [
        'x' => 0,
        'y' => 0,
        'team' => $game->us,
        'score' => $game->score_us,
        'result' => $game->status()
    ])

    @include('shareables.parts.scorebox', [
        'x' => 348,
        'y' => 0,
        'team' => $game->opponent,
        'score' => $game->score_them,
        'result' => \App\Models\Game::oppositeStatus($game->status())
    ])
</g>