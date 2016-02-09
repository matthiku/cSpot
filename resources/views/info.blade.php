
<!-- # (C) 2016 Matthias Kuhs, Ireland -->


<div class="container spark-screen">
    <div class="row">


        <div class="col-md-3">
            <img class="image" src="{{ url('images/cspot.png') }}">
        </div>

        <div class="col-md-9">
            <div class="panel panel-default">
                
                <div class="panel-heading">
                    <img src="{{ url('images/churchLogo.png') }}" height="20" width="30">
                    {{ env('CHURCH_NAME') }}<br>Welcome to  c-SPOT!
                </div>

                <br>

                <div class="panel-body center">
                    <h4>the <span class="text-primary">c</span>hurch-<span class="text-primary">S</span>ervice 
                        <span class="text-primary">P</span>lanning <span class="text-primary">O</span>nline <span class="text-primary">T</span>ool</h4>
                    <br>

                </div>

                @if (Auth::guest())

                    <div class="form-signin right">
                        <h4>
                            <a href="{{url('login')}}"><i class="fa fa-sign-in"></i> Sign in</a>  
                            &nbsp; or &nbsp;
                            <a href="{{url('register')}}"><i class="fa fa-list"></i> Sign up</a>
                        </h4>
                    </div>

                @else

                    <div class="form-signin right">
                        <h2 class="form-signin-heading">
                            <a href="{{url('home')}}">Start using c-SPOT</a>
                        </h2>
                    </div>

                @endif


            </div>
        </div>


    </div>
</div>
