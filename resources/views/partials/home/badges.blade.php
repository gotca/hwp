{{-- only show if we actually have badges --}}

@if(isset($badges) && $badges->count())
    <section class="page-section badges bg--smoke">
        <div class="container">
            <div class="row center-xs">
                @each('partials.badge', $badges, 'badge')
            </div>
        </div>
    </section>
@endif