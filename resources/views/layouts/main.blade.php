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

    <meta name="csrf-token" content="{{ csrf_token() }}">    

    <title>c-SPOT @yield('title')</title>

    <!-- composed CSS -->
    <link href="{{ url('css/c-spot.css') }}" rel="stylesheet" />
    <!-- composed JavaScript -->
    <script src="{{ url('js/c-spot.js') }}"></script>

    <script>
        var __app_url = "{{ url('/') }}";
        var cSpot = {};
        cSpot.user = JSON.parse('{!! json_encode(Auth::user(), JSON_HEX_APOS | JSON_HEX_QUOT ) !!}');
        cSpot.presentation = {};
        cSpot.presentation.sync = false;
        cSpot.presentation.mainPresenter = JSON.parse('{!! json_encode($serverSideMainPresenter, JSON_HEX_APOS | JSON_HEX_QUOT ) !!}');
        cSpot.presentation.mainPresenterSetURL = '{{ route('presentation.mainPresenter.set') }}';

        @if (Request::is('*/present') || Request::is('*/chords') || Request::is('*/sheetmusic'))
            // first steps with Server-Sent Events
            var es = new EventSource("{{ route('presentation.sync') }}");
            es.onmessage = function(e) {
                  console.log(e);
            }
        @endif

    </script>

</head>




<body id="app-layout"
    @if (Request::is('*/present'))
        class="bg-inverse"
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
