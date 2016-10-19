@extends('layouts.app')

@section('title')
    @lang('auth.resetPassword') -
@endsection

@section('content')

    <article class="page--forgot-password">

        <header class="page-header header--small">
            <div class="bg-elements">
                <div class="bg--gradient"></div>
                <div class="bg--img"></div>
            </div>
            <div class="container">
                <h1>@lang('auth.resetPassword')</h1>
            </div>
        </header>

        <div class="container page-section">
            <header class="divider--bottom text-align--center">
                <h1><span class="text--muted">@lang('auth.reset')</span> @lang('auth.password')</h1>
            </header>

            <div class="row">
                <div class="col-md-4 col-md-offset-4">

                    <form role="form" method="POST" action="{{ url('/password/reset') }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">@lang('auth.emailAddress')</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="control-label">@lang('auth.password')</label>
                            <input id="password" type="password" class="form-control" name="password" required>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="control-label">@lang('auth.confirmPassword')</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                        </div>

                        <footer class="form-group text-align--right">
                            <button type="submit" class="btn btn-primary">@lang('auth.resetPassword')</button>
                        </footer>
                    </form>

                </div>
            </div>
        </div>

    </article>

@endsection
