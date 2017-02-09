
<!-- # (C) 2016 Matthias Kuhs, Ireland -->


<div class="container spark-screen">
    <div class="row">


        <div class="col-md-3">
            <img class="image" src="{{ url('images/cspot.png') }}">
        </div>

        <div class="col-md-9">
            <div class="panel panel-default">
                
                <div class="panel-heading lora text-shadow">
                    <img src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}" height="20">
                    {{ env('CHURCH_NAME') }}<br>Welcome to c-SPOT,
                </div>

                <br>

                <div class="panel-body center lora text-shadow">
                    <h2>the <span class="text-primary">ch</span>urch-<span class="text-primary">S</span>ervice 
                        <span class="text-primary">P</span>lanning <span class="text-primary">O</span>nline <span class="text-primary">T</span>ool</h2>
                    <br>

                </div>

                <div>
                    <h4 class="float-right lora">
                        <a href="{{ url('home') }}">Start Here</a>
                    </h4>
                    <h4 class="lora">
                        <a href="{{ url('cspot/plans/calendar') }}">Event Calendar</a>
                    </h4>
                </div>


            </div>
        </div>


    </div>
</div>
