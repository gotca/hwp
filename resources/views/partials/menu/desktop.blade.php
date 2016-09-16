<nav id="desktop-menu">
    <ul>
        <li class="players has-subs">
            <a href="@route('playerlist')" title="">@lang('menu.players')</a>
            @include('partials.playerlist')
        </li>
        <li class="schedule">
            <a href="@route('schedule')" title="@lang('menu.schedule')">@lang('menu.schedule')</a>
        </li>
        {{--<li class="scoring">--}}
        {{--<a href="@route('scoring')" title="@lang('menu.scoring')">@lang('menu.scoring')</a>--}}
        {{--</li>--}}
        <li class="photos">
            <a href="@route('albumlist')" title="@lang('menu.photos')">@lang('menu.photos')</a>
        </li>
    </ul>
</nav>