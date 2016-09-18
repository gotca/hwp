<div class="recent-row">
    <div class="recent-cell recent-cell--1-3">
        @include('recent.loading', ['class'=>"recent--first"])
    </div>

    <div class="recent-cell recent-cell--1-3">
        <div class="recent-column">
            <div class="recent-cell recent-cell--1-2">
                @include('recent.loading', ['class'=>"recent--second"])
            </div>
            <div class="recent-cell recent-cell--1-2">
                @include('recent.loading', ['class'=>"recent--second"])
            </div>
        </div>
    </div>

    <div class="recent-cell recent-cell--1-3">
        @include('partials.upcoming', ['upcoming' => $upcoming])
    </div>
</div>

<div class="recent-row">
    <div class="recent-cell recent-cell--1-4">
        @include('partials.rankings', ['rankings' => $rankings])
    </div>

    <div class="recent-cell recent-cell--3-4">
        <div class="recent-column">
            <div class="recent-row">
                @for($i = 0; $i < 3; $i++)
                    <div class="recent-cell recent-cell--1-3">
                        @include('recent.loading')
                    </div>
                @endfor
            </div>

            <div class="recent-row">
                @for($i = 0; $i < 3; $i++)
                    <div class="recent-cell recent-cell--1-3">
                        @include('recent.loading')
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>