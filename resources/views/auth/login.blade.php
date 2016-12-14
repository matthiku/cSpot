
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Login User")

@section('login', 'active')

@section('content')


    <div class="row">
        <div class="container signin-body">

            @include('layouts.flashing')
    


            <center class="mb-2">

                <h4 class="lora">Sign in using your account from one of these service providers:</h4>

                <div class="row">
            
                    <div class="col-md-12">
                        @include('auth.social')
                    </div>
                    

                    <div class="col-xl-4 offset-xl-4 col-lg-6 offset-lg-3 col-md-8 offset-md-2 col-sm-10 offset-sm-1 bg-info rounded mt-2">
                        <span class="text-white">Not sure what to do? How to register?<br>
                        <a href="https://www.youtube.com/watch?v=SNgq9ZW1KMs" target="new">
                            <i class="fa fa-youtube-play red"></i>
                            Watch this short training video
                            <i class="fa fa-external-link small"></i><br> 
                        </a>
                        <small>explains the sign-in process and the basics features of c-SPOT</small></span>
                    </div>

                </div>


            </center>



            <h5 class="card-header">You can also sign in by email address and password:</h5>

            <form class="form-horizontal" role="form" method="POST" id="inputForm" oninput="enableSubmitButton()"
                action="{{ url('login') }}">
                {!! csrf_field() !!}

                <div class="row mt-1 form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="col-md-4 text-xs-right control-label">E-Mail Address</label>

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
                    <label class="col-md-4 text-xs-right control-label">Password</label>

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
                    <div class="col-md-6 offset-md-4">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember"> Remember Me
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-outline-success submit-button disabled">
                            <i class="fa fa-btn fa-sign-in"></i> Login
                        </button>
                        <a class="btn btn-outline-primary float-xs-right" href="{{ url('password/reset') }}">Forgot Your Password?</a>
                    </div>
                </div>

            </form>

            <script type="text/javascript">document.forms.inputForm.email.focus()</script>

            <br>
            @include('help')

        </div>    
    </div>

@endsection
