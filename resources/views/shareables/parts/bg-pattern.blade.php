<defs>
    <pattern id="bg-pattern" patternUnits="userSpaceOnUse" width="320" height="320">
        <image xlink:href="{!! $pattern !!}"
               x="0" y="0" width="320" height="320">
        </image>
    </pattern>
</defs>
<rect style="fill: url(#bg-pattern)" x="0" y="0" width="{!! $dimensions['width'] !!}" height="{!! $dimensions['height'] !!}"></rect>