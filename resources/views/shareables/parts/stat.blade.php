<?php
// 'x' => 526,
// 'y' => 744,
// 'scale' => 1,
// 'negative' => false,
// 'slices' => [33],
// 'prefix' => '+',
// 'value' => '3',
// 'suffix' => '%',
// 'subvalue' => '12/9',
// 'title' => 'Kickouts',
// 'subtitle' => 'Drawn/Called'


$colors = ['#2a82c9', '#f29800', '#2ac95b'];

$scale = isset($scale) ? $scale : 1;
$prefix = isset($prefix) ? $prefix : false;
$suffix = isset($suffix) ? $suffix : false;
$subvalue = isset($subvalue) ? $subvalue : false;
$subtitle = isset($subtitle) ? $subtitle : false;
$negative = isset($negative) ? $negative : false;
$slices = isset($slices) ? $slices : [0];

// offset x so its more cenetred on the val and ignores pre/post
$valX = 122;
if ($prefix) { $valX -= 10; }
if ($suffix) { $valX += 10; }

$top = 25;
$first = true;
$offset = 0;
$calcOffset = function ($prevVal, $thisVal) use (&$first, &$offset, $top, $negative) {
    if ($first) {
        $first = false;
        return $offset = $negative ? ($top + $thisVal) : $top;
    }

    return $offset = $negative ?
        $offset + $thisVal :
        $offset - $prevVal;
}

?>
<g class="stat" transform="matrix({!! $scale !!} 0 0 {!! $scale !!} {!! $x !!} {!! $y !!})">
    <g class="graph" transform="matrix(5.75 0 0 5.75 0 0)">
        <circle cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#b2b2b2" stroke-width="2"></circle>

        @foreach($slices as $val)
            <circle cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke-width="2"
                    stroke="{!! $colors[$loop->index] !!}"
                    stroke-dasharray="{!! $val !!} {!! 100 - $val !!}"
                    stroke-dashoffset="{!! $calcOffset($loop->index ? $slices[$loop->index - 1] : null, $val) !!}">
            </circle>
        @endforeach
    </g>
    <g class="stat-text--mid">
        <text class="stat-text--number {!! strlen($value) > 3 ? 'stat-text--long' : '' !!}" x="{!! $valX !!}" y="150">
            @if($prefix)<tspan class="stat-text--prefix">{!! $prefix !!}</tspan>@endif{!! $value !!}@if($suffix)<tspan class="stat-text--suffix">{!! $suffix !!}</tspan>@endif
        </text>
        @if($subvalue)<text class="stat-text--number-sub" x="122" y="177">{!! $subvalue !!}</text>@endif
    </g>
    <g class="stat-text--bottom">
        <text class="stat-text--title" x="122" y="262">{!! $title !!}</text>
        @if($subtitle)<text class="stat-text--title-sub" x="122" y="285">{!! $subtitle !!}</text>@endif
    </g>
</g>