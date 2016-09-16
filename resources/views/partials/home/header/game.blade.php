{{$game->opponent}} @ <a href="{{$game->location->googleDirectionsLink()}}" title="get directions">{{$game->location->title}} <i class="fa fa-map-marker"></i></a>
<time datetime="@iso($game->start)">
    <span class="day">@day($game->start)</span>
    @time($game->start)
</time>