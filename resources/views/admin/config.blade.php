<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Application Customization")




@section('content')


    @include('layouts.flashing')


    <h3>Application Customization</h3>

    {!! Form::open(array('action' => 'Admin\ConfigController@update', 'id' => 'inputForm')) !!}

        <p>{!! Form::label('church_name', 'Church Name') !!}<br>
           {!! Form::text('church_name', env('CHURCH_NAME')); !!}
       </p>

        <p>{!! Form::label('church_url', 'Church URL') !!}<br>
           {!! Form::text('church_url', env('CHURCH_URL')); !!}
       </p>

        <p>Current Logo: 
            <img src="{{ url('images/'.env('CHURCH_LOGO_FILENAME')) }}">
            <br>
            {!! Form::label('file', 'Upload a new one:'); !!}
            {!! Form::file('file'); !!}
        </p>

        <hr>
        {!! Form::submit('Apply Changes'); !!}
        <p>(Note: Some settings will only be visible after reloading)</p>

    {!! Form::close() !!}


@stop