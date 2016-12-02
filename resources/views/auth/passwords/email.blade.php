
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Reset Password")

<!-- Main Content -->
@section('content')

    <div class="row">
        <div class="col-lg-6 offset-lg-3 signin-body">

            @include('layouts.flashing')

            <form class="form-horizontal form-signin" role="form" method="POST" action="{{ url('password/email') }}">
                {!! csrf_field() !!}

                <h4 class="card-header center mb-1">Please enter your email address to reset your password</h4>

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="col-md-4 control-label">E-Mail Address</label>

                    <div class="col-md-6 mb-1">
                        <input required type="email" class="form-control first-focus" name="email" value="{{ old('email') }}">

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 offset-md-4 mb-1">
                        <button type="submit" class="btn btn-primary" onclick="showSpinner();">
                            <i class="fa fa-btn fa-envelope"></i> Send Password Reset Link
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

@endsection
