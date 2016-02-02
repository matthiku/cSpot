
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Welcome and Login")


@section('content')

    <div class="container signin-body">
    
        @include('layouts.flashing')

        @include('info')

        @if (Auth::guest())
            <div class="form-signin center">
                <h2 class="form-signin-heading"><a href="{{url('login')}}">Please Sign In</a></h2>
            </div>
        @else
            <div class="form-signin center">
                <h2 class="form-signin-heading"><a href="{{url('home')}}">Start using c-SPOT</a></h2>
            </div>
        @endif

        @include('help')

    </div> <!-- /container -->

@stop
