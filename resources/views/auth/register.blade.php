
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Register New User")

@section('login', 'active')

@section('content')



    <div class="row">
        <div class="container signin-body">

            @include('layouts.flashing')

            <form class="form-horizontal" role="form" method="POST" id="inputForm" 
                  oninput="enableSubmitButton()" action="{{ url('register') }}">
                {!! csrf_field() !!}
                
                <center>
                    <h4 class="lora"><strong>Sign up (register)</strong> using your account with one of those providers:</h4>
                    @include('auth.social')
                </center>
                <br />
                <br />


                <h3 class="card-header mb-2">Or register here:</h3>

                <div class="row form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                    <label class="col-md-3 offset-md-1 control-label">First Name</label>

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
                    <label class="col-md-3 offset-md-1 control-label">Last Name</label>

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
                    <label class="col-md-3 offset-md-1 control-label">E-Mail Address</label>

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
                    <label class="col-md-3 offset-md-1 control-label">Password</label>

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
                    <label class="col-md-3 offset-md-1 control-label">Confirm Password</label>

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
                    <div class="col-md-2 offset-md-8">
                        <button type="submit" class="btn form-btn btn-outline-success submit-button disabled" onclick="showSpinner();">
                            <i class="fa fa-btn fa-envelope"></i> Register
                        </button>
                    </div>
                </div>

            </form>

            <script type="text/javascript">document.forms.inputForm.first_name.focus()</script>


        </div><!-- col -->
    </div><!-- row -->

@endsection
