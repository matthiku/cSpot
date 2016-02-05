
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a Song")

@section('songs', 'active')



@section('content')


    @include('layouts.flashing')


    @if (isset($song))

        <h2>Song Details</h2>

        {!! Form::model( $song, array(
            'route'  => array('cspot.songs.update', $song->id), 
            'method' => 'put', 
            'id'     => 'inputForm',
            'class'  => 'form-horizontal'
            )) !!}

    @else

        <h2>Add Song</h2>
        {!! Form::open(array('action' => 'Cspot\SongController@store', 'id' => 'inputForm')) !!}

    @endif


        <div class="row">
            <div class="col-xl-4">
                
                <div class="row form-group">
                   {!! Form::label('title', 'Song Title', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                   <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('title'); !!}</div>
                   @if ( isset($song) )
                        <a class="btn btn-default btn-sm" type="button" target="new" 
                            href="https://olr.ccli.com/search/results?SearchTerm={{ $song->title.' '.$song->title_2.' '.$song->author }}">
                            <i class="fa fa-search" > </i> &nbsp; CCLI search by song titles and author
                        </a>
                    @endif
                </div>
                <div class="row form-group">
                    {!! Form::label('title_2', 'Subtitle', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                    <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('title_2'); !!}</div>
                </div>
                <div class="row form-group">
                    {!! Form::label('song_no', 'CCLI Song No', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                    <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::number('song_no'); !!}</div>
                    @if ( isset($song) )
                        <a class="btn btn-default btn-sm" type="button" target="new" 
                            href="https://olr.ccli.com/search/results?SearchTerm={{ $song->song_no }}">
                            <i class="fa fa-search" > </i> &nbsp; CCLI look-up (if number exists)
                        </a>
                    @endif
                </div>
                <div class="row form-group">
                    {!! Form::label('book_ref', 'Book Ref.', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                    <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('book_ref'); !!}</div>
                </div>
                <div class="row form-group">
                    {!! Form::label('author', 'Author', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                    <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('author'); !!}</div>
                </div>
                <div class="row form-group">
                    {!! Form::label('license', 'Select a license:', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                    <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 c-inputs-stacked">
                        <big class="pull-xs-right">
                            <a tabindex="0" href="#"
                                data-container="body" data-toggle="tooltip" data-placement="left" 
                                title="The type of license can be retrieved from the CCLI database (see link above). &nbsp; PD = Public Domain.">
                                <i class="fa fa-question-circle"></i></a>
                        </big>
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
                    {!! Form::label('sequence', 'Sequence', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                    <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('sequence'); !!}</div>
                </div>
                <div class="row form-group">
                    {!! Form::label('youtube_id', 'Youtube ID', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                    <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('youtube_id'); !!}</div>
                    @if ( isset($song) )
                        <a class="btn btn-default btn-sm" type="button" target="new" 
                            href="https://www.youtube.com/results?search_query={{ $song->title }}">
                            <i class="fa fa-youtube" > </i> &nbsp; YouTube search
                        </a>
                    @endif
                </div>
                <div class="row form-group">
                    {!! Form::label('link', 'Link(s)', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                    <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('link'); !!}</div>
                </div>

            </div>
            <div class="col-xl-8">

                {!! Form::label('lyrics', 'Lyrics'); !!}<br/>
                {!! Form::textarea('lyrics'); !!}

            </div>
        </div>


        <div class="row">
            <div class="col-xl-4">
                <div class="row">

                    <div class="col-xs-4">
                    @if (isset($song))

                        {!! Form::submit('Save changes', ['class'=>'full-width']); !!}
                        
                        @if (Auth::user()->isAdmin())
                            </div><div class="col-xs-4">
                            <a class="btn btn-danger btn-sm" type="button" href="{{ url('cspot/songs/'.$song->id) }}/delete">
                                <i class="fa fa-trash" > </i> &nbsp; Delete
                            </a>
                        @endif
                    @else
                        {!! Form::submit('Submit', ['class'=>'fully-width']); !!}
                    @endif
                    </div>

                    <div class="col-xs-4">
                        <a href="{{ url('cspot/songs') }}">{!! Form::button('Cancel', ['class'=>'fully-width']); !!}</a>
                    </div>

                </div>
            </div>
        </div>

    {!! Form::close() !!}


    <script type="text/javascript">document.forms.inputForm.title.focus()</script>

    
@stop