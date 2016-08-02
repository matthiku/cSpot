<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Application Customization")




@section('content')


    @include('layouts.flashing')


    <h3>Application Customization</h3>

    <div class="full-width">
        {!! Form::open( array('action' => 'Admin\CustomizeController@update', 'id' => 'inputForm', 'files'  => true, ) ) !!}

            <div class="form-group">
                {!! Form::label('church_name', 'Church Name') !!}<br>
                {!! Form::text('church_name', env('CHURCH_NAME')); !!}
           </div>

            <div class="form-group">
                {!! Form::label('church_url', 'Church URL') !!}<br>
                {!! Form::text('church_url', env('CHURCH_URL')); !!}
           </div>

            <div class="form-group">
                {!! Form::label('church_ccli', 'Church CCLI number') !!}<br>
                {!! Form::number('church_ccli', env('CHURCH_CCLI')); !!}
           </div>

            <div class="form-group">Current Favicon: 
                <img src="{{ url($logoPath.'favicon.ico') }}">
                <br>
                {!! Form::label('favicon_file', 'Upload a new one:'); !!}
                {!! Form::file('favicon_file'); !!}<br>
                <small>This must be a valid icon file! See <a target="_new" href="https://en.wikipedia.org/wiki/Favicon">Wikipedia article</a></small>
            </div>
            <div class="form-group">Current Logo: 
                <img src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}" height="30px">
                <br>
                {!! Form::label('logo_file', 'Upload a new one:'); !!}
                {!! Form::file('logo_file'); !!}
            </div>

            <hr>
            {{-- default value for checkbox --}}
            <input type="hidden" name="enable_sync" value="false">
            <div class="form-group">
                {!! Form::label('enable_sync', 'Allow Presentation Synchronisation between clients:') !!}<br>
                {!! Form::checkbox('enable_sync', 'true', env('PRESENTATION_ENABLE_SYNC')); !!}
           </div>

            <hr>
            {!! Form::submit('Apply Changes'); !!}
            <p>(Note: Some settings will only be visible after reloading)</p>

        {!! Form::close() !!}
    </div>


@stop