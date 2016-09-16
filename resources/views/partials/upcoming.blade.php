<aside class="card card--no-shadow upcoming">
    <header class="bg--grid bg--primary has-controls text-align--left">
        <h1>@lang('misc.Upcoming')</h1>
        <a href="@route('schedule')" title="@lang('misc.viewAll')">@lang('misc.viewAll')</a>
    </header>

    <table class="body upcoming table table--striped">

        @forelse($upcoming as $date => $events)
            <tbody>
                <tr class="upcoming-day">
                    <td colspan="4">@day(new Carbon\Carbon($date))</td>
                </tr>
                @foreach($events as $event)
                    <tr class="upcoming-event">
                        <th class="upcoming-time">@time($event->start)</th>
                        <th class="upcoming-team">{{$event->team}}</th>
                        <td class="upcoming-opponent">{{$event->opponent}}</td>
                        <td class="upcoming-location">
                            <a href="{{$event->location->googleDirectionsLink()}}"><i class="fa fa-map-marker"></i> {{$event->location->title_short}}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @empty
            <tbody>
                <tr><td>@include('partials.nothing-here-yet')</td></tr>
            </tbody>
        @endforelse

    </table>
</aside>