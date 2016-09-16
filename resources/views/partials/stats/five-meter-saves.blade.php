<section class="stat stat--five-meter-saves stat--loading">

    <div class="stat-chart-wrapper">
        <div class="stat-chart-sizer">
            <div class="stat-chart"></div>
        </div>

        <div class="stat-header">
            <h1 class="percent">@number($stats->five_meters_save_percent)</h1>
            <p>{{$stats->five_meters_blocked}}/{{$stats->five_meters_missed}}/{{$stats->five_meters_allowed}}</p>
        </div>
    </div>


    <h2>@lang('stats.five_meters')</h2>
    <h3>@lang('stats.blocked')/@lang('stats.missed')/@lang('stats.allowed')</h3>

    <script type="text/javascript">
        var stats = window.stats || {};
        @if($stats->five_meters_taken_on > 0)
            stats.fiveMeterSaves = {!!json_encode([
                'options' => [
                    'multiple' => true
                ],
                'data' => [
                    [trans('stats.stat'), trans('stats.value')],
                    [trans('stats.blocked'), (int)$stats->five_meters_blocked],
                    [trans('stats.missed'), (int)$stats->five_meters_missed],
                    [trans('stats.allowed'), (int)$stats->five_meters_allowed]
                ],
            ])!!}
        @else
            stats.fiveMeterSaves = {!!json_encode([
                'options' => [
                    'negative' => true
                ],
                'data' => [
                    [trans('stats.stat'), trans('stats.value')],
                    [trans('stats.blocked').'/'.trans('stats.missed').'/'.trans('stats.allowed'), [
                        'v' => 1,
                        'f' => 0
                    ]]
                ]
            ])!!}
        @endif
    </script>
</section>