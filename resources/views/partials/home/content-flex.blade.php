<section class="page-section recent-content container">

    <div class="recent-grid">

        @include('partials.home.content-flex-initial')

    </div>

    <p class="text-align--center">
        <button class="btn btn--text btn--lg load-more" data-url="/recent?page=1">@lang('pagination.LoadMore')</button>
    </p>

</section>

<script id="page-even" type="text/html">
    @include('partials.home.content-flex-even')
</script>

<script id="page-odd" type="text/html">
    @include('partials.home.content-flex-odd')
</script>