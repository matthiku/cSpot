
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Show Chords")

@section('plan', 'active')



@section('content')


    @include('layouts.flashing')




    <!-- ================================================================================ -->
    <div id="main-content">
    <!-- ================================================================================ -->

        @if ($item->song_id )
            @if ($item->key)
                <h4 class="red">{{ $item->key }}</h4>
            @endif
            @if ($item->song->chords )
                <div class="m-b-3">
                    <pre class="text-song" id="chords">{{ $item->song->chords }}</pre>
                </div>
            @else
                @if ( count($item->song->files)>0 )
                    <div class="m-b-3">
                        @foreach ($item->song->files as $file)
                            <img class="figure-img img-fluid img-rounded"  
                                src="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">
                        @endforeach
                    </div>
                @elseif ($item->song->title_2 != 'video')
                    (chords missing!)
                @else
                    <pre class="text-song m-b-3" id="lyrics">{{ $item->song->lyrics }}</pre>
                @endif
            @endif
        @endif


        
        @if ($item->files)
            @foreach ($item->files as $file)
                <figure class="figure">
                    <img class="figure-img img-fluid img-rounded full-width" 
                           src="{{ url(config('files.uploads.webpath')).'/thumb-'.$file->token }}">
                </figure>
            @endforeach
        @endif


        @if ($bibleTexts)
            <div class="col-xl-6" id="bibletext">
                @foreach ($bibleTexts as $btext)
                    <h3>{{ $btext->display }} ({{ $btext->version_abbreviation }})</h3>
                    <div>{!! $btext->text !!}</div>
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