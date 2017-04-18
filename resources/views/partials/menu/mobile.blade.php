@inject('playerList', 'App\Services\PlayerListService')

{{-- http://tympanus.net/codrops/2013/08/13/multi-level-push-menu/ --}}
<nav id="mp-menu" class="mp-menu">
    <div class="mp-level">
        <header>
            <h2>@lang('menu.mainMenu')</h2>
        </header>
        <div class="icon"><i class="fa fa-home"></i></div>
        <ul>
            <li>
                <a href="@route('home')">@lang('menu.home')</a>
            </li>

            <li class="mp-has-subs">
                <a href="@route('playerlist')">@lang('menu.players')</a>
                <div class="mp-level">
                    <header>
                        <h2>@lang('menu.players')</h2>
                    </header>
                    <div class="icon"><i class="fa fa-rebel"></i></div>
                    <a class="mp-back" href="#">back</a>
                    <ul>
                        @foreach(['V', 'JV', 'STAFF'] as $team)
                            <li class="mp-has-subs">
                                <a href="#">@lang('misc.'.$team)</a>
                                <div class="mp-level">
                                    <header>
                                        <h2>@lang('misc.'.$team)</h2>
                                    </header>
                                    <a class="mp-back" href="#">back</a>
                                    <ul class="team team--{{$team}}">
                                        @foreach($playerList->team($team) as $player)
                                            <li>
                                                <a href="@route('players', ['nameKey' => $player->nameKey])">
                                                    <span class="player--number">{{$player->number}}</span>
                                                    <span class="player--name">{{$player->name}}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </li>

            <li>
                <a href="@route('schedule')">@lang('menu.schedule')</a>
            </li>

            <li>
                <a href="@route('albumlist')">@lang('menu.photos')</a>
            </li>


            @if(auth()->check())
                <li class="admin-menu mp-has-subs">
                    <a href="@route('admin')">@lang('menu.admin')</a>
                    <div class="mp-level">
                        <header>
                            <h2>@lang('menu.admin')</h2>
                        </header>
                        <div class="icon"><i class="fa fa-gear"></i></div>
                        <a class="mp-back" href="#">back</a>

                        @include('partials.menu.admin')

                    </div>
                </li>
            @else
                <li class="admin-menu"><a href="@route('login')">@lang('menu.login')</a></li>
            @endif

        </ul>
    </div>
</nav>