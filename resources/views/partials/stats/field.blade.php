@include('partials.stats.shooting', ['stats' => $stats])
@include('partials.stats.steals-turnovers', ['stats' => $stats])
@include('partials.stats.kickouts', ['stats' => $stats])
@include('partials.stats.goals-assists', ['stats' => $stats])

@if(isset($for) && $for === 'PLAYER' && $stats->sprints_taken > 2)
    @include('partials.stats.sprints', ['stats' => $stats])
@endif