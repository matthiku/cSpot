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
</head>

<body id="app-layout">
    <nav class="tek-nav navbar navbar-dark bg-inverse">

      <a class="navbar-brand" href="#">c-SPOT</a>

      <ul class="nav navbar-nav">
        <li class="nav-item {{ Request::is('classifieds/create') ? '' : 'active' }}">
          <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
        </li>
        @if (Auth::user())
        <li class="nav-item {{ Request::is('classifieds/create') ? 'active' : '' }}">
          <a class="nav-link" href="/classifieds/create">Add Something</a>
        </li>
        @endif
      </ul>

      <ul class="nav navbar-nav pull-xs-right">
          <!-- Authentication Links -->
          @if (Auth::guest())
              <!-- <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Login</a></li> -->
              <li class="nav-item"><a class="nav-link" href="{{ url('/register') }}">Register</a></li>
          @else
              <li class="nav-item dropdown">
                  <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                      {{ Auth::user()->name }} <span class="caret"></span>
                  </a>

                  <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i> Logout</a>
                  </div>
              </li>
          @endif
      </ul>

      @if (Auth::guest())
      <form class="form-inline pull-xs-right" method="POST" role="form" action="{{ url('/login') }}">
        {!! csrf_field() !!}
        <div class="form-group">
          <label class="sr-only" for="inputEmail">Email address</label>
          <input type="email" name="email" class="form-control-sm" id="inputEmail" placeholder="Enter email">
        </div>
        <div class="form-group">
          <label class="sr-only" for="inputPassword">Password</label>
          <input type="password" name="password" class="form-control-sm" id="inputPassword" placeholder="Password">
        </div>
        <div class="checkbox">
          <label>
            <input type="checkbox"name="remember"> Remember me
          </label>
        </div>
        <button type="submit" class="btn btn-sm btn-primary">Sign in</button> &nbsp; &nbsp; &nbsp;
      </form>
      @endif


    </nav>



    <div class="container">

      <div class="row">


        <!-- 
              sidebar 
          -->
        <div class="col-md-2">
          @section('sidebar')
            <div class="list-group">
              <a href="/classifieds" class="list-group-item active">
                All Items
              </a>

                
              <!-- foreach($categories as $category) -->
                <a href="#" class="list-group-item">item 1</a>
                <a href="#" class="list-group-item">item 2</a>
              <!-- endforeach -->
            </div>
            <br>
            <form class="form-inline" method="GET" role="form" action="{{ url('classifieds/search') }}">
              {!! csrf_field() !!}
              <div class="form-group">
                <input type="text" name="searchString" class="form-control-sm search-input" id="searchString" placeholder="search listings...">
              </div>
              <!-- <button type="submit" class="btn btn-sm btn-primary search-btn">Search</button> -->
            </form>
          @show
        </div>



        <!--
             main page content 
          -->
        <div class="col-md-10">

            @include('flash')

            @yield('content')

        </div>




      </div><!-- row -->

    </div><!-- container -->





    <!-- JavaScripts -->
    <script src="/js/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="/js/bootstrap.min.js"></script>

</body>

</html>
