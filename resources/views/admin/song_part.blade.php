
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a Song Parts Name")

@section('setup', 'active')



@section('content')

    @include('layouts.flashing')


    <div class="row">
        <div class="col-xl-4 offset-xl-4">                


            @if (isset($song_part))
                <h2>Update Song Parts Name</h2>
                {!! Form::model( $song_part, array('route' => array('song_parts.update', $song_part->id), 'method' => 'put', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}
            @else
                <h2>Create New Song Parts Name</h2>
                {!! Form::open(array('action' => 'Admin\SongPartController@store', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}
            @endif
                <p>{!! Form::label('sequence', 'Song Parts Sequence Number'); !!} <i class="red">*</i><br>
                   {!! Form::number('sequence'); !!}</p>

                <p>{!! Form::label('name', 'Song Parts Name'); !!} <i class="red">*</i><br>
                   {!! Form::text('name'); !!}</p>

                <p>{!! Form::label('code', 'Song Parts Code'); !!}<br>
                   {!! Form::text('code'); !!}</p>

            @if (isset($song_part))
                <p>{!! Form::submit('Update', ['class'=>'btn btn-outline-success submit-button disabled']); !!}</p>
                <hr>
                <a class="btn btn-danger"  role="button" href="{{ url('admin/song_parts/'.$song_part->id) }}/delete">
                    <i class="fa fa-trash" > </i> &nbsp; Delete
                </a>
            @else
                <p>{!! Form::submit('Submit', ['class'=>'btn btn-outline-success submit-button disabled']); !!}
            @endif

            <a href="{{ url('admin/song_parts/') }}">{!! Form::button('Cancel', ['class'=>'btn btn-secondary cancel-button']); !!}</a></p>
            {!! Form::close() !!}
            
            <span><i class="red">*</i> = mandatory field(s) &nbsp;</span>

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