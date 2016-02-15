
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a Song")

@section('songs', 'active')



@section('content')


    @include('layouts.flashing')


    @if (isset($song))
        {!! Form::model( $song, array(
            'route'  => array('cspot.songs.update', $song->id), 
            'method' => 'put', 
            'id'     => 'inputForm',
            'class'  => 'form-horizontal'
            )) !!}
    @else
        {!! Form::open(array('action' => 'Cspot\SongController@store', 'id' => 'inputForm')) !!}
    @endif


    <div class="row">

        <div class="col-md-6 col-lg-7 col-xl-8 md-center">
            @if (isset($song))
                <h2 class="hidden-xs-down">Song Details</h2>
            @else
                <h2 class="hidden-xs-down">Add Song</h2>
            @endif
        </div>

        <div class="col-md-6 col-lg-5 col-xl-4">
            <div class="row">

                <div class="col-xs-4">
                    @if (isset($song))

                        <big>{!! Form::submit('Save changes', ['class'=>'full-width']); !!}</big>
                        
                        @if (Auth::user()->isAdmin())
                            </div>
                            <div class="col-xs-4">
                            <a class="btn btn-danger" type="button" href="{{ url('cspot/songs/'.$song->id) }}/delete">
                                <i class="fa fa-trash" > </i> Delete Song
                            </a>
                        @endif
                    @else
                        <big>{!! Form::submit('Submit', ['class'=>'fully-width']); !!}</big>
                    @endif
                </div>

                <div class="col-xs-4">
                    <big><a href="{{ url('cspot/songs') }}">{!! Form::button('All Songs', ['class'=>'fully-width']); !!}</a></big>
                </div>

            </div>
        </div>

    </div>



    <hr>



    <div class="row">
        <div class="col-xl-6">

            
            <div class="row form-group">
               {!! Form::label('title', 'Song Title', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
               <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('title'); !!}</div>
               @if ( isset($song) )
                    &nbsp; <a class="btn btn-sm" type="button" target="new" 
                        href="https://olr.ccli.com/search/results?SearchTerm={{ $song->title.' '.$song->title_2.' '.$song->author }}">
                        <i class="fa fa-search" > </i> CCLI search 
                    </a>
                    &nbsp; <a class="btn btn-sm" type="button" target="new" 
                        href="https://www.hymnal.net/en/search/all/all/{{ $song->title.' '.$song->title_2 }}">
                        <i class="fa fa-search" > </i> hymnal.net search 
                    </a>
                @endif
            </div>


            <div class="row form-group">
                {!! Form::label('title_2', 'Subtitle', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('title_2'); !!}</div>
            </div>


            <div class="row form-group">
                {!! Form::label('author', 'Author', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('author'); !!}</div>
            </div>


            <div class="row form-group">
                {!! Form::label('book_ref', 'Book Ref.', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8">{!! Form::text('book_ref'); !!}</div>
            </div>


            <div class="row form-group">
                <div class="col-sm-4 col-md-3 col-lg-2 col-xl-4">                    
                    {!! Form::label('license', 'Select a license:'); !!}
                    <big>
                        <a tabindex="0" href="#"
                            data-container="body" data-toggle="tooltip"
                            title="The type of license can be retrieved from the CCLI database (see link above). &nbsp; PD = Public Domain.">
                            <i class="fa fa-question-circle"></i></a>
                    </big>
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 c-inputs-stacked">
                    @foreach ($licensesEnum as $vers)
                        <label class="c-input c-radio">
                            <input id="radio1" name="license" type="radio"
                                value="{{ $vers }}"
                                @if ( isset($song) && $vers==$song->license )
                                    checked="" 
                                @endif
                                >
                            <span class="c-indicator"></span>
                            {{ $vers }} &nbsp; &nbsp;
                        </label>
                    @endforeach
                    @if ($errors->has('license'))
                        <br>
                        <span class="help-block">
                            <strong>{{ $errors->first('license') }}</strong>
                        </span>
                    @endif
                </div>
            </div>


            <div class="row form-group">
                {!! Form::label('hymnaldotnet_id', 'Hymnal.Net id', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8">{!! Form::number('hymnaldotnet_id'); !!}
                    @if ( isset($song)  && $song->hymnaldotnet_id > 0 )
                        <a class="btn btn-sm" type="button" target="new" 
                            href="https://www.hymnal.net/en/hymn/h/{{ $song->hymnaldotnet_id }}">
                            <i class="fa fa-music" > </i> See song on Hymnal.Net
                        </a>
                    @endif
                </div>
            </div>


            <div class="row form-group">
                {!! Form::label('ccli_no', 'CCLI Song No', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8">{!! Form::number('ccli_no'); !!}
                    @if ( isset($song)  && $song->ccli_no > 10000 )
                        <a class="btn btn-sm" type="button" target="new" 
                            href="https://olr.ccli.com/search/results?SearchTerm={{ $song->ccli_no }}">
                            <i class="fa fa-search" > </i> CCLI look-up
                        </a>
                    @endif
                </div>
            </div>


            <div class="row form-group">
                {!! Form::label('sequence', 'Sequence', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('sequence'); !!}</div>
            </div>


            <div class="row form-group">
                {!! Form::label('youtube_id', 'Youtube ID', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('youtube_id'); !!}</div>
                @if ( isset($song) )
                    &nbsp; <a class="btn btn-sm" type="button" target="new" 
                        href="https://www.youtube.com/results?search_query={{ $song->title }}">
                        <big class="fa fa-youtube" > </big> YouTube search
                    </a>
                    @if ( strlen($song->youtube_id)>0 )
                        &nbsp; <a class="btn btn-sm" type="button" target="new" 
                            href="https://www.youtube.com/watch?v={{ $song->youtube_id }}">
                            <big class="fa fa-youtube-play"></big> Play on Youtube</a>
                    @endif
                @endif
            </div>


            <div class="row form-group">
                {!! Form::label('link', 'Link(s)', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('link'); !!}</div>

            </div>

        </div>


        <div class="col-xl-6">

            @if ( isset($song) )
                Song Usage Count: <strong>{{ $song->items->count() }}</strong>
            @endif
            <hr>

            {!! Form::label('lyrics', 'Lyrics'); !!}<br/>
            {!! Form::textarea('lyrics'); !!}
            <br>
            {!! Form::label('chords', 'Chords'); !!}<br/>
            {!! Form::textarea('chords'); !!}

        </div>

    </div>



    {!! Form::close() !!}


    <script type="text/javascript">document.forms.inputForm.title.focus()</script>

    
@stop
