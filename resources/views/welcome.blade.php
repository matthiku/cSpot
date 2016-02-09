
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Welcome and Login")



@section('content')


    <div class="container signin-body">

    
        @include('layouts.flashing')

        @include('info')

        <hr>

        @include('help')


    </div> <!-- /container -->


@stop
