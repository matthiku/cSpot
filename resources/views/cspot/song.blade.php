@extends('layouts.main')

@section('title', "Create or Update a Song")

@section('songs', 'active')



@section('content')


    @include('layouts.flashing')


    @if (isset($song))
        <h2>Update a Song</h2>
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
            <div class="col-md-4">
                
                <div class="row form-group">
                   {!! Form::label('title', 'Song Title', ['class' => 'col-sm-4']); !!}
                   <div class="col-sm-8 full-width">{!! Form::text('title'); !!}</div>
                </div>
                <div class="row form-group">
                    {!! Form::label('title_2', 'Second Title', ['class' => 'col-sm-4']); !!}
                    <div class="col-sm-8 full-width">{!! Form::text('title_2'); !!}</div>
                </div>
                <div class="row form-group">
                    {!! Form::label('song_no', 'Song Number', ['class' => 'col-sm-4']); !!}
                    <div class="col-sm-8 full-width">{!! Form::number('song_no'); !!}</div>
                </div>
                <div class="row form-group">
                    {!! Form::label('book_ref', 'Book Ref.', ['class' => 'col-sm-4']); !!}
                    <div class="col-sm-8 full-width">{!! Form::text('book_ref'); !!}</div>
                </div>
                <div class="row form-group">
                    {!! Form::label('author', 'Author', ['class' => 'col-sm-4']); !!}
                    <div class="col-sm-8 full-width">{!! Form::text('author'); !!}</div>
                </div>
                <div class="row form-group">
                    {!! Form::label('sequence', 'Sequence', ['class' => 'col-sm-4']); !!}
                    <div class="col-sm-8 full-width">{!! Form::text('sequence'); !!}</div>
                </div>
                <div class="row form-group">
                    {!! Form::label('youtube_id', 'Youtube ID', ['class' => 'col-sm-4']); !!}
                    <div class="col-sm-8 full-width">{!! Form::text('youtube_id'); !!}</div>
                </div>
                <div class="row form-group">
                    {!! Form::label('link', 'Link(s)', ['class' => 'col-sm-4']); !!}
                    <div class="col-sm-8 full-width">{!! Form::text('link'); !!}</div>
                </div>

            </div>
            <div class="col-md-8">

                {!! Form::label('lyrics', 'Lyrics'); !!}<br/>
                {!! Form::textarea('lyrics'); !!}

            </div>
        </div>


        @if (isset($song))
            {!! Form::submit('Save changes'); !!}

            @if (Auth::user()->isAdmin())
                <a class="btn btn-danger btn-sm"  song="button" href="/cspot/songs/{{ $song->id }}/delete">
                    <i class="fa fa-trash" > </i> &nbsp; Delete
                </a>
            @endif
        @else
            {!! Form::submit('Submit'); !!}
        @endif
        <a href="/cspot/songs">{!! Form::button('Cancel'); !!}</a>

    {!! Form::close() !!}


    <script type="text/javascript">document.forms.inputForm.title.focus()</script>

    
@stop