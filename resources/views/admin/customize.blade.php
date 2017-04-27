<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Application Customization")




@section('content')


    @include('layouts.flashing')




        {!! Form::open( array('action' => 'Admin\CustomizeController@update', 'id' => 'inputForm', 'files'  => true, ) ) !!}

            <div class="row">

                <div class="col-md-6">
                    <h3 class="text-success">Application Customization</h3>
                    <h5>Customise this App for your Organisation</h5>

                    <div class="form-group full-width">
                        {!! Form::label('church_name', 'Church Name:', ['class' => 'mb-0']) !!}<br>
                        {!! Form::text('church_name', env('CHURCH_NAME')); !!}
                   </div>

                    <div class="form-group">
                        {!! Form::label('church_ccli', 'Church CCLI license number:', ['class' => 'mb-0']) !!}<br>
                        {!! Form::number('church_ccli', env('CHURCH_CCLI')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('church_url', 'Link to Church website:', ['class' => 'mb-0']) !!}<br>
                        {!! Form::text('church_url', env('CHURCH_URL')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('church_youtube_playlist_id', 'Church YouTube Playlist ID:', ['class' => 'mb-0']) !!}
                        <small>(should contain playlist of songs for an upcoming event)</small><br>
                        {!! Form::text('church_youtube_playlist_id', env('CHURCH_YOUTUBE_PLAYLIST_ID')); !!}
                   </div>


                    <div class="card card-block">
                        <h5 class="card-title">
                            Current Favicon (Browser Icon):
                            <img src="{{ url($logoPath.'favicon.ico') }}">
                        </h5>
                        <p class="card-text">
                            {!! Form::label('favicon_file', 'Upload a new one:'); !!} &nbsp; &nbsp;
                            {!! Form::file('favicon_file'); !!}
                            <br>
                            <small>This must be a valid icon file! See <a class="card-link" target="_new" href="https://en.wikipedia.org/wiki/Favicon">Wikipedia article</a></small>
                        </p>
                    </div>

                    <div class="card card-block">
                        <h5 class="card-title">
                            Your Church Logo:
                            <img src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}" height="30px">
                        </h5>
                        <p class="card-text">
                            {!! Form::label('logo_file', 'Upload a new one:'); !!}
                            {!! Form::file('logo_file'); !!}
                        </p>
                    </div>

                    <div class="form-group full-width">
                        {!! Form::label('bible_versions', 'List of supported Bible Versions:', ['class' => 'mb-0']) !!}
                        <small>(This list will be used when adding scripture items to an event plan)</small><br>
                        {!! Form::text('bible_versions', env('BIBLE_VERSIONS')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('hymnbook_name', 'Hymnbook Name:', ['class' => 'mb-0']) !!}<br>
                        {!! Form::text('hymnbook_name', env('CHURCH_HYMNBOOK_NAME')); !!}
                   </div>


                </div>


                <hr class="hidden-md-up hr-big">


                <div class="col-md-6">
                    <h3 class="text-success">System Configuration</h3>

                    <div class="row">
                        <div class="col-md-6">
                            {{-- default value for checkbox --}}
                            <input type="hidden" name="enable_sync" value="false">
                            <div class="form-group">
                                {!! Form::checkbox('enable_sync', 'true', env('PRESENTATION_ENABLE_SYNC')); !!}
                                {!! Form::label('enable_sync', 'Presentation Synchronisation', ['class' => 'mb-0']) !!}
                                <div class="small">Provide option to synchronise the presentation between partizipating clients</div>
                           </div>
                       </div>
                       <div class="col-md-6">
                            {{-- default value for checkbox --}}
                            <input type="hidden" name="enable_debug" value="false">
                            <div class="form-group">
                                {!! Form::checkbox('enable_debug', 'true', env('APP_DEBUG')); !!}
                                {!! Form::label('enable_debug', 'Enable debugging', ['class' => 'mb-0']) !!}
                                <div class="small text-danger">Warning! Debugging slows down the app! Use only temporarily.</div>
                           </div>
                       </div>
                   </div>


                    <div class="form-group full-width">
                        {!! Form::label('app_url', 'C-SPOT Base URL:', ['class' => 'mb-0']) !!} <small> (e.g. http://cspot.dev)</small><br>
                        {!! Form::text('app_url', env('APP_URL')); !!}
                   </div>


                    <div class="form-group full-width">
                        {!! Form::label('songselect_url', 'SongSelect URL:', ['class' => 'mb-0']) !!} <small> (to show specific songs)</small><br>
                        {!! Form::text('songselect_url', env('SONGSELECT_URL')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('songselect_search', 'SongSelect Search URL:', ['class' => 'mb-0']) !!} <small> (to search for songs)</small><br>
                        {!! Form::text('songselect_search', env('SONGSELECT_SEARCH')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('ccli_report_url', 'CCLI Report URL:', ['class' => 'mb-0']) !!} <small> (to report song usage)</small><br>
                        {!! Form::text('ccli_report_url', env('CCLI_REPORT_URL')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('hymnal_net_play', 'Hymnal.net Song URL:', ['class' => 'mb-0']) !!} <small> (to play song)</small><br>
                        {!! Form::text('hymnal_net_play', env('HYMNAL.NET_PLAY')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('hymnal_net_search', 'Hymnal.net Search URL:', ['class' => 'mb-0']) !!} <small> (to search for a song)</small><br>
                        {!! Form::text('hymnal_net_search', env('HYMNAL.NET_SEARCH')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('youtube_play', 'YouTube Song URL:', ['class' => 'mb-0']) !!} <small> (to play song)</small><br>
                        {!! Form::text('youtube_play', env('YOUTUBE_PLAY')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('youtube_search', 'YouTube Search URL:', ['class' => 'mb-0']) !!} <small> (to search for a song)</small><br>
                        {!! Form::text('youtube_search', env('YOUTUBE_SEARCH')); !!}
                   </div>

                    <div class="form-group full-width">
                        {!! Form::label('youtube_playlist_url', 'YouTube Playlist URL:', ['class' => 'mb-0']) !!} <small> (to run a playlist on YouTube)</small><br>
                        {!! Form::text('youtube_playlist_url', env('YOUTUBE_PLAYLIST_URL')); !!}
                   </div>


                </div>


            </div>

            <hr class="hr-big">

            <div class="row">
                <div class="col-md-6">
                    {!! Form::submit('Apply Changes', ['class'=>'btn btn-primary btn-block']); !!}
                    <p>(Note: Some changes will only be visible after reloading)</p>
                </div>
            </div>

        {!! Form::close() !!}

@stop
