
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
                        c-SPOT, the <span class="text-primary">ch</span>urch-<span class="text-primary">S</span>ervice 
                        <span class="text-primary">P</span>lanning <span class="text-primary">O</span>nline <span class="text-primary">T</span>ool
                    </h3>
                    for
                    <h4>
                        <img src="{{ url('images/churchLogo.png') }}" height="30" width="40">
                        {{ env('CHURCH_NAME') }}
                    </h4>

<?php // on mobile devices only:
    if (preg_match('/iphone|ipod|android/',strtolower($_SERVER['HTTP_USER_AGENT'])))
        echo "For best functionality, go into your browser's menu and add me to your homescreen!";
?>                    

                    <hr>


                    <p class="card-text">

                        <span class="btn btn-lg btn-success md-full">
                            <a href="#" data-container="body" data-toggle="tooltip" data-placement="left" class="pull-xs-right" 
                                    title="Go directly to next Sunday's Service Plan">
                                &nbsp; <i class="fa fa-question-circle"></i></a>
                            <a href="{{ url('cspot/plans/next') }}">
                                Next Sunday's Plan
                            </a>
                        </span>
                        &nbsp; 
                        &nbsp; 
                        <span class="btn btn-lg btn-primary md-full">
                            <a href="#" data-container="body" data-toggle="tooltip" data-placement="left" class="pull-xs-right" 
                                    title="Show all upcoming Service Plans">
                                &nbsp; <i class="fa fa-question-circle bg-primary"></i></a>
                            <a href="{{ url('cspot/plans?filterby=future') }}" class="bg-primary">
                                Upcoming Service Plans
                            </a>
                        </span>
                        &nbsp;
                        &nbsp; 
                        <span class="btn btn-lg btn-info md-full">
                            <a href="#" data-container="body" data-toggle="tooltip" data-placement="left" class="pull-xs-right" 
                                    title="Show (future) plans where you are leader or teacher">
                                &nbsp; <i class="fa fa-question-circle"></i></a>
                            <a href="{{ url('cspot/plans') }}">
                                Your Service Plans
                            </a>
                        </span>

                    </p>
                    <hr>
                    <div id="inpDate" onchange="submitDate(this)"></div>

                </div>

  
                @include('help')


            </div>
        </div>
    </div>

@endsection
