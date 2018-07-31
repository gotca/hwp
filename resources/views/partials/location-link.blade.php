@if($location->street)
    <a href="{{$location->googleDirectionsLink()}}" title="@lang('directions')">
        @if(!isset($hideIcon) && isset($iconBefore))<i class="fa fa-map-marker"></i>@endif
        {{isset($short) ? $location->title_short : $location->title}}
        @if(!isset($hideIcon) && !isset($iconBefore)) <i class="fa fa-map-marker"></i>@endif
    </a>
@else
    <span class="text--muted">{{isset($short) ? $location->title_short : $location->title}}</span>
@endif