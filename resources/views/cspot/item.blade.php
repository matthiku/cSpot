
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

                    @if( Auth::user()->ownsPlan($item->plan_id) )
                        &nbsp; {!! Form::submit('Save changes'); !!}
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

        <div id="col-1-comment" class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-xs-12">

            <div class="row form-group">

                <div class="col-xs-12 full-width">
                    {!! Form::label('comment', 'Comments, notes or a Bible Reference'); !!}
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

            <div id="col-2-song" class="col-xl-4 col-lg-3 col-md-6 col-sm-12 col-xs-12 bg-grey">
                <div class="col-xs-12 center">

                    @if ( ! Session::has('songs') )

                        <div class="row song-details form-group">
                            <h5>{{ $item->song->title ? $item->song->title : '' }}
                                @if ($item->song->title_2)
                                    <br>({{ $item->song->title_2 }})
                                @endif
                            </h5>
                            @if ($item->song->book_ref)
                                <h6>{{ $item->song->book_ref }}</h6>
                            @endif
                        </div>

                        <div class="row song-details">

                            <h6>Musical Instructions (e.g. Key)</h6>
                            <p>{!! Form::text('key'); !!}
                            </p>

                            @if ($item->song->youtube_id)
                                <a href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}" 
                                    target="new" class="pull-xs-left" 
                                      title="Play on YouTube" data-toggle="tooltip">
                                    <i class="red fa fa-youtube-play"></i>&nbsp;play</a> &nbsp; &nbsp; &nbsp; &nbsp;
                            @endif

                            <a href="#" 
                                onclick="$('.song-search').show();$('.song-details').hide();" 
                                title="Select another song" data-toggle="tooltip"
                            ><i class="fa fa-exchange"></i>&nbsp;change song</a> &nbsp; &nbsp;

                            <a href="#" class="pull-xs-right" 
                                onclick="location.href='{{ route('cspot.songs.edit', $item->song_id) }}'" 
                                  title="Edit details of this song" data-toggle="tooltip"
                            ><i class="fa fa-edit"></i>&nbsp;edit song</a>

                        </div>

                        <script>
                            $(document).ready( function() {
                                $('.song-search').hide();
                            });
                        </script>

                    @endif


                    <div class="row form-group">
                        @if ( Session::has('songs'))
                            Select a new song and click 'Save changes':
                            <div class="c-inputs-stacked">
                                @foreach (Session::get('songs') as $song)
                                    <label class="c-input c-radio" title="{{$song->lyrics}}" data-toggle="tooltip">
                                        <input value="{{$song->id}}" name="song_id" type="radio">
                                        <span class="c-indicator"></span>
                                        {{ $song->book_ref ? $song->book_ref.',' : '' }}
                                        {{ $song->title }}
                                        {{ $song->title_2 ? '('. $song->title_2 .')' : '' }},
                                    </label>
                                @endforeach
                            </div>
                            Or search for still another song. Just
                        @else
                            <div class="song-search">
                                To search for another song,
                            </div>
                        @endif
                        <div class="song-search">
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
                </div>
            </div>

            @if ( ! Session::has('songs') )
                <div id="col-3-lyrics" class="col-xl-5 col-lg-6 col-md-12 col-sm-12 col-xs-12 center">
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
            <div id="col-2-song-search" class="col-lg-6 col-md-12">
                @if ( Session::has('songs'))
                    Select a song:
                    <div class="c-inputs-stacked">
                        @foreach (Session::get('songs') as $song)
                            <label class="c-input c-radio border" title="{{$song->lyrics}}" data-toggle="tooltip" data-placement="bottom">
                                <input value="{{$song->id}}" name="song_id" type="radio">
                                <span class="c-indicator">                                    
                                </span>{{ $song->book_ref ? $song->book_ref.',' : '' 
                                }}{{ $song->title}}{{ $song->title_2 ? ' ('. $song->title_2 .')' : '' }}
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