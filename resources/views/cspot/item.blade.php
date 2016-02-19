
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update Plan Item")

@section('plan', 'active')



@section('content')


    @include('layouts.flashing')

    @if (isset($item))
        {!! Form::model( $item, array(
            'route'  => array('cspot.items.update', $item->id), 
            'method' => 'put', 
            'id'     => 'inputForm',
            'class'  => 'form-horizontal'
            )) !!}
    @else
        {!! Form::open(array('action' => 'Cspot\ItemController@store', 'id' => 'inputForm')) !!}
    @endif

    {!! Form::hidden('seq_no', $seq_no) !!}
    {!! Form::hidden('plan_id', isset($plan) ? $plan->id : $item->plan_id ) !!}



    <div class="row">
        <div class="col-md-6">
        @if (isset($item))
                <h2 class="nowrap">
                    <a href="{{ url('cspot/plans/'.$plan->id.'/items/'.$item->id.'/go/previous') }}"
                        class="btn btn-secondary" role="button"
                        title="go to previous item" data-toggle="tooltip" data-placement="right">
                        <i class="fa fa-angle-double-left fa-lg"></i>
                    </a> 
                    Update Item No {{$seq_no}}
                    <a href="{{ url('cspot/plans/'.$plan->id.'/items/'.$item->id.'/go/next') }}"
                        class="btn btn-secondary" role="button"
                        title="go to next item" data-toggle="tooltip" data-placement="right">
                        <i class="fa fa-angle-double-right fa-lg"></i>
                    </a>
                </h2>
                <h5 class="hidden-md-down">of the Service plan for {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</h5>
                <h4 class="hidden-lg-up">in plan for {{ $plan->date->formatLocalized('%a, %d %b') }}</h4>
            </div>

            <div class="col-md-6 text-xs-right nowrap">

                @if( Auth::user()->isEditor() || Auth::user()->id==$plan->leader_id || Auth::user()->id==$plan->teacher_id )
                    &nbsp; {!! Form::submit('Save changes'); !!}
                @endif

                @if (Auth::user()->isAdmin())
                    &nbsp; 
                    <a class="btn btn-danger btn-sm"  item="button" href="{{ url('cspot/items/'. $item->id .'/delete') }}">
                        <i class="fa fa-trash" > </i> 
                        &nbsp; Delete
                    </a>
                @endif
                &nbsp; 
                <a class="hidden-xs-down" href="{{ url('cspot/plans/'.$item->plan_id) }}/edit">{!! Form::button('Cancel - Back to Plan') !!}</a>
                <a class="hidden-sm-up" href="{{ url('cspot/plans/'.$item->plan_id) }}/edit">{!! Form::button('Cancel - Back') !!}</a>
        @else
                <h2>Add Item</h2>
                <h5>to the Service plan (id {{ $plan->id }}) for {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</h5>
        @endif
            </div>
    </div>


    <hr>

    <div class="row">

        <div class="col-lg-3 col-md-6">
            <br>
            <div class="row form-group">

                <div class="col-xs-12 full-width">
                    {!! Form::label('comment', 'Enter comment/note or a bible reference'); !!}
                    <p>
                        {!! Form::text('comment'); !!}
                        @if ($errors->has('comment'))
                            <br><span class="help-block">
                                <strong>{{ $errors->first('comment') }}</strong>
                            </span>
                        @endif
                    </p>
                </div> 

                <div class="col-xs-12">
                    {!! Form::label('version', 'For bible references, select a version:'); !!}
                    <br>
                    <select name="version" class="c-select">
                        <option {{ isset($item) ? '' : 'selected' }}>
                        </option>
                        @foreach ($versionsEnum as $vers)
                            <option 
                                @if ( isset($item) && $vers==$item->version )
                                    selected
                                @endif
                                value="{{ $vers }}">{{ $vers }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('version'))
                        <br>
                        <span class="help-block">
                            <strong>{{ $errors->first('version') }}</strong>
                        </span>
                    @endif
                </div>  

            </div>
        </div>

        @if ( isset($item->song->id) && $item->song_id<>0 )

            {!! Form::hidden('song_id', $item->song_id) !!}

            <div class="col-lg-3 col-md-6">
                @if ( ! Session::has('songs') )
                    <div class="row form-group link" 
                        onclick="location.href='{{ route('cspot.songs.edit', $item->song_id) }}'"
                          title="Click to edit this song" data-toggle="tooltip">

                        <h4>Song Number</h4>
                        <p>{{ $item->song->book_ref }}</p>

                        <h4>Song Title</h4>
                        <p> {{ isset($item->song->title) ? $item->song->title : '' }}
                            {{ isset($item->song->title_2) ? ' ('. $item->song->title_2 .')' : '' }}
                        </p>
                    </div>

                    <div class="row form-group">
                        <div class="pull-xs-right m-r-3 xl-big"><a target="new" href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}"
                                title="Play on YouTube" data-toggle="tooltip"><i class="red fa fa-youtube-play"></i></a>
                        </div>
                        <h6>Musical Instructions (e.g. Key)</h6>
                        <p>{!! Form::text('key'); !!}
                        </p>
                    </div>
                @endif

                <div class="row form-group">
                    @if ( Session::has('songs'))
                        Select a new song and click 'Save changes':
                        <div class="c-inputs-stacked">
                            @foreach (Session::get('songs') as $song)
                                <label class="c-input c-radio" title="{{$song->lyrics}}" data-toggle="tooltip">
                                    <input value="{{$song->id}}" name="song_id" type="radio">
                                    <span class="c-indicator"></span>
                                    {{$song->book_ref}}, 
                                    {{$song->title}}{{ $song->title_2 ? ' ('. $song->title_2 .')' : '' }},
                                </label>
                            @endforeach
                        </div>
                        Or search for still another song. Just
                    @else
                        <hr>
                        To search for another song,
                    @endif
                    {!! Form::label('search', 'enter song number, title or author or parts thereof:') !!}
                    {!! Form::text('search') !!}
                    <input type="submit" name="searchBtn" value="Search" />
                    @if ($errors->has('search'))
                        <br><span class="help-block">
                            <strong>{{ $errors->first('search') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            @if ( ! Session::has('songs') )
                <div class="col-lg-6 col-md-12">
                    <div class="row form-group link" id="lyrics" title="click to show chords!" data-toggle="tooltip"
                         onclick="$('#chords').show();$('#lyrics').hide();">
                        <h4>Lyrics</h4>
                        <pre>{{ $item->song->lyrics }}</pre>
                    </div>
                    <div class="row form-group link" id="chords" title="click to show lyrics!" data-toggle="tooltip"
                         onclick="$('#lyrics').show();$('#chords').hide();">
                        <h4>Chords</h4>
                        <pre>{{ $item->song->chords }}</pre>
                    </div>
                    <script>
                        
                    </script>
                </div>
            @endif

        @else
            <br>
            <div class="col-lg-6 col-md-12">
                @if ( Session::has('songs'))
                    Select a song:
                    <div class="c-inputs-stacked">
                        @foreach (Session::get('songs') as $song)
                            <label class="c-input c-radio" title="{{$song->lyrics}}" data-toggle="tooltip" data-placement="bottom">
                                <input value="{{$song->id}}" name="song_id" type="radio">
                                <span class="c-indicator"></span>
                                {{$song->book_ref}}, 
                                {{$song->title}}{{ $song->title_2 ? ' ('. $song->title_2 .')' : '' }},
                                {{$song->author}}
                            </label>
                        @endforeach
                    </div>
                    Or search for another song -
                @else
                    To search for a song,
                @endif
                {!! Form::label('search', 'enter song number, title or author or parts thereof:') !!}<br/>
                {!! Form::text('search') !!}
                <input type="submit" name="searchBtn" value="Search" />
                @if ($errors->has('search'))
                    <br><span class="help-block">
                        <strong>{{ $errors->first('search') }}</strong>
                    </span>
                @endif
            </div>
        @endif

    </div>

    @if (! isset($item))
        <!-- See if user wants to add more items to this plan -->
        <input type="hidden" name="moreItems" value="false">
        <div class="checkbox">
          <label>
            <input checked="checked" type="checkbox" value="Y" name="moreItems">
            Tick to add another item to this plan after saving this one
          </label>
        </div>                        
        {!! Form::submit('Submit'); !!}
        &nbsp; <a href="{{ url( 'cspot/plans/' . (isset($plan) ? $plan->id : $plan_id) )  }}/edit">{!! Form::button('Cancel - Back to Plan'); !!}</a>
    @endif

    {!! Form::close() !!}


    <script>
        // set focus on main input field
        document.forms.inputForm.search.focus();
        // add class in order to identify main input field later after flash messages
        document.forms.inputForm.search.setAttribute('class', 'main-input');
    </script>

    
@stop