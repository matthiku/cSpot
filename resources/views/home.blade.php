
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')


@section('content')


    <div class="container spark-screen">
        <div class="row">
            <div class="col-xl-10 offset-xl-1">
            
                @include('layouts.flashing')


                <div class="card card-block center">

                    @if (! Auth::user()->isMusician())
                        <p>Welcome, <strong class="shil">{{ Auth::user()->first_name }}</strong>, to </p>

                        <h3 class="card-title lora text-shadow">
                            c-SPOT, the <span class="text-primary">c</span>hurch-<span class="text-primary">S</span>ervices 
                            <span class="text-primary">P</span>lanning <span class="text-primary">O</span>nline <span class="text-primary">T</span>ool
                        </h3>
                        for
                        <small class="float-xs-right"><a href="http://www.ie.ccli.com/?country=ie">CCLI</a> # {{ (env('CHURCH_CCLI')) ? env('CHURCH_CCLI') : '?' }}</small>

                    @else

                        <h3 class="korn text-shadow">c-SPOT, the <span class="text-primary">c</span>hurch-<span class="text-primary">S</span>ervices 
                            <span class="text-primary">P</span>lanning <span class="text-primary">O</span>nline <span class="text-primary">T</span>ool</h3>
                        <small><a href="http://www.ie.ccli.com/?country=ie">CCLI</a> # {{ (env('CHURCH_CCLI')) ? env('CHURCH_CCLI') : '?' }}</small>
                    @endif


                    <a href="{{ env('CHURCH_URL') }}" target="new">
                        <h3 class="shil">
                            <img src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}" height="30">
                            {{ env('CHURCH_NAME') }}
                        </h3>
                    </a>


                    <hr>


                    <p class="card-text lora">

                        <span class="btn btn-lg btn-success md-full mr-2">
                            <a href="#" data-container="body" data-toggle="tooltip" data-placement="left" 
                                    class="float-xs-right" title="Go directly to next Sunday's Service Plan">
                                &nbsp; <i class="fa fa-question-circle"></i></a>
                            <a href="{{ url('cspot/plans/next') }}">
                                Next Sunday's Plan
                            </a>
                        </span>

                        <button class="btn btn-lg btn-primary md-full mr-2"
                                onclick="location.href='{{ url('cspot/plans?filterby=future') }}'">
                            <a href="#" data-container="body" data-toggle="tooltip" data-placement="left" 
                                    class="float-xs-right" title="Show all upcoming Service Plans">
                                <i class="fa fa-question-circle bg-primary text-white ml-1"></i></a>
                            Upcoming Events
                        </button>

                        <span class="btn btn-lg btn-info md-full">
                            <a href="#" data-container="body" data-toggle="tooltip" data-placement="left" class="float-xs-right" 
                                    title="Show (future) plans where you are leader or teacher">
                                &nbsp; <i class="fa fa-question-circle"></i></a>
                            <a href="{{ url('cspot/plans') }}">
                                My Services/Events
                            </a>
                        </span>

                    </p>
                    <hr>

                    <p class="card-text lora">

                        <span class="btn btn-outline-success md-full mr-2">
                            <a href="{{ url('cspot/songs') }}">
                                <i class="fa fa-btn fa-music fa-lg float-xs-left"> </i>
                                &nbsp; Songs Repository <small>{{ isset($songsCount) ? '('.$songsCount.')' : '' }}</small>
                                <i class="fa fa-btn fa-music fa-lg float-xs-right hidden-md-up"></i>
                            </a>
                        </span>

                        <button class="btn btn-outline-primary md-full mr-2">
                            <a href="{{ url('cspot/songs?only=slides') }}">
                                <i class="fa fa-btn fa-clone fa-lg float-xs-left"></i>
                                &nbsp; Slides 
                                <small>{{ isset($slideCount) ? '('.$slideCount.')' : '' }}</small>
                                <i class="fa fa-btn fa-clone fa-lg float-xs-right hidden-md-up"></i>
                            </a>
                        </button>

                        <span class="btn btn-outline-info md-full">
                            <a href="{{ url('cspot/songs?only=video') }}">
                                <i class="fa fa-btn fa-tv fa-lg float-xs-left"></i>
                                &nbsp; Videoclips 
                                <small>{{ isset($videoCount) ? '('.$videoCount.')' : '' }}</small>
                                <i class="fa fa-btn fa-tv fa-lg float-xs-right hidden-md-up"></i>
                            </a>
                        </span>

                    </p>
                    <hr>

                    <div id="inpDate" onchange="openPlanByDate(this)"></div>

                </div>

  
                @include('help')


            </div>
        </div>
    </div>

@endsection
