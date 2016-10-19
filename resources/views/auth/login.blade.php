@extends('layouts.app')

@section('title')
    @lang('auth.login') -
@endsection

@section('content')
    <article class="page--login">

        <header class="page-header header--small">
            <div class="bg-elements">
                <div class="bg--gradient"></div>
                <div class="bg--img"></div>
            </div>
            <div class="container">
                <h1>@lang('auth.login')</h1>
            </div>
        </header>

        <div class="container page-section">
            <header class="divider--bottom text-align--center">
                <h1>@lang('auth.login')</h1>
            </header>

            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <form role="form" method="POST" action="{{ url('/login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">@lang('auth.emailAddress')</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
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

                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember"> @lang('auth.rememberMe')
                                </label>
                            </div>
                        </div>

                        <footer class="form-group text-align--right">
                            <a class="" href="{{ url('/password/reset') }}">@lang('auth.forgotYourPassword')</a>
                            <button type="submit" class="btn btn--submit">@lang('auth.login')</button>
                        </footer>
                    </form>
                </div>
            </div>
        </div>

    </article>

@endsection
