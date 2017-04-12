@extends('layouts.app')

@section('content')
    <article class="page--home">

        {!! $header !!}

        {!! $results !!}

        {!! $badges !!}

        {!! $content !!}

    </article>
@endsection

@push('scripts')
    <script src="{{ elixir('js/home.js') }}"></script>
@endpush