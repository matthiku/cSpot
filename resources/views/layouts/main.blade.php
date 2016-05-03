<!DOCTYPE html>
<html lang="en">

<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<?php \Carbon\Carbon::setLocale('en');?>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Allow this to be installed an app on the device's home screen -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" sizes="192x192" href="{{ url('images/cspoticon.png') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">    

    <title>c-SPOT @yield('title')</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('css/font-awesome.min.css') }}" rel="stylesheet">

    <link href="{{ url('css/jquery-ui.min.css') }}" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{ url('css/style.css') }}" rel="stylesheet">
    <link href="{{ url('css/signin.css') }}" rel="stylesheet">

    <script src="{{ url('js/tether.min.js') }}"></script>
    <script src="{{ url('js/jquery.min.js') }}"></script>
    <script src="{{ url('js/jquery-ui.min.js') }}"></script>
    <script src="{{ url('js/jquery.ui.touch-punch.min.js') }}"></script>
    <script src="{{ url('js/jquery.detect_swipe.js') }}"></script>

    <script src="{{ url('js/helpers.js') }}"></script>
    <script>
        var __app_url = "{{ url('/') }}";
    </script>

</head>




<body id="app-layout">


    @include ('layouts.messages')
    
    @include ('layouts.navbar')


    <div class="container-fluid app-content">

            @yield('content')

    </div><!-- container fluid -->






    <!-- JavaScripts -->
    <script src="{{ url('js/bootstrap.min.js') }}"></script>

  </body>

</html>
