
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')


@section('content')


    <div class="container spark-screen">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                @include('layouts.flashing')


                <div class="card card-block text-xs-center">
                  <p>Welcome, <strong>{{ Auth::user()->first_name }}</strong>, to </p>

                  <h3 class="card-title">
                    c-SPOT, the church-Service Planning Online Tool
                  </h3>
                  for
                  <h4>
                    <img src="{{ url('images/churchLogo.png') }}" height="30" width="40">
                    {{ env('CHURCH_NAME') }}
                  </h4>

                  <hr>

                  <p class="card-text">
                    <a data-toggle="tooltip" title="Plans where you are leader or teacher"
                       href="{{ url('cspot/plans') }}" 
                       class="btn btn-lg btn-info md-full">
                           Your Service Plans</a> &nbsp; &nbsp; 
                    <a data-toggle="tooltip" title="All upcoming service plans"
                        href="{{ url('cspot/plans/future') }}" 
                        class="btn btn-lg btn-primary md-full">
                            Upcoming Service Plans</a> &nbsp;  &nbsp; 
                    <a data-toggle="tooltip" title="Go directly to next Sunday's Service Plan"
                        href="{{ url('cspot/plans/next') }}" 
                        class="btn btn-lg btn-success md-full">
                            Next Sunday's Plan</a>
                  </p>
                </div>
  
                @include('help')
            </div>
        </div>
    </div>

@endsection
