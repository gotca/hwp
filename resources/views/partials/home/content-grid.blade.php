<section class="page-section recent-content container">

    <div class="recent-grid">

        <div class="recent recent--first recent--loading">
            <div class="loader"></div>
        </div>

        @include('partials.rankings', ['rankings' => $rankings])

        <div class="recent recent--second recent--loading">
            <div class="loader"></div>
        </div>

        @include('partials.upcoming', ['upcoming' => $upcoming])

        <div class="recent recent--second recent--second-second recent--loading">
            <div class="loader"></div>
        </div>

        @for($i = 0; $i < 3; $i++)
            <div class="recent recent--1-1 recent--loading">
                <div class="loader"></div>
            </div>
        @endfor

        @for($i = 0; $i < 3; $i++)
            <div class="recent recent--1-1 recent--loading">
                <div class="loader"></div>
            </div>
        @endfor

    </div>

    <p class="text-align--center">
        <button class="btn btn--text btn--lg load-more" data-url="/recent?page=1">@lang('pagination.LoadMore')</button>
    </p>
</section>