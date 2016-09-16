<section class="stat stat--steals-turnovers stat--loading">

    <div class="stat-chart-wrapper">
        <div class="stat-chart-sizer">
            <div class="stat-chart"></div>
        </div>

        <div class="stat-header">
            <h1 class="{{ $stats->steals_to_turnovers > 0 ? 'positive' : ($stats->steals_to_turnovers < 0 ? 'negative' : '') }}">{{ str_replace('-', '', number_format($stats->steals_to_turnovers)) }}</h1>
            <p>{{ $stats->steals }}/{{ $stats->turnovers }}</p>
        </div>
    </div>

    <h2>@lang('stats.steals')/@lang('stats.turnovers')</h2>


    <script type="text/javascript">
        var stats = window.stats || {};
        {{-- at least 1 steal of turnover --}}
        @if($stats->steals > 0 || $stats->turn_overs > 0)
            @if($stats->steals > $stats->turn_overs)
                {{-- more steal than turnovers --}}
                stats.stealsTurnovers = {!!json_encode([
                    'data' => [
                        [trans('stats.stat'), trans('stats.value')],
                        [trans('stats.steals'), (int) $stats->steals],
                        [trans('stats.turnovers'), (int) $stats->turnovers]
                    ],
                ])!!}
            @else
                {{-- more turnovers, draw it negative --}}
                stats.stealsTurnovers = {!!json_encode([
                    'options' => [
                        'negative' => true
                    ],
                    'data' => [
                        [trans('stats.stat'), trans('stats.value')],
                        [trans('stats.turnovers'), (int) $stats->turnovers],
                        [trans('stats.steals'), (int) $stats->steals]
                    ],
                ])!!}
            @endif
        @else
            {{-- no steals or turnovers, grey --}}
            stats.stealsTurnovers = {!!json_encode([
                'options' => [
                    'negative' => true
                ],
                'data' => [
                    [trans('stats.stat'), trans('stats.value')],
                    [trans('stats.steals').'/'.trans('stats.turnovers'), [
                        'v' => 1,
                        'f' => 0
                    ]]
                ]
            ])!!}
        @endif
    </script>
</section>