<!DOCTYPE html>
<html lang="en">

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
  </head>


  <body id="app-layout">
    <nav class="tek-nav navbar navbar-dark bg-inverse navbar-full">

      <a class="navbar-brand" href="/">c-SPOT</a>

      <ul class="nav navbar-nav">
        <li class="nav-item {{ Request::is('home') ? 'active' : '' }}">
          <a class="nav-link" href="/home">Home <span class="sr-only">(current)</span></a>
        </li>
        @if (Auth::user())
        <li class="nav-item {{ Request::is('classifieds/create') ? 'active' : '' }}">
          <a class="nav-link" href="/home">Services</a>
        </li>
        <li class="nav-item {{ Request::is('classifieds/create') ? 'active' : '' }}">
          <a class="nav-link" href="/home">Songs</a>
        </li>
        @endif
      </ul>

      <ul class="nav navbar-nav pull-xs-right">
          <!-- Authentication Links -->
          @if (Auth::guest())
              <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Sign in</a></li>
              <li class="nav-item"><a class="nav-link" href="{{ url('/register') }}">Register</a></li>
          @else
              <li class="nav-item"><a class="nav-link {{ Request::is('admin/*') ? 'active' : '' }}" 
                   href="/admin/users">{{ Auth::user()->isAuthor() ? 'Admin' : ' ' }}</a></li>
              <li class="nav-item dropdown">
                  <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                      {{ Auth::user()->getFullName() }} <span class="caret"></span>
                  </a>

                  <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i> Logout</a>
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



    <div class="container-fluid">

            @yield('content')

    </div><!-- container -->






    <!-- JavaScripts -->
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>

  </body>

</html>
