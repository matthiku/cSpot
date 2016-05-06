<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Application Customization")




@section('content')


    @include('layouts.flashing')


    <h3>Application Customization</h3>

    {!! Form::open( array('action' => 'Admin\ConfigController@update', 'id' => 'inputForm', 'files'  => true, ) ) !!}

        <p>{!! Form::label('church_name', 'Church Name') !!}<br>
           {!! Form::text('church_name', env('CHURCH_NAME')); !!}
       </p>

        <p>{!! Form::label('church_url', 'Church URL') !!}<br>
           {!! Form::text('church_url', env('CHURCH_URL')); !!}
       </p>

        <p>Current Favicon: 
            <img src="{{ url('images/favicon.ico') }}">
            <br>
            {!! Form::label('favicon_file', 'Upload a new one:'); !!}
            {!! Form::file('favicon_file'); !!}<br>
            This must be a valid icon file! See <a target="_new" href="https://en.wikipedia.org/wiki/Favicon">Wikipedia article</a>
        </p>
        <p>Current Logo: 
            <img src="{{ url('images/'.env('CHURCH_LOGO_FILENAME')) }}">
            <br>
            {!! Form::label('logo_file', 'Upload a new one:'); !!}
            {!! Form::file('logo_file'); !!}
        </p>

        <hr>
        {!! Form::submit('Apply Changes'); !!}
        <p>(Note: Some settings will only be visible after reloading)</p>

    {!! Form::close() !!}


@stop