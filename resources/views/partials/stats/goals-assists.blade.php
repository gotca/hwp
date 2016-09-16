<section class="stat stat--assists stat--loading">

    <div class="stat-chart-wrapper">
        <div class="stat-chart-sizer">
            <div class="stat-chart"></div>
        </div>

        <div class="stat-header">
            <h1>{{$stats->goals}}/{{$stats->assists}}</h1>
        </div>
    </div>

    <h2>@lang('stats.goals')/@lang('stats.assists')</h2>


    <script type="text/javascript">
        var stats = window.stats || {};
        {{-- some goals or assists --}}
        @if($stats->goals > 0 || $stats->assist > 0)
            stats.assists = {!!json_encode([
                'options' => [
                    'multiple' => true
                ],
                'data' => [
                    [trans('stats.stat'), trans('stats.value')],
                    [trans('stats.goals'), (int) $stats->goals],
                    [trans('stats.assists'), (int) $stats->assists]
                ],
            ])!!}
        @else
            stats.assists = {!!json_encode([
                'options' => [
                    'negative' => true
                ],
                'data' => [
                    [trans('stats.stat'), trans('stats.value')],
                    [trans('stats.goals').'/'.trans('stats.assists'), [
                        'v' => 1,
                        'f' => 0
                    ]]
                ]
            ])!!}
        @endif
    </script>
</section>