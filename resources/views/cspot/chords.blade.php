
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Show Chords")

@section('plan', 'active')



@section('content')


    @include('layouts.flashing')

    <!-- remove main navbar -->
    <script>
        $('#main-navbar').detach();
    </script>



    <div class="bg-primary  row">

        <!-- 
            go to next slide 
        -->
        <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/next/chords#main') }}"
            class="btn btn-warning pull-xs-right" role="button" id="go-next-item">
            <i class="fa fa-angle-double-right fa-lg"></i>
        </a>

        <!-- 
            Dropdown Menu Button
        -->
        <div class="btn-group pull-xs-right m-r-1">

            <button type="button" class="btn btn-info dropdown-toggle" 
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
                        href="{{ url('cspot/items/').'/'.$menu_item->id }}">
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
                    <a class="dropdown-item" 
                        href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/edit') }}">
                        <i class="fa fa-pencil"></i>
                        Edit this item
                    </a>
                @endif
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" 
                    href="{{ url('cspot/plans/'.$item->plan_id.'/edit') }}">
                    <i class="fa fa-undo"></i>
                    Back to plan overview
                </a>
            </div>

        </div>

        <!-- 
            Youtube button 
        -->
        <div class="btn-group pull-xs-right m-r-1">
            @if ($item->song_id && $item->song->youtube_id)
                <a href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}" 
                    target="new" class="pull-xs-right btn btn-info">
                <i class="red fa fa-youtube-play fa-lg"></i>&nbsp;</a>
            @else
                <a href="#" disabled=""
                   class="pull-xs-right btn btn-secondary-outline">
                <i class="fa fa-youtube-play fa-lg"></i>&nbsp;</a>
            @endif
        </div>

        <!-- 
            go to previous slide (item)
        -->
        <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/previous/chords#main') }}"
            class="btn btn-warning pull-xs-left" role="button" id="go-previous-item">
            <i class="fa fa-angle-double-left fa-lg"></i>
        </a> 

        <!-- 
            go to first/last slide 
        -->
        <a href="{{ url('cspot/items/').'/'.$item->plan->firstItem()->id }}" id="go-first-item"></a>
        <a href="{{ url('cspot/items/').'/'.$item->plan->lastItem()->id  }}" id="go-last-item" ></a>


        <!-- 
            show slide title 
        -->
        <h5 class="text-xs-center" style="line-height: inherit;">
            <span>
                <small>Item {{$item->seq_no}}</small>
                @if ($item->song_id && $item->song->title)
                    - {{ $item->song->title }}
                @else
                    - {{ $item->comment }}
                @endif
            </span>
        </h5>


    </div>




    <div id="main-content">

        @if ($item->song_id )
            @if ($item->key)
                <h4 class="red">{{ $item->key }}</h4>
            @endif
            @if ($item->song->chords )
                <pre class="big" id="chords">{{ $item->song->chords }}</pre>
            @else
                <pre class="big">{{ $item->song->lyrics }}</pre>
            @endif
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
            <div>({{ $item->comment }})</div>
        @endif

    </div>



    <nav class="navbar navbar-fixed-bottom bg-primary center p-b-0 p-t-0">

        <ul class="nav navbar-nav pull-xs-right">
            <li>
                <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/next/chords#main') }}"
                    class="nav-item btn btn-warning" role="button">
                    <i class="fa fa-angle-double-right fa-lg"></i>
                </a>
            </li>
        </ul>

        <span class="nav navbar-nav big center">
            <small>Item {{$item->seq_no}} -</small>
            @if ($item->song_id && $item->song->title)
                {{ $item->song->title }}
            @else
                {{ $item->comment }}
            @endif
        </span>

        <ul class="nav navbar-nav pull-xs-left">
            <li>
                <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/previous/chords#main') }}"
                    class="nav-link btn btn-warning" role="button">
                    <i class="fa fa-angle-double-left fa-lg"></i>
                </a> 
            </li>
        </ul>

    </nav>


@stop