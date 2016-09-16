<section class="stat stat--saves stat--loading">

    <div class="stat-chart-wrapper">
        <div class="stat-chart-sizer">
            <div class="stat-chart"></div>
        </div>

        <div class="stat-header">
            <h1 class="percent">@number($stats->save_percent)</h1>
            <p>{{$stats->saves}}/{{$stats->goals_allowed}}</p>
        </div>
    </div>

    <h2>@lang('stats.saves')</h2>

    <script type="text/javascript">
        var stats = window.stats || {};
        @if($stats->saves > 0 || $stats->goals_allowed > 0)
            stats.saves = {!!json_encode([
                'data' => [
                    [trans('stats.stat'), trans('stats.value')],
                    [trans('stats.saves'), (int) $stats->saves],
                    [trans('stats.goals_allowed'), (int) $stats->goals_allowed]
                ],
            ])!!}
        @else
            {{-- nothing, grey --}}
            stats.saves = {!!json_encode([
                'options' => [
                    'negative' => true
                ],
                'data' => [
                    [trans('stats.stat'), trans('stats.value')],
                    [trans('stats.saves').'/'.trans('stats.goals_allowed'), [
                        'v' => 1,
                        'f' => 0
                    ]]
                ]
            ])!!}
        @endif
    </script>
</section>