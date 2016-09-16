<section class="page-section results bg--dark bg--grid">
    <div class="container">
        <header class="divider--bottom text-align--center">
            <h1>@lang('misc.Latest') <span class="text--muted">@lang('misc.Results')</span> </h1>
        </header>

        <div class="results-wrapper container">
            <div class="row">
                @forelse($results as $result)
                    <div class="col-xs-12 col-sm-6 col-md-3">
                        @include('partials.result', ['result' => $result])
                    </div>
                @empty
                    @include('partials.nothing-here-yet')
                @endforelse
            </div>

            <p class="text-align--center"><a href="@route('schedule')" title="@lang('misc.viewFullSchedule')">@lang('misc.viewFullSchedule')</a></p>
        </div>
    </div>
</section>