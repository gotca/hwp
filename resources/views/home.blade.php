@extends('master')

@section('content')
    <article class="page--home">

        {!! $header !!}

        {!! $results !!}

        {!! $badges !!}

        {!! $content !!}

    </article>
@endsection

@push('scripts')
    <script src="js/home.js"></script>
@endpush