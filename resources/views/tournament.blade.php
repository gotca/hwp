@extends('layouts.app')

@section('title')
    {{$tournament->title}} -
@endsection

@section('content')
    <article class="page--tournament">

        <header class="page-header header--small">
            <div class="bg-elements">
                <div class="bg--gradient"></div>
                <div class="bg--img"></div>
            </div>
            <div class="container">
                <h1>{{$tournament->title}}</h1>
                <h2>@dateSpan($tournament->start, $tournament->end)</h2>
            </div>
        </header>

        @if($upcoming->count())
            <section class="page-section tournament--upcoming bg--light bg--grid">
                <div class="container">
                    <header class="divider--bottom text-align--center">
                        <h1><span class="text--muted">@lang('tournament.upcoming')</span> @lang('tournament.games')</h1>
                    </header>

                    <div class="row center-xs">
                        @foreach($upcoming as $date => $games)
                            <section class="col-xs-6 col-md-4">
                                <header class="bg--grid bg--bright">
                                    <h1>@day(new \Carbon\Carbon($date))</h1>
                                </header>

                                <div class="body">
                                    <table>
                                        @foreach($games as $game)
                                        <tr class="upcoming-event">
                                            <td class="upcoming-time">@time($game->start)</td>
                                            <td class="upcoming-team">{{$game->team}}</td>
                                            <td class="upcoming-opponent">{{$game->opponent}}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td class="upcoming-location" colspan="3">
                                                <a href="{{$game->location->googleDirectionsLink()}}"><i class="fa fa-map-marker"></i> {{$game->location->title}}</a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </section>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <div class="page-section container">
            <header class="divider--bottom text-align--center">
                <h1><span class="text--muted">@lang('tournament.all')</span> @lang('tournament.games')</h1>
            </header>

            <table class="table table--striped schedule table--collapse">
                <thead class="bg--grid bg--dark">
                    <tr>
                        <th class="schedule-date">@lang('tournament.date')</th>
                        <th class="schedule-team">@lang('tournament.team')</th>
                        <th class="schedule-title">@lang('tournament.title')</th>
                        <th class="schedule-location">@lang('tournament.location')</th>
                        <th class="schedule-result">@lang('tournament.result')</th>
                        <th class="schedule-score">@lang('tournament.score')</th>
                        <th class="schedule-btns"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($games as $game)
                        <tr data-timestamp="{{$game->start->timestamp}}">
                            <td class="schedule-date" data-title="@lang('schedule.date')">
                                @dayWithDateTime($game->start)
                            </td>
                            <td class="schedule-team" data-title="@lang('schedule.team')">
                                {{$game->team}}
                            </td>
                            <td class="schedule-title" data-title="@lang('schedule.title')">
                                {{$game->opponent}}
                            </td>
                            <td class="schedule-location" data-title="@lang('schedule.location')">
                                <a href="{{$game->location->googleDirectionsLink()}}" title="@lang('misc.directions')">
                                    {{$game->location->title}}
                                </a>
                            </td>
                            <td class="schedule-result schedule--{{$game->status()}}" data-title="@lang('schedule.result')">
                                {{$game->status()}}
                            </td>
                            <td class="schedule-score" data-title="@lang('schedule.score')">
                                @if($game->score_us)
                                    {{$game->score_us}} - {{$game->score_them}}
                                @endif
                            </td>
                            <td class="schedule-btns">
                                <div class="btn-group btn-group--end">
                                    @if($game->box_stats_count)
                                        <a class="btn" href="@route('game.stats', ['id'=>$game->id])" title="@lang('misc.stats')">
                                            <i class="fa fa-line-chart"></i>
                                        </a>
                                    @endif
                                    @if($game->album_count)
                                        <a class="btn" href="@route('game.photos', ['id'=>$game->id])" title="@lang('misc.photos')">
                                            <i class="fa fa-picture-o"></i>
                                        </a>
                                    @endif
                                    @if($game->updates_count)
                                        <a class="btn" href="@route('game.recap', ['id'=>$game->id])" title="@lang('misc.recap')">
                                            <i class="fa fa-ticket"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </article>
@endsection