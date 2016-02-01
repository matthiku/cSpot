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
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/signin.css"/>
    <link rel="stylesheet" href="/css/dashboard.css"/>
    
    <script src="/js/tether.min.js"></script>
    <script src="/js/jquery.min.js"></script>
  </head>


  <body id="app-layout">
    <nav class="tek-nav navbar navbar-dark bg-inverse navbar-full">

      <a class="navbar-brand" href="/"><img src="/images/xs-cspot.png" height="20" width="30"></a>

      <ul class="nav navbar-nav">
        <li class="nav-item {{ Request::is('home') ? 'active' : '' }}">
          <a class="nav-link" href="/home">Home <span class="sr-only">(current)</span></a>
        </li>
        @if (Auth::user())
        <li class="nav-item dropdown {{ Request::is('cspot/plans*') ? 'active' : '' }}">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                Service Plans <span class="caret"></span>
            </a>
            <div class="dropdown-menu" role="menu">
              <a class="dropdown-item" href="/cspot/plans/future"><i class="fa fa-btn fa-calendar fa-lg"></i> Upcoming Plans</a>
              <a class="dropdown-item" href="/cspot/plans"><i class="fa fa-btn fa-calendar-check-o fa-lg"></i> My Service Plans</a>
              @if( Auth::user()->isEditor() )
                <a class="dropdown-item" href="/cspot/plans/create"><i class="fa fa-btn fa-calendar-plus-o fa-lg"></i> Add New Plan</a>
              @endif
              <a class="dropdown-item" href="/admin/types"><i class="fa fa-btn fa-bars fa-lg"></i> Service Types</a>
            </div>
        </li>
        <li class="nav-item {{ Request::is('cspot/songs*') ? 'active' : '' }}">
          <a class="nav-link" href="/cspot/songs">Songs</a>
        </li>
        @endif
      </ul>

      <ul class="nav navbar-nav pull-xs-right">
          <!-- Authentication Links -->
          @if (Auth::guest())
              <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Sign in</a></li>
              <li class="nav-item"><a class="nav-link" href="{{ url('/register') }}">Register</a></li>
          @else
              <li class="nav-item dropdown">
                  <a href="#" class="nav-link dropdown-toggle{{ Request::is('admin/*') ? ' active' : '' }}" 
                     data-toggle="dropdown" role="button" aria-expanded="false">
                      Admin <span class="caret"></span>
                  </a>
                  <div class="dropdown-menu" role="menu">
                    <a class="dropdown-item" href="/admin/default_items"><i class="fa fa-btn fa-file-o fa-lg"></i> Default Items</a>
                    <a class="dropdown-item" href="/admin/users"><i class="fa fa-btn fa-users fa-lg"></i> User List</a>
                    <a class="dropdown-item" href="/admin/roles"><i class="fa fa-btn fa-user-times fa-lg"></i> User Roles</a>
                  </div>
              </li>
              <li class="nav-item dropdown">
                  <a href="#" class="nav-link dropdown-toggle" 
                     data-toggle="dropdown" role="button" aria-expanded="false">
                      Welcome, {{ Auth::user()->getFullName() }} <span class="caret"></span>
                  </a>

                  <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="{{ url('/admin/users/'.Auth::user()->id) }}"><i class="fa fa-btn fa-user fa-lg"></i> Profile</a>
                      <a class="dropdown-item" href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out fa-lg"></i> Logout</a>
                  </div>
              </li>
          @endif
      </ul>

      @if ( Auth::guest() )
      <form class="form-inline pull-xs-right hidden-md-down" method="POST" role="form" action="{{ url('/login') }}">
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


    </nav>



    <div class="container-fluid app-content">

            @yield('content')

    </div><!-- container fluid -->






    <!-- JavaScripts -->
    <script src="/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
          $(function () {
            $('[data-toggle="tooltip"]').tooltip()
          });
        });
    </script>

  </body>

</html>
