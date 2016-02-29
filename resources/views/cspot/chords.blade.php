
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Show Chords")

@section('plan', 'active')



@section('content')


    @include('layouts.flashing')


    <div class="bg-secopndary underscore row">
        <div class="col-xl-6">
            <h5 class="text-xs-center" style="margin-bottom: 0">

                <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/next/chords#main') }}"
                    class="btn btn-secondary pull-xs-right" role="button">
                    <i class="fa fa-angle-double-right fa-lg"></i>
                </a>

                <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/previous/chords#main') }}"
                    class="btn btn-secondary pull-xs-left" role="button">
                    <i class="fa fa-angle-double-left fa-lg"></i>
                </a> 

                <span class="">
                    <small>Item No {{$item->seq_no}} -</small>
                    @if ($item->song_id && $item->song->title)
                        {{ $item->song->title }}
                    @else
                        {{ $item->comment }}
                    @endif
                </span>

            </h5>
        </div>


        <div class="col-xl-6">

            <div class="btn-group pull-xs-left">
                @if ($item->song_id && $item->song->youtube_id)
                    <a href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}" 
                        target="new" class="pull-xs-right btn btn-primary">
                    <i class="red fa fa-youtube-play fa-lg"></i>&nbsp;</a>
                @else
                    <a href="#" disabled=""
                       class="pull-xs-right btn btn-secondary-outline">
                    <i class="fa fa-youtube-play fa-lg"></i>&nbsp;</a>
                @endif
            </div>

            <!-- Dropdown button -->
            <div class="btn-group pull-xs-right">
                <button type="button" class="btn btn-primary dropdown-toggle" 
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Go to
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    @foreach ($items as $menu_item)
                        <a class="dropdown-item nowrap" 
                            href="{{ url('cspot/items/').'/'.$menu_item->id }}">
                            <small>{{ $menu_item->seq_no }}</small> &nbsp; 
                            @if ($menu_item->song_id && $menu_item->song->title)
                                <i class="fa fa-music">&nbsp;</i>{{ $menu_item->song->title }}
                            @else
                                {{ $menu_item->comment }}
                            @endif
                        </a>
                    @endforeach
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" 
                        href="{{ url('cspot/plans/'.$item->plan_id.'/edit') }}">
                        <i class="fa fa-undo fa-lg"></i>
                        Back to plan overview
                    </a>
                </div>
            </div>
        </div>

    </div>




    <div id="main">

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

        <pre class="big">{{ $item->comment }}</pre>

        @if ($bibleTexts)
            <div class="big col-xl-6">
                @foreach ($bibleTexts as $btext)
                    <div>
                        {!! $btext->text !!}
                    </div>
                    <div class="small">
                        {!! $btext->copyright !!}
                    </div>
                    <hr>
                @endforeach
            </div>
        @endif

    </div>



    <nav class="navbar navbar-fixed-bottom bg-primary center">

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