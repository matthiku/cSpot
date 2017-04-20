
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Present Plan")

@section('plans', 'active')



@section('content')


    @include('layouts.flashing')

    <!-- remove main navbar -->
    <script>
        $('#main-navbar').detach();
    </script>





    {{-- ================================================================================ --}}
    <div id="main-content">
    {{-- ================================================================================ --}}



        {{-- is this item indeed a song? --}}
        @if ($item->song_id )

            @if ($item->key)
                <h4 class="red">{{ $item->key }}</h4>
            @endif

            {{-- check if we have proper musci sheets for this song --}}
            @if ($type=='sheetmusic' && count($item->song->files)>0 )
                <div class="mb-3">
                    @foreach ($item->song->files as $file)
                        <img class="figure-img img-fluid img-rounded"
                            src="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">
                    @endforeach
                </div>

            {{-- alternatively, check if there are onsong chords --}}
            @elseif ( isset($onSongChords)  &&  $onSongChords->count() )

                @include ('cspot.snippets.present_chords')

            {{-- otherwise, there could be ordinary chords --}}
            @elseif ($item->song->chords )
                <div class="mb-3">
                    <pre class="big" id="chords">{{ $item->song->chords }}</pre>
                </div>

            {{-- if all else fails, show the plain lyrics --}}
            @else
                <pre class="big mb-3">{{ $item->song->onsongs ? $item->song->onSongLyrics() : $item->song->lyrics }}</pre>

            @endif
        @endif



        {{-- does this item has one or more files linked to it? --}}
        @if ($item->files->count())
            @foreach ($item->files as $file)
                <figure class="figure">
                    <img class="figure-img img-fluid img-rounded full-width"
                           src="{{ url(config('files.uploads.webpath')).'/thumb-'.$file->token }}">
                </figure>
            @endforeach
        @else
            <script>
                // make sure the image use all but does not exceed the visible area
                function resizeSheetmusic() {
                    $('.figure-img').css({
                        'height': $(window).height()-45,
                        'width':  $(window).width(),
                        'display' : 'inline',
                    });
                }
                $(document).ready( function() {
                    resizeSheetmusic();
                    $(document).on('resize', function() {
                        resizeSheetmusic();
                    })
                });
            </script>
        @endif


        {{-- maybe this item has a scripture text --}}
        @if ($bibleTexts)
            <div class="col-xl-6">
                @foreach ($bibleTexts as $btext)
                    <h3>{{ $btext->display }} ({{ $btext->version_abbreviation }})</h3>
                    <div class="big">{!! $btext->text !!}</div>
                    <div class="small">{!! $btext->copyright !!}</div>
                    <hr>
                @endforeach
            </div>
        {{-- in all other cases, show a jumbotron and include the item comment text --}}
        @else
            @if ($item->comment)
                <div class="jumbotron">
                    <h1 class="display-3 center">
                        <span class="text-muted">({{ $item->comment }})</span>
                    </h1>
                </div>
            @endif
        @endif

    </div>
    <!-- ================================================================================ -->



    @include('cspot.snippets.present_navbar')


@stop
