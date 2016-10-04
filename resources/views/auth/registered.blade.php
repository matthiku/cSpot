
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Register New User")

@section('login', 'active')

@section('content')



    <div class="row">
        <div class="col-lg-6 offset-lg-3 signin-body">

            @include('layouts.flashing')

            <center>Click the link contained in that email in order to conclude your registration.</center>

        </div><!-- col -->
    </div><!-- row -->

@endsection
