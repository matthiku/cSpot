
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Present Plan")

@section('plan', 'active')



@section('content')


    @include('layouts.flashing')

    <!-- remove main navbar -->
    <script>
        $('#main-navbar').detach();
    </script>





    <!-- ================================================================================ -->
    <div id="main-content">

        @if ($item->song_id )
            @if ($item->key)
                <h4 class="red">{{ $item->key }}</h4>
            @endif
            @if ($type=='sheetmusic' && count($item->song->files)>0 )
                <div class="m-b-3">
                    @foreach ($item->song->files as $file)
                        <img class="figure-img img-fluid img-rounded"  
                            src="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">
                    @endforeach
                </div>
            @elseif ($item->song->chords )
                <div class="m-b-3">
                    <pre class="big" id="chords">{{ $item->song->chords }}</pre>
                </div>
            @else
                <pre class="big m-b-3">{{ $item->song->lyrics }}</pre>
            @endif
        @endif

        @if ($item->files)
            @foreach ($item->files as $file)
                @include ('cspot.snippets.present_files')
            @endforeach
        @endif
        

        @if ($bibleTexts)
            <div class="col-xl-6">
                @foreach ($bibleTexts as $btext)
                    <h3>{{ $btext->display }} ({{ $btext->version_abbreviation }})</h3>
                    <div class="big">{!! $btext->text !!}</div>
                    <div class="small">{!! $btext->copyright !!}</div>
                    <hr>
                @endforeach
            </div>
        @else
            <div class="jumbotron">
                <h1 class="display-3 center">
                    <span class="text-muted">{{ $item->comment ? '('.$item->comment.')' : '' }}</span>
                </h1>
            </div>
        @endif

    </div>
    <!-- ================================================================================ -->



    @include('cspot.snippets.present_navbar')


@stop