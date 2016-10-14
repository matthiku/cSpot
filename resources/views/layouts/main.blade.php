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


    <meta name="csrf-token" content="{{ csrf_token() }}">    

    <title>c-SPOT @yield('title')</title>

    <!-- composed CSS -->
    <link href="{{ url('css/c-spot.css') }}" rel="stylesheet" />
    <!-- composed JavaScript -->
    <script src="{{ url('js/c-spot.js') }}"></script>

    

    <script>
        var __app_url = "{{ url('/') }}";
        var cSpot = {};
        cSpot.user = JSON.parse('{!! addslashes( json_encode(Auth::user(), JSON_HEX_APOS | JSON_HEX_QUOT ) ) !!}');

        cSpot.lastSongUpdated_at = "{{ $lastSongUpdated_at }}";

        cSpot.env = {};
        cSpot.env.songSelectUrl = "{{ env("SONGSELECT_URL", 'https://songselect.ccli.com/Songs/') }}"

        cSpot.const = {};
        cSpot.const.waitspinner = '<i class="fa fa-spinner fa-spin fa-fw"></i>';

        cSpot.routes = {};
        cSpot.routes.apiNextEvent  = '{{ route('api.next.event'        ) }}';
        cSpot.routes.apiAddFiles   = '{{ route('cspot.api.addfile'     ) }}';
        cSpot.routes.apiAddNote    = '{{ route('api.addNote'           ) }}';
        cSpot.routes.apiUpload     = '{{ route('cspot.api.upload'      ) }}';
        cSpot.routes.apiItems      = '{{ route('cspot.api.item'        ) }}';
        cSpot.routes.apiItemUpdate = '{{ route('cspot.api.item.update' ) }}';
        cSpot.routes.apiPlanUpdate = '{{ route('api.plan.update'       ) }}';
        cSpot.routes.apiItemsFileUnlink = '{{ route('api.items.file.unlink' ) }}';
        cSpot.routes.apiSongsFileUnlink = '{{ route('api.songs.file.unlink' ) }}';

        cSpot.presentation = {};
        cSpot.presentation.sync = false;
        cSpot.presentation.mainPresenterSetURL = '{{ route('presentation.mainPresenter.set') }}';

        @if( Request::is('*/present') || Request::is('*/chords') || Request::is('*/sheetmusic') || Request::is('*/leader') )

            // keep track of current background image
            cSpot.presentation.currentBGimage = 0;
            cSpot.presentation.BGimageCount = 0;

            // get relevant ids of current slides
            cSpot.presentation.plan_id = {{ $item->plan_id }};
            cSpot.presentation.item_id = {{ $item->id      }};
            cSpot.presentation.seq_no  = {{ $item->seq_no  }};
            cSpot.presentation.max_seq_no = {{ $item->plan->lastItem()->seq_no }};

            // set offline mode (using cahced items) as default
            cSpot.presentation.useOfflineMode = true;
        @endif

        {{-- only on presentation pages --}}
        @if( env('PRESENTATION_ENABLE_SYNC', 'false') 
            && (Request::is('*/present') || Request::is('*/chords') || Request::is('*/sheetmusic') || Request::is('*/leader')) )

            cSpot.presentation.slide = 'start';     // the initial SLIDE name
            cSpot.presentation.mainPresenter = JSON.parse('{!! json_encode($serverSideMainPresenter, JSON_HEX_APOS | JSON_HEX_QUOT ) !!}');

            // simple function to determine if the current user is the MP
            function isPresenter() {
                if (cSpot.user.id == cSpot.presentation.mainPresenter.id)
                    return true;
                return false;
            }

            cSpot.presentation.setPositionURL = '{{ route('presentation.position.set') }}';

            // prepare Server-Sent Events
            var es = new EventSource("{{ route('presentation.sync') }}");
            // handle generic messages
            es.onmessage = function(e) {
                  console.log(e);
            };

            // handle advetisements of new Show Positions
            es.addEventListener("syncPresentation", function(e) {
                cSpot.presentation.syncData = JSON.parse(e.data);
                ;;;console.log('New sync request received: ' + e.data);
                // has user requested a syncchronisation?
                if (cSpot.presentation.sync) {
                    // call function to sync 
                    syncPresentation(cSpot.presentation.syncData);
                }
            });

            // handle advertisements of new MPs
            es.addEventListener("newMainPresenter", function(e) {
                cSpot.presentation.mainPresenter = JSON.parse(e.data);
                // are we not longer MP?
                if (!isPresenter()) {
                    // make sure the MP checkbox is no longer checked!
                    $('#configMainPresenter').prop( "checked", false);
                    // make sure the Sync checkbox is visible!
                    $('#configSyncPresentation').parent().parent().parent().show();
                }
                // write the new MP name into checkbox label
                $('.showPresenterName').text(' ('+cSpot.presentation.mainPresenter.name+')')
            });


            // Function to inform server of current position
            function sendShowPosition(slideName) {
                cSpot.presentation.slide = slideName;
                if (isPresenter()) {
                    var data = {
                            plan_id : cSpot.presentation.plan_id,
                            item_id : cSpot.presentation.item_id,
                            slide   : slideName,
                        }
                    ;;;console.log('sending show position: '+JSON.stringify(data));
                    $.ajax({
                        url: cSpot.presentation.setPositionURL,
                        type: 'PUT',
                        data: data,
                    });
                }
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
