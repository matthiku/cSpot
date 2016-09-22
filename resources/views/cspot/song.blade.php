
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
                <h2 class="hidden-xs-down">Add New
                    <span class="song-only">Song</span>
                    <span class="video-only" style="display: none;">Videoclip</span>
                </h2>
                Change to:
                <span class="btn-group btn-group-sm" role="group" aria-label="choose type of new song">
                    <button type="button" class="btn btn-secondary" onclick="showVideoForm()">Videoclip</button>
                    <button type="button" class="btn btn-secondary" onclick="showSlidesForm()">Text Slides</button>
                </span>
                <big>
                    <a tabindex="0" href="#"
                        data-container="body" data-toggle="tooltip" data-placement="bottom"
                        title="Select 'Videoclip' and the linked Youtube video can be shown on the presentation screen!
Select 'Text slides' in order to show Powerpoint-like slides using the text in the lyrics field!">
                        <i class="fa fa-info-circle"></i></a>
                </big>
            @endif
        </div>

        <div class="col-md-6 col-lg-5 col-xl-4">
            <div class="row">

                <div class="col-xs-4">
                    @if (isset($song))

                        <big class="submit-button" onclick="showSpinner()" style="display: none;">{!! Form::submit('Save changes', ['class'=>'full-width']); !!}</big>
                        
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

            
            <div class="row form-group m-b-0">
                {!! Form::label('title', 'Title', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('title'); !!}</div>
            </div>


            <div class="row form-group song-only">
                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4'>                    
                    {!! Form::label('title_2', 'Subtitle'); !!}
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('title_2'); !!}</div>
            </div>


            <div class="row form-group song-only">
                {!! Form::label('author', 'Author or Copyright statement', ['class' => 'col-sm-4 col-md-3 col-lg-2 col-xl-4']); !!}
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('author'); !!}</div>
            </div>


            <div class="row form-group song-only">
                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4'>                    
                    {!! Form::label('book_ref', 'Book Ref.'); !!}
                    <small>(e.g. Mission Praise='MP'</small>
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8">{!! Form::text('book_ref'); !!}</div>
            </div>


            <div class="row form-group song-only">
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


            <div class="row form-group song-only">
                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4'>
                    {!! Form::label('hymnaldotnet_id', 'Hymnal.Net ID or URL'); !!}
                    <big>
                        <a tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                            title="Search opens in new tab. Once song is found, copy the URL (the address) and paste it into this field.">
                            <i class="fa fa-question-circle"></i></a>
                    </big>
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">
                    {!! Form::text('hymnaldotnet_id'); !!}
                    <div class="center small">
                        @if ( isset($song) )
                            <a target="new" 
                               href="{{ env('HYMNAL.NET_SEARCH', 'https://www.hymnal.net/en/search/all/all/').$song->title.' '.$song->title_2 }}">
                               <i class="fa fa-search"></i> hymnal.net search <i class="fa fa-external-link"></i>
                            </a>
                            @if ( $song->hymnaldotnet_id )
                                <a class="m-l-2" target="new" 
                                    href="{{ $song->hymnaldotnet_id }}">
                                    <i class="fa fa-music" > </i> see song on hymnal.net <i class="fa fa-external-link"></i>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>


            <div class="row form-group song-only">
                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4'>
                    {!! Form::label('ccli_no', 'CCLI Song No or URL'); !!}
                    <big>
                        <a tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                            title="Search opens in new tab. Once song is found, copy the URL (the address) and paste it into this field.">
                            <i class="fa fa-question-circle"></i></a>
                    </big>
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">
                    {!! Form::text('ccli_no'); !!}
                    @if ( isset($song) )
                        <div class="center small">
                            <a target="new" 
                               href="{{ env('SONGSELECT_SEARCH', 'https://songselect.ccli.com/search/results?SearchText=').$song->title.' '.$song->title_2.' '.$song->author }}">
                               <i class="fa fa-search"></i><img src="/images/songselectlogo.png" width="15"> CCLI search <i class="fa fa-external-link"></i>
                            </a>
                            @if ($song->ccli_no > 10000 )
                                <a class="m-l-2" target="new" 
                                    href="{{ env('SONGSELECT_URL', 'https://songselect.ccli.com/Songs/').$song->ccli_no }}">
                                    <img src="/images/songselectlogo.png" width="15"> view on SongSelect <i class="fa fa-external-link"></i>
                                </a>
                            @endif
                        </div>
                    @endif
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


            <div class="row form-group song-or-video-only">
                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4'>
                    {!! Form::label('youtube_id', 'Youtube ID or URL'); !!}
                    <big>
                        <a tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                            title="Search opens in new tab. Once song is found, copy the URL (the address) and paste it into this field.">
                            <i class="fa fa-question-circle"></i></a>
                    </big>
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('youtube_id'); !!}</div>
                @if ( isset($song) )
                    <div class="center small">
                        <a target="new" 
                            href="{{ env('YOUTUBE_SEARCH', 'https://www.youtube.com/results?search_query=').$song->title }}">
                            <i class="fa fa-search"></i><i class="fa fa-youtube"></i> YouTube search <i class="fa fa-external-link"></i>
                        </a>
                        @if ( strlen($song->youtube_id)>0 )
                            <a class="m-l-2" target="new" 
                                href="{{ env('YOUTUBE_PLAY', 'https://www.youtube.com/watch?v=').$song->youtube_id }}">
                                <i class="fa fa-youtube-play"></i> play on Youtube <i class="fa fa-external-link"></i>
                            </a>
                        @endif
                    </div>
                @endif
            </div>


            <div class="row form-group song-or-video-only">
                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4'>
                    {!! Form::label('link', '(other) link(s)'); !!}
                    <big>
                        <a tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                            title="Paste the full URL (or link or address, usually starts with http://...) into this field. For more than one, just separate them by ';'">
                            <i class="fa fa-question-circle"></i></a>
                    </big>
                </div>
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


            <div class="form-group m-b-0 song-only">
                {!! Form::label('sequence', 'Sequence: '); !!}
                {!! Form::text('sequence'); !!}
            </div>


            <div id="accordion" role="tablist" aria-multiselectable="true">
              <div class="panel panel-default song-or-slides-only">
                <div class="panel-heading" role="tab" id="headingOne">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <span class="song-only">Lyrics</span><span class="slide-only" style="display: none;">Slides</span>:<a
                                tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                                data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><pre class="tooltip-inner tooltip-wide"></pre></div>'
                                title="(Click 'Lyrics' to open!)
Song parts indicators must be enclosed with [], 
like [1] for verse 1 or [chorus] for a chorus. 

Blank lines force a new slide 
when the song is presented.">
                        <i class="fa fa-question-circle m-l-2"></i></a>
                    </a>
                  </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse{{!isset($song) ? ' in' : ''}}" role="tabpanel" aria-labelledby="headingOne">
                    {!! Form::textarea('lyrics'); !!}
                    <button id="lyrics-copy-btn" class="pull-xs-right"><i class="fa fa-copy"></i>&nbsp;copy lyrics</button>

                    {{-- reset size of textarea --}}
                    <small><a href="#" id="reset-lyrics-textarea" onclick="resizeTextArea(this, 'lyrics')" style="display:none">resize textbox</a></small>
  
                </div>
              </div>



              <div class="panel panel-default song-only">
                <div class="panel-heading" role="tab" id="headingTwo">
                  <h4 class="panel-title">
                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Chords:<a 
                            tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                            data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><pre class="tooltip-inner tooltip-wide"></pre></div>'
                            title='(Click on title "Chords" to open!)
Song parts indicators must be on separate
lines and end with a colon (:).
Blank lines will be ignored.

Put instructions on separate lines and
enclose them in brackets,
like "(repeat chorus!)"'>
                        <i class="fa fa-question-circle m-l-2"></i></a>
                    </a>
                  </h4>
                </div>

                <div id="collapseTwo" class="panel-collapse collapse{{!isset($song) ? ' in' : ''}}" role="tabpanel" aria-labelledby="headingTwo">
                    {!! Form::textarea('chords'); !!}
                    <button id="chords-copy-btn" class="pull-xs-right"><i class="fa fa-copy"></i>&nbsp;copy chords</button>
                    <br>

                    {{-- reset size of textarea --}}
                    <small><a href="#" id="reset-chords-textarea" onclick="resizeTextArea(this, 'chords')" style="display:none">resize textbox</a></small>
                </div>
              </div>



                @if ( isset($song) )
                  <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingThree">
                      <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Song Usage History: <small>(used <strong>{{ count($plansUsingThisSong) }}</strong> times)</small>
                        </a>
                      </h4>
                    </div>


                    <div id="collapseThree" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree">
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


    {!! Form::close() !!}


    <script>

        // define field that should always get input focus
        document.forms.inputForm.title.focus()
        document.forms.inputForm.title.setAttribute('class', 'main-input');


        // Add ability to copy textarea content to the clipboard
        var copyTextareaBtn = document.querySelector('#chords-copy-btn');
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
            $('#reset-chords-textarea').show();
            $('#reset-chords-textarea').position({my: 'right bottom', at: 'right top', of: 'textarea[name="chords"]'});
        });


        {{-- Add ability to copy textarea content to the clipboard --}}
        var copyTextareaBtn = document.querySelector('#lyrics-copy-btn');
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
        $("textarea[name='lyrics']").click(function() {
            {{-- get number of lines of lyrics and set size of textarea accordingly --}}
            $("textarea[name='lyrics']").attr( 'rows', Math.max($("textarea[name='lyrics']").val().split('\n').length, 4) );
            $('#reset-lyrics-textarea').show();
            $('#reset-lyrics-textarea').position({my: 'right bottom', at: 'right top', of: 'textarea[name="lyrics"]'});
        });

        function resizeTextArea(that, name) {
            $('textarea[name='+name+']').attr('rows',6);
            $(that).hide();
        }

        function showSlidesForm() {
            $('.song-only').hide();
            $('.song-or-video-only').hide();
            $('.song-or-slides-only').show();
            $('.slide-only').show();
            $("input[name='title_2']").val('slide');
        }

        function showVideoForm() {
            $('.song-only').hide();
            $('.song-or-slides-only').hide();
            $('.song-or-video-only').show();
            $('.video-only').show();
            $("input[name='title_2']").val('video');
        }

    </script>

    
@stop
