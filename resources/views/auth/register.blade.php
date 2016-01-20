@extends('layouts.main')

@section('title', "Register New User")

@section('login', 'active')

@section('content')

    @include('layouts.flashing')


    <div class="row">
        <div class="col-lg-6 col-lg-offset-3 signin-body">


            <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                {!! csrf_field() !!}

                <h3 class="card-header">Register</h3>
                

                <div class="row form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label class="col-md-3 col-md-offset-1 control-label">Name</label>

                    <div class="col-md-6">
                        <input required type="text" class="form-control" name="name" value="{{ old('name') }}">

                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

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

                <div class="row form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                    <label class="col-md-3 col-md-offset-1 control-label">Confirm Password</label>

                    <div class="col-md-6">
                        <input required type="password" class="form-control" name="password_confirmation">

                        @if ($errors->has('password_confirmation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-2 col-md-offset-8">
                        <button type="submit" class="btn btn-primary form-btn">
                            <i class="fa fa-btn fa-user"></i> Register
                        </button>
                    </div>
                </div>

                <h4>Or sign up using your account in one of these providers:</h4>

                <a href="/login/github" class="btn btn-lg btn-secondary" role="button"><i class="fa fa-github"></i> Github</a>
                <a href="/login/google" class="btn btn-lg btn-secondary" role="button"><i class="fa fa-google"></i> Google</a>
                <a href="/login/twitter" class="btn btn-lg btn-secondary" role="button"><i class="fa fa-twitter"></i> Twitter</a>
                <a href="/login/facebook" class="btn btn-lg btn-secondary" role="button"><i class="fa fa-facebook"></i> Facebook</a>
                <a href="/login/linkedin" class="btn btn-lg btn-secondary" role="button"><i class="fa fa-linkedin"></i> LinkedIn</a>

            </form>

        </div><!-- col -->
    </div><!-- row -->

@endsection
