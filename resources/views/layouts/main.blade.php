<!DOCTYPE html>
<html lang="en">

<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<?php \Carbon\Carbon::setLocale('en');?>

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>c-SPOT @yield('title')</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('css/font-awesome.min.css') }}" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{ url('css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/signin.css') }}"/>
    <!-- <link rel="stylesheet" href="{{ url('css/dashboard.css') }}"/> -->
    
    <script src="{{ url('js/tether.min.js') }}"></script>
    <script src="{{ url('js/jquery.min.js') }}"></script>

    <script src="{{ url('js/helpers.js') }}"></script>
    <script>
        var __app_url = "{{ url('/') }}";
    </script>

  </head>




  <body id="app-layout">
    <nav class="tek-nav navbar navbar-dark bg-inverse navbar-full">

      <ul class="nav navbar-nav pull-xs-right">
          <!-- Authentication Links -->
          @if (Auth::guest())
              <li class="nav-item"><a class="nav-link" href="{{ url('login') }}">Sign in</a></li>
              <li class="nav-item"><a class="nav-link" href="{{ url('register') }}">Register</a></li>
          @else
              <li class="nav-item dropdown">
                  <a href="#" class="nav-link dropdown-toggle{{ Request::is('admin/*') ? ' active' : '' }}" 
                     data-toggle="dropdown" role="button" aria-expanded="false">
                      Config <span class="caret"></span>
                  </a>
                  <div class="dropdown-menu" role="menu">
                    <a class="dropdown-item" href="{{ url('admin/default_items') }}"><i class="fa fa-btn fa-server fa-lg"></i> &nbsp; Default Items</a>
                    <hr>
                    <a class="dropdown-item" href="{{ url('admin/users') }}"><i class="fa fa-btn fa-users fa-lg"></i> &nbsp; User List</a>
                    <a class="dropdown-item" href="{{ url('admin/roles') }}"><i class="fa fa-btn fa-check-square-o fa-lg"></i> &nbsp; User Roles</a>
                  </d..iv>
              </li>
              <li class="nav-item dropdown m-r-2">
                  <a href="#" class="nav-link dropdown-toggle " 
                     data-toggle="dropdown" role="button" aria-expanded="false">
                      {{ Auth::user()->first_name }}
                      <span class="caret"></span>
                  </a>

                  <div class="dropdown-menu dropdown-menu-right" role="menu">
                      <a class="dropdown-item" href="{{ url('admin/users/'.Auth::user()->id) }}">Profile <i class="fa fa-btn fa-user fa-lg"></i></a>
                      <a class="dropdown-item" href="{{ url('logout') }}">Logout <i class="fa fa-btn fa-sign-out fa-lg"></i></a>
                  </div>
              </li>
          @endif
      </ul>

      @if ( Auth::guest() )
      <form class="form-inline pull-xs-right hidden-md-down" method="POST" role="form" action="{{ url('login') }}">
        Log in using @include('auth.social', ['hideLblText' => 'true']) or: 
        {!! csrf_field() !!}
        <div class="form-group">
          <input type="email" name="email" class="form-control-sm" id="inputEmail" placeholder="Enter email">
        </div>
        <div class="form-group small-pw-input">
          <input type="password" name="password" class="form-control-sm small-pw-input" id="inputPassword" placeholder="Password">
        </div>
        <div class="checkbox hidden-xs-up">
          <label>
            <input type="checkbox" name="remember" checked="checked"> Remember me
          </label>
        </div>
        <button type="submit" class="btn btn-sm btn-primary">Sign in</button> &nbsp; &nbsp; &nbsp;
      </form>
      @endif


      <a class="navbar-brand" href="{{ url('.') }}"><img src="{{ url('images/xs-cspot.png') }}" height="20" width="30"></a>

      <ul class="nav navbar-nav">
        @if (Auth::user())
        <li class="nav-item dropdown {{ Request::is('cspot/plans*') ? 'active' : '' }}">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                Planning <span class="caret"></span>
            </a>
            <div class="dropdown-menu" role="menu">
              <a class="dropdown-item" href="{{ url('cspot/plans/next') }}"><i class="fa fa-btn fa-bell-o fa-lg"></i> &nbsp; Next Sunday</a>
              <a class="dropdown-item" href="{{ url('cspot/plans/future') }}"><i class="fa fa-btn fa-calendar fa-lg"></i> &nbsp; Upcoming Plans</a>
              <hr>
              <a class="dropdown-item" href="{{ url('cspot/plans') }}"><i class="fa fa-btn fa-calendar-check-o fa-lg"></i> &nbsp; My Service Plans</a>
              @if( Auth::user()->isEditor() )
                <a class="dropdown-item" href="{{ url('cspot/plans/create') }}"><i class="fa fa-btn fa-calendar-plus-o fa-lg"></i> &nbsp; Add New Plan</a>
              @endif
              <a class="dropdown-item" href="{{ url('admin/types') }}"><i class="fa fa-btn fa-tasks fa-lg"></i> &nbsp; Service Types</a>
              <hr>
              <a class="dropdown-item" href="{{ url('cspot/songs') }}"><i class="fa fa-btn fa-music fa-lg"></i> &nbsp; Songs</a>
            </div>
        </li>
        @endif
        <li class="hidden-md-down center">{{ env('CHURCH_NAME') }}</li>
      </ul>


    </nav>



    <div class="container-fluid app-content">

            @yield('content')

    </div><!-- container fluid -->






    <!-- JavaScripts -->
    <script src="{{ url('js/bootstrap.min.js') }}"></script>

  </body>

</html>
