
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a Song")

@section('plans', 'active')



@section('content')


    @include('layouts.flashing')


    @if (isset($song))
        
        {!! Form::model( $song, array(
            'route'  => array('songs.update', $song->id), 
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




    {{-- title row and navigation 
    --}}
    <div class="row mx-0">

        {{-- PAGE TITLE  
        --}}
        <div class="col-md-6">

            @if (isset($song))

                <h2 class="hidden-xs-down text-success lora">Song/Item Details</h2>
    
                @if (isset($song->updated_at))
                    <small title="{{ $song->updated_at->formatLocalized('%a, %d %b %Y, %H:%M') }}">Last 
                        updated:<span class="ml-1 px-1 rounded {{ (\Carbon\Carbon::now()->diffinSeconds($song->updated_at) < 120) ? 'text-white bg-warning' : 'bg-muted' }}">{{ 
                                \Carbon\Carbon::now()->diffinSeconds($song->updated_at) < 120
                                    ? 'just now'
                                    : \Carbon\Carbon::now()->diffForHumans( $song->updated_at, true ).' ago' }}</span>
                    </small>
                @endif

            @else            

                <h2 class="hidden-xs-down text-success lora">Add New
                    <span class="song-only">Song</span>
                    <span class="video-only" style="display: none;">Videoclip</span>
                    <span class="slides-only" style="display: none;">Set of Slides</span>
                    <span class="training-show" style="display: none;">Training Video</span>
                </h2>

            @endif
        </div>


        {{-- Song/Item Navigation and Submit/Save Buttons 
        --}}
        <div class="col-md-6">

            @if ( (isset($song) && $song->title_2=='training') || (! isset($song) && Request::has('type') && Request::input('type')=='training'  ) )
                <a class="float-right btn btn-outline-success" href="{{ route('trainingVideos') }}">
                    <span class="hidden-lg-down">Back to </span><span class="hidden-sm-down">List of </span>Training Videos
                </a>
            @else
                <a class="float-right btn btn-outline-success song-only" 
                        href="{{ url('cspot/songs?page=') . ( session()->has('currentPage') ? session('currentPage') : $currentPage ) }}">
                    <span class="hidden-lg-down">All </span>Songs
                </a>
                <a class="float-right btn btn-outline-warning mr-1 slides-only" style="display: none;"
                        href="{{ url('cspot/songs?filterby=title_2&filtervalue=slides') }}">
                    <span class="hidden-lg-down">All </span>Slideshows
                </a>
                <a class="float-right btn btn-outline-danger mr-1 video-only" style="display: none;"
                        href="{{ url('cspot/songs?filterby=title_2&filtervalue=video') }}">
                    <span class="hidden-lg-down">All </span>Videoclips
                </a>
            @endif
        
            {{-- SAVE or SUBMIT button --}}
            @if (isset($song))
                <big class="mr-1">
                    {!! Form::submit('Save changes', ['class' => 'btn btn-success submit-button disabled']); !!}
                </big>
            @else            
                <span class="mr-1">{!! Form::submit('Submit', ['class' => 'btn btn-outline-success submit-button disabled']); !!}</span>
            @endif

            {{-- DELETE button --}}
            @if ( isset($song) && Auth::user()->isAdmin() && count($plansUsingThisSong)==0 )
                <a href="{{ url('cspot/songs/'.$song->id) }}/delete">
                    <i class="fa fa-trash text-danger" > </i> delete this item
                </a><br><small>(as it's not used anywhere)</small>
            @endif
        </div>

    </div>



    <hr>


    {{-- song details  
    --}}
    <div class="row mx-0">

        {{-- left part of song details 
        --}}
        <div class="col-xl-6">

            <div class="slides-only mb-2" style="display: none;">
                <p><strong>Slides</strong> (or sets of slides) are like songs, but with your own, free text.</p>
                <p>Insert empty lines between text to force a new slide. But in order to be able to navigate beween your slides, use slide 
                    indicators like <i>[1], [2]</i> on a single line between your slides.</p>
                <p>You can also use some <a target="new" href="https://en.wikipedia.org/wiki/Cascading_Style_Sheets">CSS</a> rules 
                    (like <i>&lt;color: red;&gt;</i> or <i>&lt;font-weight: bolder&gt;</i>, even combined) to format your text
                    by putting these rules at the beginning of your text line.</p>
                <hr>
            </div>
            <div class="video-only mb-2" style="display: none;">
                <p><strong>Videoclips</strong> are YouTube videos embedded into the presentation in a single slide.</p>
                <p>Search for your clip on YouTube and copy the YouTube-URL (or <i>address</i> 
                    or just the ID) into the appropriate field.</p>
                <p>If you want to show only parts of the video click on the "Share" link underneath the video 
                    on YouTube and check the "Start at:" setting underneath the provided link. 
                    This will add a <strong>start time</strong> ("<i>&t=NmNs</i>") to the URL.</p>
                <p>You can enter the title of your clip and then click on "<i class="text-primary">search YouTube</i>" 
                    to directly start searching for the desired videoclip.</p>
                <hr>
            </div>

            <div class="training-show mb-2" style="display: none;">
                <p><strong>Training Videos</strong> are links to YouTube videos and will be presented in a particular way within c-SPOT.</p>
                <p>For the <strong>description</strong>, you can use basic HTML elements like ol, ul etc.</p>
                <p>The <strong>Book Ref.</strong> field is used to organize and sort the list of training videos.
                    The syntax you should use is <em>XXnnb</em> where XX are 2 letters, nn are 2 numbers and b is optional for another indicator.</p>
                <p>Currently, 'tr' is being used to show the basic c-SPOT training videos.</p>
                <hr>
            </div>


            {{-- song-title 
            --}}
            <div class="row form-group mb-0">
                {!! Form::label('title', 'Title', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4 text-sm-right']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('title'); !!}</div>
            </div>


            {{-- song sub-title 
            --}}
            <div class="row form-group song-only">
                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4 text-sm-right'>                    
                    {!! Form::label('title_2', 'Subtitle'); !!}
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('title_2'); !!}</div>
            </div>


            {{-- song author 
            --}}
            <div class="row form-group song-only training-show">
                {!! Form::label('author', 'Author/Copyright', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4 text-sm-right']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('author'); !!}</div>
            </div>


            {{-- song book ref 
            --}}
            <div class="row form-group song-only training-show bg-muted">
                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4 text-sm-right'>                    
                    {!! Form::label('book_ref', 'Book Ref.'); !!}                    
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8">
                    {!! Form::text('book_ref'); !!}
                    <small class="song-only">(e.g. Mission Praise='MPnnn')</small>
                </div>
            </div>


            {{-- song license  
            --}}
            <div class="row form-group song-only">
                <div class="col-sm-4 col-md-3 col-lg-2 col-xl-4 text-sm-right">                    
                    {!! Form::label('license', 'License type:'); !!}
                    <big>
                        <a tabindex="0" href="#"
                            data-container="body" data-toggle="tooltip"
                            title="The type of license can be retrieved from the CCLI database - see link below. &nbsp; PD = Public Domain.">
                            <i class="fa fa-info-circle"></i></a>
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


            {{-- hymnal.net link etc 
            --}}
            <div class="row form-group song-only bg-muted">
                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4 text-sm-right'>
                    {!! Form::label('hymnaldotnet_id', 'Hymnal.Net URL (link)'); !!}
                    <big>
                        <a tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                            title="Search opens in new tab. Once song is found, copy the URL (the address) and paste it into this field.">
                            <i class="fa fa-info-circle"></i></a>
                    </big>
                </div>

                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width link-input-field" id="hdn-link-input-field">
                    {!! Form::text('hymnaldotnet_id'); !!}

                    @if ( isset($song) )
                        <div class="small">
                            <a target="new"  onclick="$('#hdn-link-input-field').hide();$('#hdn-drop-target').show()"
                               href="{{ env('HYMNAL.NET_SEARCH', 'https://www.hymnal.net/en/search/all/all/').$song->title.' '.$song->title_2 }}">
                               <i class="fa fa-search"></i> search hymnal.net <i class="fa fa-external-link"></i>
                            </a>
                            @if ( $song->hymnaldotnet_id )
                                <a class="right-align-input" target="new" 
                                    href="{{ $song->hymnaldotnet_id }}">
                                    <i class="fa fa-music" > </i> show <i class="fa fa-external-link"></i>
                                </a>
                            @endif
                        </div>
                    @endif

                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8" id="hdn-drop-target" style="display: none;">
                    <textarea class="bg-warning drop-target">drop Hymnal.Net link address here ....</textarea>
                    <a href="#" onclick="$('#hdn-link-input-field').show();$('#hdn-drop-target').hide()">Cancel</a>
                </div>
            </div>



            {{-- CCLI data 
            --}}
            <div class="row form-group song-only">
                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4 text-sm-right'>
                    <label for="ccli_no">CCLI Song N<sup>o</sup></label>
                    <big>
                        <a tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                            title="Search opens in new tab. Once song is found, copy the URL (the address) and paste it into this field.">
                            <i class="fa fa-info-circle"></i></a>
                    </big>
                </div>

                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width link-input-field" id="ccli-link-input-field">
                    {!! Form::text('ccli_no'); !!}

                    @if ( isset($song) )
                        <div class=" small">
                            <a target="new"  onclick="$('#ccli-link-input-field').hide();$('#ccli-drop-target').show()"
                               href="{{ env('SONGSELECT_SEARCH', 'https://songselect.ccli.com/search/results?SearchText=').$song->title.' '.$song->title_2.' '.$song->author }}">
                               <i class="fa fa-search"></i><img src="{{ url('/') }}/images/songselectlogo.png" width="15"> search CCLI <i class="fa fa-external-link"></i>
                            </a>
                            @if ( $song->ccli_no > 1000 && 'MP'.$song->ccli_no!=$song->book_ref )
                                <a class="right-align-input" target="new" 
                                    href="{{ env('SONGSELECT_URL', 'https://songselect.ccli.com/Songs/').$song->ccli_no }}">
                                    <img src="{{ url('/') }}/images/songselectlogo.png" width="15"> show <i class="fa fa-external-link"></i>
                                </a>
                            @endif
                        </div>
                    @endif

                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8" id="ccli-drop-target" style="display: none;">
                    <textarea class="bg-warning drop-target">drop CCLI link address here ....</textarea>
                    <a href="#" onclick="$('#ccli-link-input-field').show();$('#ccli-drop-target').hide()">Cancel</a>
                </div>

                <script>
                    // when the user enters a CCLI No, we can safely assume that the license type is 'CCLI'
                    if ($("input[name='ccli_no']").length) {
                        $("input[name='ccli_no']").on('change', function() {
                            // now select the CCLI radio button
                            $("input[name='license']")[1].click()
                        })
                    }
                </script>
            </div>



            {{-- YouTube data 
            --}}
            <div class="row form-group song-or-video-only training-show mt-1 bg-muted">

                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4 text-sm-right'>
                    {!! Form::label('youtube_id', 'Youtube ID or URL'); !!}
                    <big>
                        <a tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                            title="Search opens in new tab. Once song is found, copy the URL (the address) and paste it into this field.">
                            <i class="fa fa-info-circle"></i></a>
                    </big>
                </div>

                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width link-input-field" id="yt-link-input-field">
                    {!! Form::text('youtube_id'); !!}

                    <div class="small">
                        <a target="new" onclick="$('#yt-link-input-field').hide();$('#yt-drop-target').show()" id="youtube-search-link"
                            href="{{ env('YOUTUBE_SEARCH', 'https://www.youtube.com/results?search_query='). (isset($song) ? $song->title : '') }}">
                            <i class="fa fa-search"></i>&nbsp;<i class="fa fa-youtube"></i> search YouTube <i class="fa fa-external-link"></i>
                        </a>
                        @if ( isset($song) )
                            @if ( strlen($song->youtube_id)>0 )
                                <a class="right-align-input" target="new"
                                    href="{{ env('YOUTUBE_PLAY', 'https://www.youtube.com/watch?v=').$song->youtube_id }}">
                                    <i class="fa fa-youtube-play"></i> play <i class="fa fa-external-link"></i>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8" id="yt-drop-target" style="display: none;">
                    <textarea class="bg-warning drop-target">drop YouTube link address here ....</textarea>
                    <a href="#" onclick="$('#yt-link-input-field').show();$('#yt-drop-target').hide()">Cancel</a>
                </div>
            </div>


            {{-- other links etc 
            --}}
            <div class="row form-group song-or-video-only training-show">
                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4 text-sm-right'>
                    {!! Form::label('link', 'Further link(s)'); !!}
                    <big>
                        <a tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                            title="Paste the full URL (or link or address, usually starts with http://...) into this field. For more than one, just separate them by ';'">
                            <i class="fa fa-info-circle"></i></a>
                    </big>
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('link'); !!}</div>
            </div>


            
            {{-- attach file(s) to this song 
            --}}
            <div class="row form-group mt-1 bg-muted">
                <div class="col-sm-4 col-md-3 col-lg-2 col-xl-4 text-sm-right l-h-1">
                    {!! Form::label('file', 'Attach Sheet Music:'); !!}
                    <br>
                    <small>(Maximum file size: <?php echo ini_get("upload_max_filesize"); ?>)</small>
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">
                    {!! Form::file('file'); !!}
                    <!-- file category will be '1' (songs) -->
                    {!! Form::hidden('file_category_id','1') !!}
                    <br><small>(<strong>Note: </strong>File name will be book ref. + title)</small>
                </div>
            </div>
            @if ( isset($song) && $song->files)
                @foreach ($song->files as $file)
                    @include ('cspot.snippets.show_files')
                @endforeach
            @endif


            {{-- SAVE or SUBMIT button 
            --}}
            <div class="hidden-lg-down">    
                @if (isset($song))
                    <hr class="hr-big">
                    <big class="mr-1">
                        {!! Form::submit('Save changes', ['class' => 'btn btn-success submit-button disabled']); !!}
                    </big>
                @else            
                    <span class="mr-1">{!! Form::submit('Submit', ['class' => 'btn btn-outline-success submit-button disabled']); !!}</span>
                @endif
            </div>


        </div>



        {{-- Lyrics, chords, OnSong and Usage History 
        --}}
        <div class="col-xl-6">


            {{-- show the sequence here only if there is no OnSong data --}}
            @if ( isset($song)  &&  ! $song->onsongs->count() )
                <div class="old-style-song-sequence-input-field form-group mb-0 lh-1 song-only" 
                     title="(The sequence determines how the lyrics are presented)">
                    {!! Form::label('sequence', 'Sequence: ', ['class' => 'baseline']); !!}
                    {!! Form::text('sequence'); !!}
                    <small class="hidden-sm-down">
                        This determines how the lyrics are presented.<br>
                        The sequence must only contain codes for songparts that exist in the "Lyrics" section!
                    </small>
                </div>
            @endif


            <div id="accordion" role="tablist" aria-multiselectable="true">
              <div class="panel panel-default song-or-slides-only">
                <div class="panel-heading" role="tab" id="lyrics-panel">
                    <h4 class="panel-title">

                        <a href="#collapseLyrics" data-toggle="collapse" data-parent="#accordion"
                            aria-expanded="true" aria-controls="collapseLyrics">
                            <span class="song-only">Lyrics</span><span 
                                class="slides-only" style="display: none;">Slides</span><span 
                                class="training-show" style="display: none;">Description</span>:
                        </a>

                        @if ( !isset($song) || (isset($song) && $song->title_2!='slides') )
                            <a  tabindex="0" href="#" data-container="body" data-toggle="tooltip" data-placement="bottom"
                                data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><pre class="tooltip-inner tooltip-wide"></pre></div>'
                                title="{{ "Song parts indicators must be enclosed with [],\nlike [1] for verse 1 or [chorus] for a chorus.\n\nBlank lines force a new slide\nwhen the song is presented.\n\nDon't use that if you plan to use the OnSong\nformat as lyrics will be taken from the OnSong data!" }}"
                                <i class="fa fa-info-circle ml-2"></i>
                            </a>
                        @endif
                    </h4>
                </div>
                @if ( !isset($song)  ||  (isset($song) && $song->title_2!='video')   )
                    <div id="collapseLyrics" class="panel-collapse collapse{{ ( !isset($song) || (isset($song) && $song->title_2=='slides') ) ? ' in' : '' }}" 
                            role="tabpanel" aria-labelledby="lyrics-panel">

                        <textarea name="lyrics" rows=4 onkeyup="calculateTextAreaHeight(this);">{{ isset($song) ? $song->lyrics : '' }}</textarea>

                        <button id="lyrics-copy-btn" class="float-right"><i class="fa fa-copy"></i>&nbsp;copy text</button>

                        {{-- zoom size of textarea --}}
                        <small id="zoom-lyrics-textarea" style="display: none;">textbox size:
                            <span class="btn btn-sm btn-outline-info narrow baseline" onclick="resizeTextArea('plus', 'lyrics')"> 
                                <small>&#10133;</small>
                            </span>
                            <span class="btn btn-sm btn-outline-info narrow baseline" onclick="resizeTextArea('minus', 'lyrics')"> 
                                <small>&#10134;</small>
                            </span>
                        </small>
      
                    </div>
                @endif
              </div>




              <div class="panel panel-default song-only">
                <div class="panel-heading" role="tab" id="chords-panel">
                  <h4 class="panel-title">
                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseChords" aria-expanded="false" aria-controls="collapseChords">
                        Chords:</a>
                        <a 
                            tabindex="0" href="#" data-container="body" data-toggle="tooltip" data-placement="bottom"
                            data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><pre class="tooltip-inner tooltip-wide"></pre></div>'
                            title='{{ isset($song) ? "(Click on title 'Chords' to open!)\n" : '' }}{{ 
                                    "Song parts indicators must be on separate\nlines and end with a colon (:).\nBlank lines will be ignored.\n\nPut instructions on separate lines and\nenclose them in brackets,\nlike '(repeat chorus!)'\n\nDon't fill that in if you plan to use the OnSong format!" }}'>
                        <i class="fa fa-info-circle ml-2"></i>
                    </a>
                  </h4>
                </div>

                <div id="collapseChords" class="panel-collapse collapse{{!isset($song) ? ' in' : ''}}" role="tabpanel" aria-labelledby="chords-panel">

                    <textarea name="chords" id="chords-textarea" rows=4 onkeyup="calculateTextAreaHeight(this);">{{ isset($song) ? $song->chords : '' }}</textarea>
                    
                    <button id="chords-copy-btn" class="float-right"><i class="fa fa-copy"></i>&nbsp;copy chords</button>

                    <span class="btn btn-sm btn-outline-primary float-right" 
                        onclick="$('#show-chords-as-onsong').text(joinLyricsAndChordsToOnSong($('#chords-textarea').val()));$(this).hide();">
                        show OnSong-encoded copy</span>
                    <pre id="show-chords-as-onsong"></pre>


                    {{-- zoom size of textarea --}}
                    <small id="zoom-chords-textarea" style="display: none;">textbox size:
                        <span class="btn btn-sm btn-outline-info narrow baseline" onclick="resizeTextArea('plus', 'chords')"> 
                            <small>&#10133;</small>
                        </span>
                        <span class="btn btn-sm btn-outline-info narrow baseline" onclick="resizeTextArea('minus', 'chords')"> 
                            <small>&#10134;</small>
                        </span>
                    </small>
                </div>
              </div>


                <div class="panel panel-default song-only">
                    <div class="panel-heading" role="tab" id="onsong-panel">
                      <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOnSong" aria-expanded="false" aria-controls="collapseOnSong">
                            OnSong:</a>
                            <a 
                                tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                                data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><pre class="tooltip-inner tooltip-wide"></pre></div>'
                                title='{{ isset($song) ? "(Click on title 'OnSong' to open!)\n\n" : '' }}{{
                                         "Lyrics and Chords combined, in OnSong-compatible format." }}'>
                            <i class="fa fa-info-circle ml-2"></i>
                        </a>
                      </h4>
                    </div>


                    <div id="collapseOnSong" class="panel-collapse collapse{{!isset($song) ? ' in' : ''}}" role="tabpanel" aria-labelledby="onsong-panel">
                        
                        @if ( isset($song) )
                            @include ('cspot.snippets.onsong')
                        @else
                            (will be available once you saved this new song)
                        @endif

                    </div>
                </div>



                @if ( isset($song) )
                  <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="history-panel">
                      <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseHistory" aria-expanded="false" aria-controls="collapseHistory">
                            Usage History: <small>(used <strong>{{ count($plansUsingThisSong) }}</strong> times)</small>
                        </a>
                      </h4>
                    </div>


                    <div id="collapseHistory" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="history-panel">
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
                                            onclick="location.href='{{ url('cspot/plans')}}?show=all&filterby=user&filtervalue={{$plan->leader_id }}'">
                                                {{ $plan->leader ? $plan->leader->first_name : $plan->leader_id }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                  </div>
                @endif
            </div>{{-- accordion --}}

        </div>{{-- col-xl-6 --}}

    </div>{{-- row --}}


    {{-- SAVE or SUBMIT button --}}
    <div class="hidden-xl-up">    
        @if (isset($song))
            <hr class="hr-big">
            <big class="mr-1">
                {!! Form::submit('Save changes', ['class' => 'btn btn-success submit-button disabled']); !!}
            </big>
        @else            
            <span class="mr-1">{!! Form::submit('Submit', ['class' => 'btn btn-outline-success submit-button disabled']); !!}</span>
        @endif
    </div>


    {!! Form::close() !!}


    <script>

        // provide item data on the client side
        @if (isset($song))
            cSpot.item = [];
            cSpot.item.song = {!! json_encode($song, JSON_HEX_APOS | JSON_HEX_QUOT ) !!};
        @endif

        // define field that should always get input focus
        document.forms.inputForm.title.focus()
        document.forms.inputForm.title.setAttribute('class', 'main-input');


        // correct position of textarea sizing buttons
        $(document).ready(function() {
            positionZoomButtons('lyrics');
            positionZoomButtons('chords');
            $('#collapseLyrics').on('shown.bs.collapse', function () {
                positionZoomButtons('lyrics');
            })
            $('#collapseChords').on('shown.bs.collapse', function () {
                positionZoomButtons('chords');
            })
        })

        // Add ability to copy textarea content to the clipboard
        var copyTextareaBtn = document.querySelector('#chords-copy-btn');
        if (copyTextareaBtn)
            copyTextareaBtn.addEventListener('click', function(event) {
                var copyTextarea = $('textarea[name="chords"]');
                copyTextarea.select();
                try {
                    var successful = document.execCommand('copy');
                    var msg = successful ? 'successful' : 'unsuccessful';
                } catch (err) {
                }
                event.preventDefault();
            });
        $("textarea[name='chords']").click(function() {
            {{-- get number of lines of text and set size of textarea accordingly --}}
            $("textarea[name='chords']").attr('rows', Math.max($("textarea[name='chords']").val().split('\n').length, 4) );
            positionZoomButtons('chords');
        });


        {{-- Add ability to copy textarea content to the clipboard --}}
        var copyTextareaBtn = document.querySelector('#lyrics-copy-btn');
        if (copyTextareaBtn)
            copyTextareaBtn.addEventListener('click', function(event) {
                var copyTextarea = $('textarea[name="lyrics"]');
                copyTextarea.select();
                try {
                    var successful = document.execCommand('copy');
                    var msg = successful ? 'successful' : 'unsuccessful';
                } catch (err) {
                }
                event.preventDefault();
            });
        // on click, resize textarea according to the size of its content (amount of lines)
        $("textarea[name='lyrics']").click(function() {
            {{-- get number of lines of lyrics and set size of textarea accordingly --}}
            $("textarea[name='lyrics']").attr( 'rows', Math.max($("textarea[name='lyrics']").val().split('\n').length, 4) );
            positionZoomButtons('lyrics');
        });


        function showSlidesForm(what) {
            $('.song-only').hide();
            $('.video-only').hide();
            $('.song-or-video-only').hide();
            $('.slides-only').show();
            $('.song-or-slides-only').show();
            $('#all-items-button').text('Slides');
            if (what) 
                $("input[name='title_2']").val(what);
        }

        function showVideoForm(what) {
            $('.song-only').hide();
            $('.slides-only').hide();
            $('.song-or-slides-only').hide();
            $('.video-only').show();
            $('.song-or-video-only').show();
            $('#all-items-button').text('Videoclips');
            if (what) 
                $("input[name='title_2']").val(what);
        }


        function showTrainingForm(what) {
            $('.song-only').hide();
            $('.video-only').hide();
            $('.song-or-video-only').hide();
            $('.song-or-slides-only').show();
            $('.training-show').show();
            $('#all-items-button').text('Training Videos');
            if (what) 
                $("input[name='title_2']").val(what);
        }


        /* Provide drop-targets for URL strings like YouTube links, CCLI numbers, hymnal.net URLs
        */
        $(".drop-target")
            .bind("dragover", false)
            .bind("dragenter", false)
            .bind("drop", function(e) {
                this.value = e.originalEvent.dataTransfer.getData("text") ||
                    e.originalEvent.dataTransfer.getData("text/plain");

                // hide drop target and show input field again
                $('.drop-target').hide()
                $('.link-input-field').show();
                // find the corresponding input field and fill it with the dropped link string
                $( $(e.target.parentNode.parentNode).children('.link-input-field').children('input')[0] ).val(this.value);
                // submit the form to save the new data
                $('#inputForm').submit();

            return false;
        });

        /* position external links underneath the input elements
        */
        function extLinksPosition() {
            // position each element of this class according to its correspondnig input element
            $('.right-align-input').show(); // (as we hid those elements at first)
            $('.right-align-input').each( function() {
                $(this).position({my: 'right', at: 'right-5', of: $(this).parent().parent().children('input')[0]});
            });            
        }
        // do so after first document laod
        $(document).ready( function() {
            extLinksPosition();
        });
        // repeat after each resizing of the browser window
        $(window).on('resize', function(){ 
            extLinksPosition();
        });


        /* change form content depending on type of song
        */
        @if (isset($song))
            @if ($song->title_2=='video')
                showVideoForm();
            @elseif ($song->title_2=='slides' )
                showSlidesForm();
            @elseif ($song->title_2=='training' )
                showTrainingForm();
            @endif
        @endif

        /* For new items, change form according to querystring
        */
        @if (Request::has('type'))
            @if (Request::input('type')=='video')
                showVideoForm('video');
            @elseif (Request::input('type')=='slides')
                showSlidesForm('slides');
            @elseif (Request::input('type')=='training')
                showTrainingForm('training');
            @endif
        @endif

        /* Set YouTube search according to content of title input field
        */
        @if (! isset($song))
            var oldYtLinkHref = $('#youtube-search-link').attr('href');
            $('#title').on('blur', 
            function() {
                var title = $('#title').val();
                $('#youtube-search-link').attr('href', oldYtLinkHref+title)
            });
        @endif

    </script>

    
@stop
