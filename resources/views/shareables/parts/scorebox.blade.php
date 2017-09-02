<g class="score-box" transform="translate({!! $x !!} {!! $y !!})">

    <rect style="fill:#FFFFFF;" filter="url(#shadow)" width="318" height="384"></rect>
    <rect class="result--{!! $result !!}" width="318" height="130"></rect>
    <rect class="grid" width="318" height="130"></rect>

    @if(is_array($team))
        <text class="team" transform="translate(159 {!! 71 - (10 * (count($team) - 1)) !!})">
            @foreach($team as $part)
                <tspan x="0" y="{!! $loop->index * 50 !!}">{!! $part !!}</tspan>
            @endforeach
        </text>
    @else
        <text class="team" transform="translate(159 71)">{!! $team !!}</text>
    @endif

    <text class="score" transform="translate(159 304)">{!! $score !!}</text>

</g>