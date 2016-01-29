@extends('layouts.main')


@section('content')


    <div class="container spark-screen">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                @include('layouts.flashing')

                <div class="card card-block text-xs-center">
                  <h3 class="card-title">Dashboard</h3>
                  <p class="card-text">Welcome, {{ Auth::user()->getFullName() }}, you are logged in!</p>
                  <a href="/cspot/plans" class="btn btn-primary">Your Service Plans</a>
                  <a href="/cspot/plans/future" class="btn btn-secondary">Upcoming Service Plans</a>
                  <a href="/cspot/plans/next" class="btn btn-secondary">Next Sunday Service Plan</a>
                </div>                

            </div>
        </div>
    </div>

@endsection
