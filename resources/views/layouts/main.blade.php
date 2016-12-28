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
    <link rel="icon" sizes="192x192" href="{{ url($logoPath.'favicon.ico') }}" />

    <link rel="shortcut icon" sizes="192x192" href="{{ url($logoPath.'cspoticon.png') }}">
    <link rel="shortcut icon" sizes="128x128" href="{{ url($logoPath.'cspoticon128.png') }}">

    <link rel="manifest" href="{{ url('/') }}/manifest.json">

    <link href="https://fonts.googleapis.com/css?family=Lora|Shadows+Into+Light|Vollkorn" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">    

    <title>c-SPOT @yield('title')</title>

    <!-- composed CSS -->
    <link rel="stylesheet" href="{{ elixir('css/c-spot.css') }}">

    <script>
        var cSpot = {};
        cSpot.appURL         = "{{ url('/') }}";
        cSpot.getConfigRoute = "{{ route('APIconfigGet') }}";
    </script>

    <!-- load cSpot JavaScript App -->
    <script src="{{ elixir('js/c-spot.js') }}"></script>

</head>




<body id="app-layout"
    @if (Request::is('*/present'))
        style="background-color: #373a3c;"
    @endif
    >


    @include ('layouts.messages')
    

    @unless (Request::is('*/present') || Request::is('*/chords'))

        @include ('layouts.navbar')

    @endunless


    <div class="container-fluid app-content">

            @yield('content')

    </div><!-- container fluid -->





</body>

</html>
