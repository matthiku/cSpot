@extends('layouts.main')

@section('title', "Welcome and Login")


@section('content')

    <div class="container signin-body">
    
        @include('info')

        @if (Auth::guest())
        <div class="form-signin">
            <h2 class="form-signin-heading">Please Sign In</h2>
        </div>
        @endif

    </div> <!-- /container -->

@stop
