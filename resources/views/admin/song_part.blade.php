
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a OnSong Parts Name")

@section('setup', 'active')



@section('content')

    @include('layouts.flashing')



    <div class="row">
        <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2 col-sm-10 offset-sm-1">                


            @if (isset($song_part))
                <h2>Update OnSong Parts Name</h2>
                {!! Form::model( $song_part, array('route' => array('song_parts.update', $song_part->id), 'method' => 'put', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}

            @else
                <h2>Create New Song Parts Name</h2>
                {!! Form::open(array('action' => 'Admin\SongPartController@store', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}

            @endif


            <div class="row">
                <div class="col-sm-6 text-sm-right">
                    {!! Form::label('sequence', 'OnSong Parts Sequence Number'); !!} <i class="red">*</i><br>
                </div>
                <div class="col-sm-6">
                    {!! Form::number('sequence'); !!}
                    <button type="button" class="btn btn-sm btn-secondary" data-container="body" data-toggle="popover" data-placement="right"
                        data-content="This determines the sequence of this part name in the drop-down list for adding a new song part.">&#10067;</button>
                </div>
            </div>            

            <div class="row">
                <div class="col-sm-6 text-sm-right">
                    {!! Form::label('name', 'OnSong Parts Name'); !!} <i class="red">*</i><br>
                </div>
                <div class="col-sm-6">
                    {!! Form::text('name'); !!}
                    <button type="button" class="btn btn-sm btn-secondary" data-container="body" data-toggle="popover" data-placement="right"
                        data-content="This name is shown in the Chords presentation and any listings of OnSong parts.">&#10067;</button>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 text-sm-right">
                    {!! Form::label('code', 'OnSong Parts Code'); !!} <i class="red">*</i><br>
                </div>
                <div class="col-sm-6">
                    @if ( !isset($song_part)  ||  (isset($song_part) && $song_part->code!='m') )
                        {!! Form::text('code'); !!}
                        <small>&nbsp;<strong>Note:</strong> An incorrect value here will have a significant impact on the way songs and chords are shown in the presentation!</small>
                    @else
                        <span class="btn btn-sm btn-secondary disabled">{{ $song_part->code }}</span>
                        <small>
                            &nbsp;<strong>Note:</strong>
                            OnSong parts using this code will be shown as-is in the chords presentation but ignored in the lyrics presentation.<br>
                            See also the <a href="http://www.onsongapp.com/docs/features/formats/onsong/metadata" target="new">OnSong documentation</a>
                        </small>
                    @endif
                </div>
            </div>


            <div class="row">
    
                <div class="col-sm-6 text-sm-right">
                    @if (isset($song_part))
                        {!! Form::submit('Update', ['class'=>'btn btn-outline-success submit-button disabled']); !!}
                        </div>
                        <div class="col-sm-6 text-sm-right">
                            @if ( ! $song_part->onsongs->count() )
                                <a class="btn btn-sm btn-danger"  role="button" href="{{ url('admin/song_parts/'.$song_part->id) }}/delete">
                                    <i class="fa fa-trash" > </i> &nbsp; Delete
                                </a>
                            @endif

                    @else
                        {!! Form::submit('Submit', ['class'=>'btn btn-outline-success submit-button disabled']); !!}

                    @endif

                    <a href="{{ url('admin/song_parts/') }}">{!! Form::button('Cancel', ['class'=>'btn btn-sm btn-secondary cancel-button']); !!}</a>
                </div>
            </div>

            {!! Form::close() !!}
            
            
            <span class="small"><i class="red">*</i> = mandatory field(s) &nbsp;</span>

            <small class="float-sm-right">see also <a href="http://www.onsongapp.com/docs/features/formats/onsong/">the OnSong File Format manual</a></small>

            <hr>


            <small class="float-sm-right">(click to edit)</small>
            
            <h5>List of existing Song Parts Names:</h5>


            <table class="table table-striped table-sm">
                <thead class="thead-default">
                    <tr>
                        <th>SeqNo</th>
                        <th>Name</th>
                        <th>Code</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($song_parts as $part)
                        <tr class="link" onclick="location.href='{{ url('admin/song_parts/'.$part->id) }}/edit'">
                            <th>{{ $part->sequence }}</th>
                            <td>{{ $part->name }}</td>
                            <td>{{ $part->code }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if( Auth::user()->isEditor() )
                <a class="btn btn-outline-primary float-xs-right" href='{{ url('admin/song_parts/create') }}'>
                    <i class="fa fa-plus"> </i>
                    <span class="hidden-sm-down"> &nbsp; Add a new OnSong Part</span>
                    <span class="hidden-md-up">Add New</span>
                </a>
            @endif


        </div>
    </div>
    
@stop