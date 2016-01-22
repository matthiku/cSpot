@extends('layouts.main')

@section('title', "Register New User")

@section('login', 'active')

@section('content')



    <div class="row">
        <div class="col-lg-6 col-lg-offset-3 signin-body">

            @include('layouts.flashing')

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                {!! csrf_field() !!}

                <h3 class="card-header">Register</h3>
                

                <div class="row form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                    <label class="col-md-3 col-md-offset-1 control-label">First Name</label>

                    <div class="col-md-6">
                        <input required type="text" class="form-control" name="first_name" value="{{ old('first_name') }}">

                        @if ($errors->has('first_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('first_name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                    <label class="col-md-3 col-md-offset-1 control-label">Last Name</label>

                    <div class="col-md-6">
                        <input required type="text" class="form-control" name="last_name" value="{{ old('last_name') }}">

                        @if ($errors->has('last_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('last_name') }}</strong>
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
                            <i class="fa fa-btn fa-envelope"></i> Register
                        </button>
                    </div>
                </div>

                <br />
                <center>
                    <h4>Or sign up using your account on one of these providers:</h4>
                    @include('auth.social')
                </center>

            </form>

        </div><!-- col -->
    </div><!-- row -->

@endsection
