<section class="stat stat--shoot-out-saves stat--loading">

    <div class="stat-chart-wrapper">
        <div class="stat-chart-sizer">
            <div class="stat-chart"></div>
        </div>

        <div class="stat-header">
            <h1 class="percent">@number($stats->shoot_out_save_percent)</h1>
            <p>{{$stats->shoot_out_blocked}}/{{$stats->shoot_out_missed}}/{{$stats->shoot_out_allowed}}</p>
        </div>
    </div>


    <h2>@lang('stats.shoot_outs')</h2>
    <h3>@lang('stats.blocked')/@lang('stats.missed')/@lang('stats.allowed')</h3>

    <script type="text/javascript">
        var stats = window.stats || {};
        @if($stats->shoot_out_taken_on)
            stats.shootOutSaves = {!!json_encode([
                'options' => [
                    'multiple' => true
                ],
                'data' => [
                    [trans('stats.stat'), trans('stats.value')],
                    [trans('stats.blocked'), (int) $stats->shoot_out_blocked],
                    [trans('stats.missed'), (int) $stats->shoot_out_missed],
                    [trans('stats.allowed'), (int) $stats->shoot_out_allowed]
                ]
            ])!!}
        @else
            stats.shootOutSaves = {!!json_encode([
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