<section class="stat stat--kickouts stat--loading">

    <div class="stat-chart-wrapper">
        <div class="stat-chart-sizer">
            <div class="stat-chart"></div>
        </div>

        <div class="stat-header">
            <h1 class="{{ $stats->kickouts_drawn_to_called > 0 ? 'positive' : ($stats->kickouts_drawn_to_called < 0 ? 'negative' : '') }}">{{ str_replace('-', '', $stats->kickouts_drawn_to_called) }}</h1>

            <p>{{ $stats->kickouts_drawn }}/{{ $stats->kickouts }}</p>
        </div>
    </div>

    <h2>@lang('stats.kickouts')</h2>
    <h3>@lang('stats.drawn')/@lang('stats.called')</h3>


    <script type="text/javascript">
        var stats = window.stats || {};
        {{-- at least 1 kickout or kickout drawn--}}
        @if($stats->kickouts_drawn > 0 || $stats->kickouts > 0)
            @if($stats->kickouts_drawn > $stats->kickouts)
                {{-- drawn more, make it positive --}}
                stats.kickouts = {!!json_encode([
                    'data' => [
                        [trans('stats.stat'), trans('stats.value')],
                        [trans('stats.drawn'), (int) $stats->kickouts_drawn],
                        [trans('stats.called'), (int) $stats->kickouts]
                    ]
                ])!!}
            @else
                {{-- called more, negative --}}
                stats.kickouts = {!!json_encode([
                    'options' => [
                        'negative' => true
                    ],
                    'data' => [
                        [trans('stats.stat'), trans('stats.value')],
                        [trans('stats.called'), (int) $stats->kickouts],
                        [trans('stats.drawn'), (int) $stats->kickouts_drawn]
                    ]
                ])!!}
            @endif
        @else
            {{-- nothing, just grey --}}
            stats.kickouts = {!!json_encode([
                'options' => [
                    'negative' => true
                ],
                'data' => [
                    [trans('stats.stat'), trans('stats.value')],
                    ['v', 1],
                    ['f', 0]
                ]
            ])!!}
        @endif
    </script>
</section>