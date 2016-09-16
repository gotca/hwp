<div class="recent recent--photos popup-gallery"
     data-recent-id="{{$recent->id}}"
     data-photos-count="{{$count}}"
     data-gallery-path="@route('gallery.recent', ['recent'=>$recent->id])"
>
    <div class="bg-elements">
        <div class="bg--dark-gray"></div>
        <div class="bg--gallery bg--img">
            @each('partials.photos.bg-square', $photos, 'photo')
        </div>
        <div class="bg--grid blend--overlay"></div>
        <div class="bg--bottom-third-primary"></div>
    </div>

    <a href="{{$photos->first()->photo}}"
       title="@lang('recent.viewPhotos')"
       class="gallery-trigger"
       data-gallery="recent:{{$recent->id}}"
    >
        <div class="tag"><span><em>@lang('recent.photos')</em></span></div>

        <h1>{{trans_choice('recent.addedPhotosCount', $count)}}</h1>

        <time datetime="@iso($recent->created_at)">@stamp($recent->created_at)</time>
    </a>

    <div class="recent--hover">
        <h1><i class="fa fa-search-plus"></i>@lang('recent.viewPhotos')</h1>
    </div>

    <div class="block-loading bg--dark">
        <div class="loader"></div>
    </div>

</div>