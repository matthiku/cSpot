
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Show Chords")

@section('plan', 'active')



@section('content')


    @include('layouts.flashing')




    <!-- ================================================================================ -->
    <div id="main">
    <!-- ================================================================================ -->

        @if ($item->song_id )
            @if ($item->key)
                <h4 class="red">{{ $item->key }}</h4>
            @endif
            @if ($item->song->chords )
                <div class="m-b-3">
                    <pre class="text-song big" id="chords">{{ $item->song->chords }}</pre>
                </div>
            @else
                <pre class="text-song big m-b-3" id="lyrics">{{ $item->song->lyrics }}</pre>
            @endif
        @endif


        
        @if ($item->files)
            @foreach ($item->files as $file)
                @include ('cspot.snippets.present_files')
            @endforeach
        @endif


        @if ($bibleTexts)
            <div class="col-xl-6" id="bibletext">
                @foreach ($bibleTexts as $btext)
                    <h3>{{ $btext->display }} ({{ $btext->version_abbreviation }})</h3>
                    <div class="big">{!! $btext->text !!}</div>
                    <div class="small">{!! $btext->copyright !!}</div>
                    <hr>
                @endforeach
            </div>
        @else
            <div class="jumbotron" id="comment">
                <h1 class="display-3 center">
                    <span class="text-muted">{{ $item->comment ? '('.$item->comment.')' : '' }}</span>
                </h1>
            </div>
        @endif

    </div>
    <!-- ================================================================================ -->

    @include('cspot.snippets.present_navbar')


@stop