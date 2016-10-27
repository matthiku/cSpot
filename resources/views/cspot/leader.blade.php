
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Leader's Script")

@section('plan', 'active')



@section('content')


    {{-- for now we do not want to show flash messages here --}}
    {{-- @include('layouts.flashing') --}}




    <!-- ================================================================================ -->
    <div id="main-content">
    <!-- ================================================================================ -->


        @if ($item->song_id )
            @if ($item->key)
                <h4 class="red">{{ $item->key }}</h4>
            @endif

            {{ $item->song->title_2=='slides' ? '(Info slide)' : '' }}
            @if ( count($item->song->files)>0 )
                <div class="mb-3">
                    @foreach ($item->song->files as $file)
                        <img class="figure-img img-fluid img-rounded"  
                            src="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">
                    @endforeach
                </div>
            @elseif ($item->song->title_2 != 'video')
                <div class="text-song">Sequence: {{ $item->song->sequence }}</div>
                <div class="text-song white-space-pre-wrap mb-3" id="lyrics">{{ $item->song->lyrics }}</div>

            {{-- insert videoclip or lyrics --}}
            @else
                <div class="hidden-xs-up" id="videoclip-url">{{ $item->song->title_2}}</div>
                <div class="text-present mb-3" id="present-lyrics">
                    <iframe width="560" height="315" frameborder="0" allowfullscreen
                        src="https://www.youtube.com/embed/{{ $item->song->youtube_id }}" ></iframe>
                </div>
            @endif


        
        @elseif ($item->files)
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
            <div class="jumbotron" id="item-comment">
                <h1 class="display-3 center">
                    <span class="text-muted">{{ $item->comment ? '('.$item->comment.')' : '' }}</span>
                </h1>
            </div>
        @endif

    </div>
    <!-- ================================================================================ -->



    @include('cspot.snippets.present_navbar')



@stop