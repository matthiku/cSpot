@extends('layouts.main')


@section('content')

    @include('layouts.sidebar')

    <div class="container spark-screen">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                @include('layouts.flashing')

                <div class="card card-block">
                  <h3 class="card-title">Dashboard</h3>
                  <p class="card-text">You are logged in!</p>
                  <a href="/cspot/plans" class="btn btn-primary">Your Service Plans</a>
                </div>                

            </div>
        </div>
    </div>

@endsection
