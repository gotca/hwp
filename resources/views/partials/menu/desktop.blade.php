<nav id="desktop-menu">
    <ul>
        <li class="players has-subs super-subs">
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

        @if(auth()->check())
            <li class="admin-menu has-subs">
                <a href="@route('admin')">@lang('menu.admin')</a>
                <section>
                    @include('partials.menu.admin')
                </section>
            </li>
        @else
            <li class="admin-menu"><a href="@route('login')">@lang('menu.login')</a></li>
        @endif
    </ul>
</nav>