@extends('game')

@section('title')
    @lang('game.stats') - {{$game->title}} -
@endsection

@section('game-content')

    <section class="page-section game-stats--final bg--dark bg--grid">
        <div class="container">
            <header class="divider--bottom text-align--center">
                <h1><span class="text--muted">@lang('stats.final')</span> @lang('stats.results')</h1>
            </header>

            <div class="row around-xs center-md">
                <div class="col-sm-5 col-md-4">
                    <section class="card result result--{{$statusUs}}">
                        <header class="bg--grid">
                            <h1>Hudsonville</h1>
                        </header>

                        <div class="body">
                            <div class="score">
                                <h2>{{$stats->score[0]}}</h2>
                            </div>
                        </div>

                        <footer class="clearfix">
                            <table class="box-score align--right">
                                <tbody>
                                <tr>
                                    @foreach($stats->boxscore[0] as $score)
                                        <td>{{array_sum((array)$score)}}</td>
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>
                        </footer>
                    </section>
                </div>
                <div class="col-sm-5 col-md-4">
                    <section class="card result result--{{$statusThem}}">
                        <header class="bg--grid">
                            <h1>{{$game->opponent}}</h1>
                        </header>
                        <div class="body">
                            <div class="score">
                                <h2>{{$stats->score[1]}}</h2>
                            </div>
                        </div>

                        <footer class="clearfix">
                            <table class="box-score align--right">
                                <tbody>
                                <tr>
                                    @foreach($stats->boxscore[1] as $score)
                                        <td>{{array_sum((array)$score)}}</td>
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>
                        </footer>
                    </section>
                </div>
            </div>
        </div>
    </section>

    <section class="page-section game-stats--goalie bg--smoke">
        <div class="container">
            <header class="divider--bottom text-align--center">
                <h1><span class="text--muted">@lang('stats.goalie')</span> @lang('stats.stats')</h1>
            </header>

            <?php
            $fields = [
                    'saves' => 0,
                    'goals_allowed' => 0,
                    'save_percent' => 0,
                    'five_meters_taken_on' => 0,
                    'five_meters_blocked' => 0,
                    'five_meters_allowed' => 0,
                    'shoot_out_taken_on' => 0,
                    'shoot_out_blocked' => 0,
                    'shoot_out_allowed' => 0,
            ];
            ?>
            <table class="table table--collapse stats-table game-stats--goalie">
                <thead>
                <tr>
                    <th></th>
                    @foreach($fields as $key => $v)
                        <th>@lang('stats.'.$key)</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($goalies as $stat)
                    <tr>
                        <th>@playerLink($stat->player)</th>
                        @foreach($fields as $key => &$sum)
                            <td class="stat--{{$key}}" data-title="@lang('stats.'.$key)">
                                <span>@number($stat->$key)</span></td>
                            <?php
                            $fields[$key] += $stat->$key;
                            ?>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
                @if(count($goalies) > 1)
                    <?php
                    $totals = new \App\Models\Stat($fields);
                    ?>
                    <tfoot>
                    <tr>
                        <th>@lang('stats.totals')</th>
                        @foreach($fields as $key => $sum)
                            <td class="stat--{{$key}}" data-title="@lang('stats.'.$key)">
                                <span>@number($totals->$key)</span></td>
                        @endforeach
                    </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </section>

    <section class="page-section game-stats--field">
        <div class="container">
            <header class="divider--bottom text-align--center">
                <h1><span class="text--muted">@lang('stats.field')</span> @lang('stats.stats')</h1>
            </header>

            <?php
            $fields = [
                    'goals' => 0,
                    'shots' => 0,
                    'shooting_percent' => 0,
                    'assists' => 0,
                    'steals' => 0,
                    'turnovers' => 0,
                    'steals_to_turnovers' => 0,
                    'blocks' => 0,
                    'kickouts' => 0,
                    'kickouts_drawn' => 0,
                    'five_meters_called' => 0,
                    'five_meters_drawn' => 0,
                    'five_meters_taken' => 0,
                    'five_meters_made' => 0,
                    'five_meters_percent' => 0,
                    'sprints_won' => 0,
                    'sprints_taken' => 0,
                    'shoot_out_taken' => 0,
                    'shoot_out_made' => 0,
            ];
            ?>
            <table class="table table--condensed table--collapse stats-table game-stats--field">
                <thead>
                <tr>
                    <th></th>
                    @foreach($fields as $key => $v)
                        <th>@lang('stats.'.$key)</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($players as $stat)
                    <tr>
                        <th>@playerLink($stat->player)</th>
                        @foreach($fields as $key => &$sum)
                            <td class="stat--{{$key}}" data-title="@lang('stats.'.$key)">
                                <span>@numberOrNothing($stat->$key)</span>
                            </td>
                            <?php
                            $fields[$key] += $stat->$key;
                            ?>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <?php
                    app('debugbar')->info($fields);
                    $totals = new \App\Models\Stat($fields);
                    ?>
                    <th>@lang('stats.totals')</th>
                    @foreach($fields as $key => $sum)
                        <td class="stat--{{$key}}" data-title="@lang('stats.'.$key)"><span>@number($totals->$key)</span>
                        </td>
                    @endforeach
                </tr>
                </tfoot>
            </table>
        </div>
    </section>

    <section class="page-section game-stats--box-score bg--smoke">
        <div class="container">
            <header class="divider--bottom text-align--center">
                <h1><span class="text--muted">@lang('stats.goals')</span> @lang('stats.perQuarter')</h1>
            </header>

            <?php
            $perRow = 4;
            $us = array_chunk($stats->boxscore[0], $perRow);
            $them = array_chunk($stats->boxscore[1], $perRow);
            $i = 0;
            $j = 0;
            ?>
            @foreach($us as $row)
                <div class="row around-xs center-md">
                    @foreach($row as $quarter)
                        <div class="col-xs-12 col-md-3">
                            <section class="card box-score">

                                <header class="bg--dark bg--grid">
                                    <h1>@ordinal(($i * $perRow) + $j + 1)</h1>
                                </header>

                                <table class="body table table--striped">
                                    <tbody>
                                        <tr class="team-name">
                                            <th>Hudsonville</th>
                                            <th>{{array_sum((array)$us[$i][$j])}}</th>
                                        </tr>
                                        @forelse($us[$i][$j] as $nameKey => $goals)
                                            <tr>
                                                <th>@playerLink($nameKey)</th>
                                                <td>{{$goals}}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2">&ndash;</td>
                                            </tr>
                                        @endforelse
                                    </tbody>

                                    <tbody>
                                        <tr class="team-name">
                                            <th>{{$game->opponent}}</th>
                                            <th>{{array_sum((array)$them[$i][$j])}}</th>
                                        </tr>
                                        @forelse($them[$i][$j] as $number => $goals)
                                            <tr>
                                                <th>
                                                    @if($number != "_empty_")
                                                        #{{$number}}
                                                    @else
                                                        {{-- intentionally left blank --}}
                                                    @endif
                                                </th>
                                                <td>{{$goals}}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2">&ndash;</td>
                                            </tr>
                                        @endforelse
                                    </tbody>

                                </table>
                            </section>
                        </div>
                        <?php
                        $j++;
                        ?>
                    @endforeach
                </div>
                <?php
                $i++;
                $j = 0;
                ?>
            @endforeach
        </div>
    </section>

    <section class="page-section game-stats--advantages-converted">
        <div class="container">
            <header class="divider--bottom text-align--center">
                <h1><span class="text--muted">@lang('stats.advantages')</span> @lang('stats.converted')</h1>
            </header>

            <div class="row around-xs center-md">
                <div class="col-sm-5 col-md-4">
                    <section class="card result">
                        <header class="bg--grid bg--dark">
                            <h1>Hudsonville</h1>
                        </header>
                        <div class="body">
                            <div class="score">
                                <div>{{$stats->advantage_conversion[0]->converted}}</div>
                                <div>{{$stats->advantage_conversion[0]->drawn}}</div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-sm-5 col-md-4">
                    <section class="card result">
                        <header class="bg--grid bg--dark">
                            <h1>{{$game->opponent}}</h1>
                        </header>
                        <div class="body">
                            <div class="score">
                                <div>{{$stats->advantage_conversion[1]->converted}}</div>
                                <div>{{$stats->advantage_conversion[1]->drawn}}</div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>

@endsection