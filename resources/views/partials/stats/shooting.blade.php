<section class="stat stat--shooting stat--loading">

    <div class="stat-chart-wrapper">
        <div class="stat-chart-sizer">
            <div class="stat-chart"></div>
        </div>

        <div class="stat-header">
            <h1 class="percent">@number($stats->shooting_percent)</h1>

            <p>{{ $stats->goals }}/{{ $stats->shots }}</p>
        </div>
    </div>

    <h2>@lang('stats.shooting_percent')</h2>

	<script type="text/javascript">
        var stats = window.stats || {};
        @if($stats->shots > 0)
            stats.shooting = {!!json_encode([
                'data' => [
                    [trans('stats.stat'), trans('stats.value')],
                    [trans('stats.made'), (int)$stats->goals],
                    [trans('stats.missed-blocked'), $stats->shots - $stats->goals]
                ],
            ])!!};
        @else
            stats.shooting = {!!json_encode([
                'options' => [
                    'negative' => true
                ],
                'data' => [
                    [trans('stats.stat'), trans('stats.value')],
                    [trans('stats.shots'), [
                        'v' => 1,
                        'f' => 0
                    ]],
                ],
            ])!!};
        @endif
    </script>
</section>