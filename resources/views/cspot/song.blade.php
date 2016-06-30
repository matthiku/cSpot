
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a Song")

@section('songs', 'active')



@section('content')


    @include('layouts.flashing')


    @if (isset($song))
        {!! Form::model( $song, array(
            'route'  => array('cspot.songs.update', $song->id), 
            'method' => 'put', 
            'id'     => 'inputForm',
            'class'  => 'form-horizontal',
            'files'  => true,
            )) !!}
    @else
        {!! Form::open(array(
            'action' => 'Cspot\SongController@store', 
            'id' => 'inputForm',
            'files'  => true,
            )) !!}
    @endif


    <input type="hidden" name="currentPage" value="{{ $currentPage }}">

    <div class="row">

        <div class="col-md-6 col-lg-7 col-xl-8 md-center">
            @if (isset($song))
                <h2 class="hidden-xs-down">Song Details</h2>
                <small>Last updated: {{ isset($song->updated_at) ? $song->updated_at->formatLocalized('%a, %d %b %Y, %H:%M') : 'unknown' }}</small>
            @else
                <h2 class="hidden-xs-down">Add Song</h2>
            @endif
        </div>

        <div class="col-md-6 col-lg-5 col-xl-4">
            <div class="row">

                <div class="col-xs-4">
                    @if (isset($song))

                        <big class="submit-button" style="display: none;">{!! Form::submit('Save changes', ['class'=>'full-width']); !!}</big>
                        
                        @if (Auth::user()->isAdmin() && count($plansUsingThisSong)==0 )
                            </div>
                            <div class="col-xs-4">
                            <a class="btn btn-danger" type="button" href="{{ url('cspot/songs/'.$song->id) }}/delete">
                                <i class="fa fa-trash" > </i> Delete Song
                            </a>
                        @endif
                    @else
                        <big class="submit-button" style="display: none;">{!! Form::submit('Submit', ['class'=>'fully-width']); !!}</big>
                    @endif
                </div>

                <div class="col-xs-4 pull-xs-right">
                    <big><a href="
                        @if (session()->has('currentPage'))
                            {{ url('cspot/songs?page='.session('currentPage')) }}
                        @else
                            {{ url('cspot/songs?page='.$currentPage) }}
                        @endif
                        ">{!! Form::button('All Songs', ['class'=>'fully-width']); !!}</a></big>
                </div>

            </div>
        </div>

    </div>

    <hr>



    <div class="row">
        <div class="col-xl-6">

            
            <div class="row form-group">
               {!! Form::label('title', 'Song Title', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
               <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('title'); !!}</div>
               @if ( isset($song) )
                    &nbsp; <a class="btn btn-sm" type="button" target="new" 
                        href="https://olr.ccli.com/search/results?SearchTerm={{ $song->title.' '.$song->title_2.' '.$song->author }}">
                        <i class="fa fa-search" > </i> CCLI search 
                    </a>
                    &nbsp; <a class="btn btn-sm" type="button" target="new" 
                        href="https://www.hymnal.net/en/search/all/all/{{ $song->title.' '.$song->title_2 }}">
                        <i class="fa fa-search" > </i> hymnal.net search 
                    </a>
                @endif
            </div>


            <div class="row form-group">
                {!! Form::label('title_2', 'Subtitle', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('title_2'); !!}</div>
            </div>


            <div class="row form-group">
                {!! Form::label('author', 'Author', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('author'); !!}</div>
            </div>


            <div class="row form-group">
                {!! Form::label('book_ref', 'Book Ref.', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8">{!! Form::text('book_ref'); !!}</div>
            </div>


            <div class="row form-group">
                <div class="col-sm-4 col-md-3 col-lg-2 col-xl-4">                    
                    {!! Form::label('license', 'Select a license:'); !!}
                    <big>
                        <a tabindex="0" href="#"
                            data-container="body" data-toggle="tooltip"
                            title="The type of license can be retrieved from the CCLI database (see link above). &nbsp; PD = Public Domain.">
                            <i class="fa fa-question-circle"></i></a>
                    </big>
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8">
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
                {!! Form::label('hymnaldotnet_id', 'Hymnal.Net Link', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('hymnaldotnet_id'); !!}
                    @if ( isset($song->hymnaldotnet_id) )
                        <a class="btn btn-sm" type="button" target="new" 
                            href="{{ $song->hymnaldotnet_id }}">
                            <i class="fa fa-music" > </i> See song on Hymnal.Net
                        </a>
                    @endif
                </div>
            </div>


            <div class="row form-group">
                {!! Form::label('ccli_no', 'CCLI Song No', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8">{!! Form::number('ccli_no'); !!}
                    @if ( isset($song)  && $song->ccli_no > 10000 )
                        <a class="btn btn-sm" type="button" target="new" 
                            href="https://olr.ccli.com/search/results?SearchTerm={{ $song->ccli_no }}">
                            <i class="fa fa-search" > </i> CCLI look-up
                        </a>
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
                    &nbsp; <a class="btn btn-sm" type="button" target="new" 
                        href="https://www.youtube.com/results?search_query={{ $song->title }}">
                        <big class="fa fa-youtube" > </big> YouTube search
                    </a>
                    @if ( strlen($song->youtube_id)>0 )
                        &nbsp; <a class="btn btn-sm" type="button" target="new" 
                            href="https://www.youtube.com/watch?v={{ $song->youtube_id }}">
                            <big class="fa fa-youtube-play"></big> Play on Youtube</a>
                    @endif
                @endif
            </div>


            <div class="row form-group">
                {!! Form::label('link', 'Link(s)', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('link'); !!}</div>
            </div>


            <div class="row form-group">
                {!! Form::label('file', 'Attach an image (e.g. sheet music)', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <small>(Max. Size: <?php echo ini_get("upload_max_filesize"); ?>)</small>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">
                    {!! Form::file('file'); !!}
                    {!! Form::hidden('file_category_id','1') !!}
                </div>
            </div>
            @if ( isset($song) && $song->files)
                @foreach ($song->files as $file)
                    @include ('cspot.snippets.show_files')
                @endforeach
            @endif


        </div>



        <div class="col-xl-6">


            {!! Form::label('lyrics', 'Lyrics:'); !!}
            <big><a tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                    data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><pre class="tooltip-inner tooltip-wide"></pre></div>'
                    title="Song parts indicators must be enclosed with [], 
like [1] for verse 1 or [chorus] for a chorus. 

Blank lines force a new slide 
when the song is presented.">
                    <i class="fa fa-question-circle m-l-2"></i></a></big>
            <br/>
            {!! Form::textarea('lyrics'); !!}
            <button id="lyrics-copy-btn" class="pull-xs-right"><i class="fa fa-copy"></i>&nbsp;copy lyrics</button>

            {{-- reset size of textarea --}}
            <small><a href="#" id="reset-lyrics-textarea" onclick="$('#lyrics').attr('rows',6);$(this).hide()" style="display:none">resize textbox</a></small>

            {{-- Add ability to copy textarea content to the clipboard --}}
            <script>
                var copyTextareaBtn = document.querySelector('#lyrics-copy-btn');
                copyTextareaBtn.addEventListener('click', function(event) {
                  var copyTextarea = $('#lyrics');
                  copyTextarea.select();
                  try {
                    var successful = document.execCommand('copy');
                    var msg = successful ? 'successful' : 'unsuccessful';
                  } catch (err) {
                  }
                  event.preventDefault();
                });
                $("#lyrics").click(function() {
                    $("#lyrics").attr('rows', $("#lyrics").val().split('\n').length);
                    $('#reset-lyrics-textarea').show();
                    $('#reset-lyrics-textarea').position({my: 'right bottom', at: 'right top', of: '#lyrics'});
                });
            </script>
            <br>

            {!! Form::label('chords', 'Chords:'); !!}
            <big><a tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                    data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><pre class="tooltip-inner tooltip-wide"></pre></div>'
                    title='Song parts indicators must be on separate
lines and end with a colon (:).
Blank lines will be ignored.

Put instructions on separate lines and
enclose them with brackets,
like "(repeat chorus!)"'>
                    <i class="fa fa-question-circle m-l-2"></i></a></big>
            <br/>
            {!! Form::textarea('chords'); !!}
            <button id="chords-copy-btn" class="pull-xs-right"><i class="fa fa-copy"></i>&nbsp;copy chords</button>
            <br>

            {{-- reset size of textarea --}}
            <small><a href="#" id="reset-chords-textarea" onclick="$('#chords').attr('rows',6);$(this).hide()" style="display:none">resize textbox</a></small>

            <!-- Add ability to copy textarea content to the clipboard -->
            <script>
                var copyTextareaBtn = document.querySelector('#chords-copy-btn');
                copyTextareaBtn.addEventListener('click', function(event) {
                  var copyTextarea = $('#chords');
                  copyTextarea.select();
                  try {
                    var successful = document.execCommand('copy');
                    var msg = successful ? 'successful' : 'unsuccessful';
                  } catch (err) {
                  }
                  event.preventDefault();
                });
                $("#chords").click(function() {
                    $("#chords").attr('rows', $("#chords").val().split('\n').length);
                    $('#reset-chords-textarea').show();
                    $('#reset-chords-textarea').position({my: 'right bottom', at: 'right top', of: '#chords'});
                });
            </script>



            @if ( isset($song) )
                <h4>Song Usage History: <small>(used <strong>{{ count($plansUsingThisSong) }}</strong> times)</small></h4>
                @if ( count($plansUsingThisSong) )
                    <table class="table table-striped table-normal table-hover table-sm">
                        <thead class="thead-default"><tr>
                            <th>Date</th>
                            <th>Leader</th>
                        </tr></thead>
                        <tbody>
                        @foreach ($plansUsingThisSong as $plan)
                            <tr>
                                <td class="link" title="Click to edit this plan" 
                                    onclick="location.href='{{ url('cspot/plans/'.$plan->id) }}/edit'">
                                        {{ $plan->date->formatLocalized('%A, %d %B %Y') }}
                                </td>
                                <td class="link" title="click to see all plans by this user" 
                                    onclick="location.href='{{ url('cspot/plans/by_user/'.$plan->leader_id) }}/edit'">
                                        {{ $plan->leader->first_name }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            @endif



        </div>

    </div>



    {!! Form::close() !!}


    <script>
        // define field that should always get input focus
        document.forms.inputForm.title.focus()
        document.forms.inputForm.title.setAttribute('class', 'main-input');
    </script>

    
@stop
