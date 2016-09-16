<article class="note">
    <header class="bg--dark bg--grid">
        <h1>{{$note->title}}</h1>
        <time>@stamp($note->updated_at)</time>
    </header>

    <div class="body">
        {!! $note->content !!}
    </div>
</article>