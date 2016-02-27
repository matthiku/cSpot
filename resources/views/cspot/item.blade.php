
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<?php Use Carbon\Carbon; ?>

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
                        Review Item No {{$seq_no}}
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
                    <h2>Add Item No {{ $seq_no }}.0</h2>
                    <h5>to the Service plan (id {{ $plan->id }}) for {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</h5>
            @endif
        </div>
    </div>


    <hr>


    <div class="row">

        @if ( isset($item->song->id) && $item->song_id<>0 )


            {!! Form::hidden('song_id', $item->song_id) !!}


            <div id="col-2-song" class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="card card-block text-xs-center" style="padding-bottom: 0;">

                    <div class="row song-details form-group">
                        <h5 class="card-title">
                            <i class="pull-xs-left fa fa-music"></i>
                            <i class="pull-xs-right fa fa-music"></i>
                            {{ $item->song->title ? $item->song->title : '' }}
                            @if ($item->song->title_2)
                                <br>({{ $item->song->title_2 }})
                            @endif
                        </h5>
                        @if ($item->song->book_ref)
                            <h6>{{ $item->song->book_ref }}</h6>
                        @endif
                    </div>

                    <div class="card-text song-details">

                        <h6>Musical Instructions (e.g. Key)</h6>
                        @if( Auth::user()->ownsPlan($item->plan_id) )
                            <p>{!! Form::text('key'); !!}</p>
                        @else
                            <p>{!! Form::text('key', $item->key, ['disabled'=>'disabled']); !!}</p>
                        @endif

                        <div class="row">
                            Note: 
                            @if ( $usageCount )
                                Song was used before in <strong>{{ $usageCount }}</strong> service(s) -
                                lastly <strong title="{{ $newestUsage->date }}">
                                    {{ Carbon::now()->diffForHumans( $newestUsage->date, true ) }} ago</strong>
                            @else
                                Song was never used before in a service
                            @endif
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-xs-12 col-sm-4 full-btn">
                                @if ($item->song->youtube_id)
                                    <a href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}" 
                                        target="new" class="fully-width btn btn-primary-outline btn-sm" 
                                          title="Play on YouTube" data-toggle="tooltip">
                                    <i class="red fa fa-youtube-play"></i>&nbsp;play</a>
                                @endif
                            </div>
                            @if ( Auth::user()->ownsPlan($item->plan_id) )
                                <div class="col-xs-12 col-sm-4 full-btn">
                                    <a href="#" class="fully-width btn btn-primary-outline btn-sm"
                                        onclick="$('.song-search').show();$('.song-details').hide();" 
                                        title="Select another song" data-toggle="tooltip"
                                    ><i class="fa fa-exchange"></i>&nbsp;change song</a>
                                </div>
                            @endif
                            @if (Auth::user()->isEditor() )
                                <div class="col-xs-12 col-sm-4 full-btn">
                                    <a href="#" class="fully-width btn btn-primary-outline btn-sm" 
                                        onclick="location.href='{{ route('cspot.songs.edit', $item->song_id) }}'" 
                                          title="Edit details of this song" data-toggle="tooltip"
                                    ><i class="fa fa-edit"></i>&nbsp;edit song</a>
                                </div>
                            @endif
                        </div>
                        

                    </div>

                    <script>
                        $(document).ready( function() {
                            $('.song-search').hide();
                        });
                    </script>


                    <div class="row form-group">
                        <div class="song-search">
                            To search for another song,
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

            <div id="col-3-lyrics" class="col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-12 center">
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

        @else
            @if( Auth::user()->ownsPlan($plan->id) )
                @if ( ! isset($item) ||  (isset($item) && ! $item->comment) )
                    <div id="col-2-song-search" class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        To search for a song,
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
            @endif
        @endif

        @if ( !isset($item) || (isset($item) && ! $item->song_id) )
            <!-- WAS: <div id="col-1-comment" class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-xs-12"> -->
            <div id="col-1-comment" class="col-xs-12">

                <div class="row form-group bg-grey">

                    <div class="col-xs-12 full-width">
                        {!! Form::label('comment', 'Comments or notes', ['id'=>'comment-label']); !!}
                        <p>
                            @if( Auth::user()->ownsPlan($plan->id) )
                                {!! Form::text('comment'); !!}
                            @else
                                {!! Form::text('comment', $item->comment, ['disabled'=>'disabled']); !!}
                            @endif
                            @if ($errors->has('comment'))
                                <br><span class="help-block">
                                    <strong>{{ $errors->first('comment') }}</strong>
                                </span>
                            @endif
                        </p>
                    </div> 

                    
                    @if( Auth::user()->ownsPlan($plan->id) )
                    <div class="col-xs-12 full-width p-b-1">
                        <h6>Add Bible Reference(s)</h6>

                        <select name="from-book" id="from-book" class="pull-xs-left" 
                                onchange="showNextSelect('from', 'chapter')">
                            <option selected="TRUE" value=" "> </option>
                            @foreach ($bibleBooks->getArrayOfBooks() as $book)
                                <option value="{{ $book }}">{{ $book }}</option>
                            @endforeach                        
                        </select>&nbsp;

                        <span class="select-reference" style="display: none;">                    
                            ch.
                            <select name="from-chapter" id="from-chapter" style="display: none;" 
                                    onchange="showNextSelect('from', 'verse')">
                                <option selected="" value=" "> </option>
                            </select>
                            verse 
                            <select name="from-verse" id="from-verse" style="display: none;"
                                    onchange="showNextSelect('to', 'verse')">
                                <option selected="" value=" "> </option>
                            </select>
                            to 
                            <select name="to-verse" id="to-verse" style="display: none;">
                                <option selected="" value=" "> </option>
                            </select>
                        </span>
                    </div>
                    @endif


                    <div class="col-xs-12 select-version" style="display: none;">
                        {!! Form::label('version', 'Select version:'); !!}
                        <select name="version" id="version" onchange="populateComment()">
                            <option {{ isset($item) ? '' : 'selected' }}>
                            </option>
                            @foreach ($versionsEnum as $vers)
                                <option value="{{ $vers }}">{{ $vers }}
                                </option>
                            @endforeach
                        </select>
                    </div>  

                    <div class="col-lg-6 col-sm-12" id="bible-passages">
                        @foreach ($bibleTexts as $btext)
                            <h5>{{ $btext->display }} ({{ $btext->version_abbreviation }})</h5>
                            <div>
                                {!! $btext->text !!}
                            </div>
                            <div class="small">
                                {!! $btext->copyright !!}
                            </div>
                            <hr>
                        @endforeach
                        <div id="waiting" style="display: none;"><i class="fa fa-spinner fa-spin"></i> leafing through the pages....</div>
                    </div>

                </div>
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


    @if (! isset($item))
        <script>
            // set focus on main input field (only when adding a new item)
            document.forms.inputForm.search.focus();
            // add class in order to identify main input field later after flash messages
            document.forms.inputForm.search.setAttribute('class', 'main-input');
        </script>
    @endif

    
@stop