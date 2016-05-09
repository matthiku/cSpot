
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Present Plan")

@section('plan', 'active')



@section('content')


    @include('layouts.flashing')

    <!-- remove main navbar -->
    <script>
        $('#main-navbar').detach();

        $(document).ready(function() {
            $('body').addClass('bg-inverse');
            // for certain bible text sources:
            $('.bible-text-present>.p>.v').prepend('<br>');

            // check if user has changed the default font size for the presentation
            fontSize = getLocalStorValue('.text-present_font-size');
            if ($.isNumeric(fontSize)) {
                $('.text-present').css('font-size', parseInt(fontSize));
            }
            fontSize = getLocalStorValue('.bible-text-present>.p_font-size');
            if ($.isNumeric(fontSize)) {
               $('.bible-text-present>.p').css('font-size', parseInt(fontSize));
            }
        });

    </script>





    <!-- ================================================================================ -->
    <div id="main-content" class="bg-inverse">

        @if ($item->song_id )
            @if ($item->song->lyrics )
                <pre class="text-present m-b-3" 
                    id="lyrics">{{ $item->song->lyrics }}
                </pre>
                <br><br><br><br>
            @endif
        @endif

        @if ($item->files)
            @foreach ($item->files as $file)
                @include ('cspot.snippets.present_files')
            @endforeach
            <br><br><br><br>
        @endif

        @if ($bibleTexts)
            <div class="bg-inverse bible-text-present">
                @foreach ($bibleTexts as $btext)
                    <h1>{{ $btext->display }} ({{ $btext->version_abbreviation }})</h1>
                    <div class="bible-text-present">{!! $btext->text !!}</div>
                    <div class="small">{!! $btext->copyright !!}</div>
                    <hr>
                @endforeach
            </div>
            <br><br><br><br><br><br>
        @endif

    </div>
    <!-- ================================================================================ -->



    <nav class="navbar navbar-fixed-bottom bg-primary center p-b-0 p-t-0">

        <ul class="nav navbar-nav pull-xs-right">
            <li>
                <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/next/present') }}"
                    class="nav-item btn btn-sm btn-warning" role="button" id="go-next-item">
                    <i class="fa fa-angle-double-right fa-lg"></i>
                </a>
            </li>
        </ul>

        <span class="nav navbar-nav center">
            <small>Item {{$item->seq_no}} -</small>
            @if ($item->song_id && $item->song->title)
                {{ $item->song->title }}
            @else
                {{ $item->comment }}
            @endif
        </span>


        
        <!-- 
            DropUP Menu Button
        -->
        <div class="btn-group dropup pull-xs-right m-r-1">

            <button type="button" class="btn btn-sm btn-info dropdown-toggle" 
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Go to
            </button>
            <div class="dropdown-menu dropdown-menu-right bg-faded">
                @foreach ($items as $menu_item)
                    <a class="dropdown-item nowrap 
                        @if ($item->id == $menu_item->id)
                            bg-info
                        @endif
                        "
                        href="{{ url('cspot/items/').'/'.$menu_item->id.'/present' }}">
                        <small class="hide-sm-down">{{ $menu_item->seq_no }}</small> &nbsp; 
                        @if ($menu_item->song_id && $menu_item->song->title)
                            <i class="fa fa-music">&nbsp;</i><strong>{{ $menu_item->song->title }}</strong>
                        @else
                            {{ $menu_item->comment }}
                        @endif
                    </a>
                @endforeach
                @if (Auth::user()->ownsPlan($item->plan_id))
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" id="go-edit"
                        href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/edit') }}">
                        <i class="fa fa-pencil"></i>
                        Edit this item
                    </a>
                @endif
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" id="go-back"
                    href="{{ url('cspot/plans/'.$item->plan_id) }}">
                    <i class="fa fa-undo"></i>
                    Back to plan overview
                </a>
            </div>

        </div>
        <!-- 
            go to first/last slide 
        -->
        <a href="{{ url('cspot/items/').'/'.$item->plan->firstItem()->id.'/present' }}" id="go-first-item"></a>
        <a href="{{ url('cspot/items/').'/'.$item->plan->lastItem()->id.'/present'  }}" id="go-last-item" ></a>


        <ul class="nav navbar-nav pull-xs-left">
            <li>
                <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/previous/present') }}"
                    class="nav-item btn btn-sm btn-warning" role="button" id="go-previous-item">
                    <i class="fa fa-angle-double-left fa-lg"></i>
                </a> 
<!--                 <a href="#" onclick="requestFullScreen(document.body);" 
                        class="nav-item nav-link btn btn-sm btn-info" role="button">
                    <i class="fa fa-tv fa-lg"></i>
                    <span class="hidden-sm-down">Fullscreen</span>
                </a>
 -->
                <a href="#" onclick="decFontSize(['.text-present', '.bible-text-present>.p']);" 
                        title="decrease font size" id="decr-font"
                        class="nav-item btn btn-sm btn-info" role="button">
                    A <i class="fa fa-minus fa-lg"></i>
                </a>
                <a href="#" onclick="incFontSize(['.text-present', '.bible-text-present>.p']);" 
                        title="increase font size" id="incr-font"
                        class="nav-item btn btn-sm btn-info" role="button">
                    A <i class="fa fa-plus fa-lg"></i>
                </a>
            </li>
        </ul>

    </nav>


@stop