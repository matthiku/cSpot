<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Application Customization")




@section('content')


    @include('layouts.flashing')


    

        {!! Form::open( array('action' => 'Admin\CustomizeController@update', 'id' => 'inputForm', 'files'  => true, ) ) !!}

            <div class="row">

                <div class="col-md-6">
                    <h3>Application Customisaztion</h3>
                    <h5>Customise this App for your Organisation</h5>

                    <div class="form-group full-width">
                        {!! Form::label('church_name', 'Church Name:', ['class' => 'm-b-0']) !!}<br>
                        {!! Form::text('church_name', env('CHURCH_NAME')); !!}
                   </div>

                    <div class="form-group">
                        {!! Form::label('church_ccli', 'Church CCLI license number:', ['class' => 'm-b-0']) !!}<br>
                        {!! Form::number('church_ccli', env('CHURCH_CCLI')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('church_url', 'Church website:', ['class' => 'm-b-0']) !!}<br>
                        {!! Form::text('church_url', env('CHURCH_URL')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('church_youtube_playlist_id', 'Church YouTube Playlist ID:', ['class' => 'm-b-0']) !!}
                        <small>(should contain playlist of songs for an upcoming event)</small><br>
                        {!! Form::text('church_youtube_playlist_id', env('CHURCH_YOUTUBE_PLAYLIST_ID')); !!}
                   </div>


                    <div class="form-group">Current Favicon: 
                        <img src="{{ url($logoPath.'favicon.ico') }}">
                        <br>
                        {!! Form::label('favicon_file', 'Upload a new one:'); !!}
                        {!! Form::file('favicon_file'); !!}<br>
                        <small>This must be a valid icon file! See <a target="_new" href="https://en.wikipedia.org/wiki/Favicon">Wikipedia article</a></small>
                    </div>

                    <div class="form-group">Your Church Logo: 
                        <img src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}" height="30px">
                        <br>
                        {!! Form::label('logo_file', 'Upload a new one:'); !!}
                        {!! Form::file('logo_file'); !!}
                    </div>
                </div>

                <hr class="hidden-md-up hr-big">

                <div class="col-md-6">
                    <h3>System Configuration</h3>

                    {{-- default value for checkbox --}}
                    <input type="hidden" name="enable_sync" value="false">
                    <div class="form-group">
                        {!! Form::checkbox('enable_sync', 'true', env('PRESENTATION_ENABLE_SYNC')); !!}
                        {!! Form::label('enable_sync', 'Presentation Synchronisation', ['class' => 'm-b-0']) !!}
                        <div class="small">Provide option to synchronise the presentation between partizipating clients</div>
                   </div>

                    {{-- default value for checkbox --}}
                    <input type="hidden" name="enable_debug" value="false">
                    <div class="form-group">
                        {!! Form::checkbox('enable_debug', 'true', env('APP_DEBUG')); !!}
                        {!! Form::label('enable_debug', 'Enable debugging', ['class' => 'm-b-0']) !!}
                        <div class="small text-danger">Warning! Debugging slows down the app! Use only temporarily.</div>
                   </div>


                    <div class="form-group full-width">
                        {!! Form::label('songselect_url', 'SongSelect URL:', ['class' => 'm-b-0']) !!} <small> (to show specific songs)</small><br>
                        {!! Form::text('songselect_url', env('SONGSELECT_URL')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('songselect_search', 'SongSelect Search URL:', ['class' => 'm-b-0']) !!} <small> (to search for songs)</small><br>
                        {!! Form::text('songselect_search', env('SONGSELECT_SEARCH')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('ccli_report_url', 'CCLI Report URL:', ['class' => 'm-b-0']) !!} <small> (to report song usage)</small><br>
                        {!! Form::text('ccli_report_url', env('CCLI_REPORT_URL')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('hymnal_net_play', 'Hymnal.net Song URL:', ['class' => 'm-b-0']) !!} <small> (to play song)</small><br>
                        {!! Form::text('hymnal_net_play', env('HYMNAL.NET_PLAY')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('hymnal_net_search', 'Hymnal.net Search URL:', ['class' => 'm-b-0']) !!} <small> (to search for a song)</small><br>
                        {!! Form::text('hymnal_net_search', env('HYMNAL.NET_SEARCH')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('youtube_play', 'YouTube Song URL:', ['class' => 'm-b-0']) !!} <small> (to play song)</small><br>
                        {!! Form::text('youtube_play', env('YOUTUBE_PLAY')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('youtube_search', 'YouTube Search URL:', ['class' => 'm-b-0']) !!} <small> (to search for a song)</small><br>
                        {!! Form::text('youtube_search', env('YOUTUBE_SEARCH')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('youtube_playlist_url', 'YouTube Playlist URL:', ['class' => 'm-b-0']) !!} <small> (to run a playlist on YouTube)</small><br>
                        {!! Form::text('youtube_playlist_url', env('YOUTUBE_PLAYLIST_URL')); !!}
                   </div>


                </div>


            </div>

            <hr class="hr-big">

            {!! Form::submit('Apply Changes'); !!}
            <p>(Note: Some changes will only be visible after reloading)</p>

        {!! Form::close() !!}

@stop