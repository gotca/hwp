@extends('layouts.app')

@section('title')
    @lang('auth.forgotYourPassword') -
@endsection

<!-- Main Content -->
@section('content')

    <article class="page--forgot-password">

        <header class="page-header header--small">
            <div class="bg-elements">
                <div class="bg--gradient"></div>
                <div class="bg--img"></div>
            </div>
            <div class="container">
                <h1>@lang('auth.forgotYourPassword')</h1>
            </div>
        </header>

        <div class="container page-section">
            <header class="divider--bottom text-align--center">
                <h1><span class="text--muted">@lang('auth.forgotten')</span> @lang('auth.password')</h1>
            </header>

            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form role="form" method="POST" action="{{ url('/password/email') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">@lang('auth.emailAddress')</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                            @if ($errors->has('email'))
                                <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>

                        <footer class="form-group text-align--right">
                            <button type="submit" class="btn btn--submit">@lang('auth.sendPasswordReset')</button>
                        </footer>
                    </form>
                </div>
            </div>
        </div>

    </article>
@endsection
