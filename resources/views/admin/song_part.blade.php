
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a Song Parts Name")

@section('setup', 'active')



@section('content')

    @include('layouts.flashing')



    <div class="row">
        <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2 col-sm-10 offset-sm-1">                


            @if (isset($song_part))
                <h2>Update Song Parts Name</h2>
                {!! Form::model( $song_part, array('route' => array('song_parts.update', $song_part->id), 'method' => 'put', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}

            @else
                <h2>Create New Song Parts Name</h2>
                {!! Form::open(array('action' => 'Admin\SongPartController@store', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}

            @endif


            <div class="row">
                <div class="col-sm-6 text-sm-right">
                    {!! Form::label('sequence', 'Song Parts Sequence Number'); !!} <i class="red">*</i><br>
                </div>
                <div class="col-sm-6">
                    {!! Form::number('sequence'); !!}
                </div>
            </div>            

            <div class="row">
                <div class="col-sm-6 text-sm-right">
                    {!! Form::label('name', 'Song Parts Name'); !!} <i class="red">*</i><br>
                </div>
                <div class="col-sm-6">
                    {!! Form::text('name'); !!}
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 text-sm-right">
                    {!! Form::label('code', 'Song Parts Code'); !!} <i class="red">*</i><br>
                </div>
                <div class="col-sm-6">
                    {!! Form::text('code'); !!}
                </div>
            </div>            


            <div class="row">
    
                <div class="col-sm-6 text-sm-right">
                    @if (isset($song_part))
                        {!! Form::submit('Update', ['class'=>'btn btn-outline-success submit-button disabled']); !!}
                        </div>
                        <div class="col-sm-6 text-sm-right">
                            <a class="btn btn-sm btn-danger"  role="button" href="{{ url('admin/song_parts/'.$song_part->id) }}/delete">
                                <i class="fa fa-trash" > </i> &nbsp; Delete
                            </a>

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
                        <tr>
                            <th>{{ $part->sequence }}</th>
                            <td>{{ $part->name }}</td>
                            <td>{{ $part->code }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
    
@stop