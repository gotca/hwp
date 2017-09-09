<?php
$scale = isset($scale) ? $scale : 1;
?>
<g class="badge" transform="matrix({!! $scale !!} 0 0 {!! $scale !!} {!! $x !!} {!! $y !!})">
    @if($showTitle)
        <text class="badge-title" transform="translate(122 99)">{!! $badge['title'] !!}</text>
    @endif
    <image style="overflow:visible;"
           width="130" height="147"
           xlink:href="{!! asset('/') !!}badges/{!! $badge['image'] !!}"></image>
</g>