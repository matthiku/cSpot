@extends('layouts.main')

@section('title', "Reset Password")

<!-- Main Content -->
@section('content')

    <div class="row">
        <div class="col-lg-6 col-lg-offset-3 signin-body">

            @include('layouts.flashing')

            <form class="form-horizontal form-signin" role="form" method="POST" action="{{ url('/password/email') }}">
                {!! csrf_field() !!}

                <h3 class="card-header">Enter your email address to reset your password</h3>

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="col-md-4 control-label">E-Mail Address</label>

                    <div class="col-md-6">
                        <input required type="email" class="form-control" name="email" value="{{ old('email') }}">

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-btn fa-envelope"></i> Send Password Reset Link
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

@endsection
