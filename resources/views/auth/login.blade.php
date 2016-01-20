@extends('layouts.main')

@section('title', "Login User")

@section('login', 'active')

@section('content')


    <div class="row">
        <div class="col-lg-6 col-lg-offset-3 signin-body">
    @include('layouts.flashing')
            
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                {!! csrf_field() !!}

                <h2 class="card-header">Please sign in</h2>

                <div class="row form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="col-md-3 col-md-offset-1 control-label">E-Mail Address</label>

                    <div class="col-md-6">
                        <input required type="email" class="form-control" name="email" value="{{ old('email') }}">

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label class="col-md-3 col-md-offset-1 control-label">Password</label>

                    <div class="col-md-6">
                        <input required type="password" class="form-control" name="password">

                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember"> Remember Me
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-btn fa-sign-in"></i> Login
                        </button>

                        <a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
                    </div>
                </div>
                <h4>Or sign in using your provider:</h4>

                <a href="/login/github" class="btn btn-lg btn-secondary" role="button"><i class="fa fa-github"></i> Github</a>
                <a href="/login/google" class="btn btn-lg btn-secondary" role="button"><i class="fa fa-google"></i> Google</a>
                <a href="/login/twitter" class="btn btn-lg btn-secondary" role="button"><i class="fa fa-twitter"></i> Twitter</a>
                <a href="/login/facebook" class="btn btn-lg btn-secondary" role="button"><i class="fa fa-facebook"></i> Facebook</a>
                <a href="/login/linkedin" class="btn btn-lg btn-secondary" role="button"><i class="fa fa-linkedin"></i> LinkedIn</a>
            </form>

        </div>    
    </div>

@endsection
