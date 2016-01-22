@extends('layouts.main')

@section('title', "Welcome and Login")


@section('content')

    <div class="container signin-body">
    
        @include('layouts.flashing')

        @include('info')

        @if (Auth::guest())
        <div class="form-signin center">
            <h2 class="form-signin-heading"><a href="/login">Please Sign In</a></h2>
        </div>
        @endif

    </div> <!-- /container -->

@stop
