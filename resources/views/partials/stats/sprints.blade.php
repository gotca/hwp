<section class="stat stat--sprints stat--loading">

    <div class="stat-chart-wrapper">
        <div class="stat-chart-sizer">
            <div class="stat-chart"></div>
        </div>

        <div class="stat-header">
            <h1 class="percent">@number($stats->sprints_percent)</h1>
            <p>{{$stats->sprints_won}}/{{$stats->sprints_taken}}</p>
        </div>
    </div>

    <h2>@lang('stats.sprints')</h2>


    <script type="text/javascript">
        var stats = window.stats || {};
        stats.sprints = {!!json_encode([
            'data' => [
                [trans('stats.stat'), trans('stats.value')],
                [trans('stats.won'), (int) $stats->sprints_won],
                [trans('stats.lost'), $stats->sprints_taken - $stats->sprints_won]
            ],
        ])!!}
    </script>
</section>