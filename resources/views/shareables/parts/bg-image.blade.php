<?php
$cw = $canvas['width'];
$ch = $canvas['height'];

// - 10 to account for the blur
$ratios = [
    'width' => $cw / ($photo->width - 10),
    'height' => $ch / ($photo->height - 10)
];

$scale = max($ratios['width'], $ratios['height']);

$width = $photo->width * $scale;
$height = $photo->height * $scale;
$x =  -(($width - $cw) / 2);
$y = -(($height - $ch) / 2);
?>

<image id="bg" style="overflow:visible;"
   width="{!! $photo->width !!}" height="{!! $photo->height !!}"
   xlink:href="{!! $photo->photo !!}"
   transform="matrix({!! $scale !!} 0 0 {!! $scale !!} {!! $x !!} {!! $y !!})"
   filter="url(#blur)"
></image>