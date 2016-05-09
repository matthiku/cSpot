
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Login User")

@section('login', 'active')

@section('content')


    <div class="row">
        <div class="col-lg-6 col-lg-offset-3 signin-body">

            @include('layouts.flashing')
            
            <form class="form-horizontal" role="form" method="POST" id="inputForm"  action="{{ url('login') }}">
                {!! csrf_field() !!}
        
                <center>
                    <h4>Sign in with your account with one of those service providers:</h4>
                    @include('auth.social')
                </center>
                <br />
                <br />

                <h2 class="card-header">Or sign in via email:</h2>

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

                <div class="row">
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
                        <a class="btn btn-primary-outline pull-right" href="{{ url('password/reset') }}">Forgot Your Password?</a>
                    </div>
                </div>

            </form>

            <script type="text/javascript">document.forms.inputForm.email.focus()</script>

            <br>
            @include('help')

        </div>    
    </div>

@endsection
