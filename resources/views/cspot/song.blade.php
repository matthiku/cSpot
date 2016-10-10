
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a Song")

@section('songs', 'active')



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

    <div class="row">

        <div class="col-md-6 col-lg-7 col-xl-8 md-center">
            @if (isset($song))
                <h2 class="hidden-xs-down">Song/Item Details</h2>
                <small>Last updated: {{ isset($song->updated_at) ? $song->updated_at->formatLocalized('%a, %d %b %Y, %H:%M') : 'unknown' }}</small>
            @else
                <h2 class="hidden-xs-down">Add New
                    <span class="song-only">Song</span>
                    <span class="video-only" style="display: none;">Videoclip</span>
                </h2>
                Change to:
                <span class="btn-group btn-group-sm" role="group" aria-label="choose type of new song">
                    <button type="button" class="btn btn-secondary" onclick="showVideoForm('video')">Videoclip</button>
                    <button type="button" class="btn btn-secondary" onclick="showSlidesForm('slides')">Text Slides</button>
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


        {{-- Song/Item List Navigation Buttons 
        --}}
        <div class="col-md-6 col-lg-5 col-xl-4">

            {{-- SAVE or SUBMIT button --}}
            @if (isset($song))
                <big class="submit-button" onclick="showSpinner()" style="display: none;">{!! Form::submit('Save changes'); !!}</big>
                    @if (Auth::user()->isAdmin() && count($plansUsingThisSong)==0 )
                        </div>
                        <div class="small col-xs-4">
                        Item is not used in any plan, so you can<br> 
                        <a href="{{ url('cspot/songs/'.$song->id) }}/delete">
                            <i class="fa fa-trash text-danger" > </i> delete this song
                        </a>
                    @endif
                </big>
            @else
                <big class="submit-button" style="display: none;">{!! Form::submit('Submit'); !!}
                </big>
            @endif

            <a class="pull-xs-right btn btn-outline-success" href="{{ url('cspot/songs?page=') .
                    ( session()->has('currentPage') ? session('currentPage') : $currentPage ) }}">
                All Songs
            </a>
            <a class="pull-xs-right btn btn-outline-warning m-r-1"
                    href="{{ url('cspot/songs?filterby=title_2&filtervalue=slides') }}">
                All Slideshows
            </a>
            <a class="pull-xs-right btn btn-outline-danger m-r-1"
                    href="{{ url('cspot/songs?filterby=title_2&filtervalue=video') }}">
                All Videoclips
            </a>
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
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8">
                    {!! Form::text('book_ref'); !!}
                    <small>(e.g. Mission Praise='MPnnn')</small>
                </div>
            </div>


            <div class="row form-group song-only">
                <div class="col-sm-4 col-md-3 col-lg-2 col-xl-4">                    
                    {!! Form::label('license', 'Select a license:'); !!}
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


            <div class="row form-group song-only">
                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4'>
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



            <div class="row form-group song-only">
                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4'>
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



            <div class="row form-group song-or-video-only m-t-1">

                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4'>
                    {!! Form::label('youtube_id', 'Youtube ID or URL'); !!}
                    <big>
                        <a tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                            title="Search opens in new tab. Once song is found, copy the URL (the address) and paste it into this field.">
                            <i class="fa fa-info-circle"></i></a>
                    </big>
                </div>

                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width link-input-field" id="yt-link-input-field">
                    {!! Form::text('youtube_id'); !!}

                    @if ( isset($song) )
                        <div class="small">
                            <a target="new" onclick="$('#yt-link-input-field').hide();$('#yt-drop-target').show()"
                                href="{{ env('YOUTUBE_SEARCH', 'https://www.youtube.com/results?search_query=').$song->title }}">
                                <i class="fa fa-search"></i><i class="fa fa-youtube"></i> search YouTube <i class="fa fa-external-link"></i>
                            </a>
                            @if ( strlen($song->youtube_id)>0 )
                                <a class="right-align-input" target="new" 
                                    href="{{ env('YOUTUBE_PLAY', 'https://www.youtube.com/watch?v=').$song->youtube_id }}">
                                    <i class="fa fa-youtube-play"></i> play <i class="fa fa-external-link"></i>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8" id="yt-drop-target" style="display: none;">
                    <textarea class="bg-warning drop-target">drop YouTube link address here ....</textarea>
                    <a href="#" onclick="$('#yt-link-input-field').show();$('#yt-drop-target').hide()">Cancel</a>
                </div>
            </div>


            <div class="row form-group song-or-video-only">
                <div class='col-sm-4 col-md-3 col-lg-2 col-xl-4'>
                    {!! Form::label('link', '(other) link(s)'); !!}
                    <big>
                        <a tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                            title="Paste the full URL (or link or address, usually starts with http://...) into this field. For more than one, just separate them by ';'">
                            <i class="fa fa-info-circle"></i></a>
                    </big>
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">{!! Form::text('link'); !!}</div>
            </div>


            <div class="row form-group m-t-1">
                <div class="col-sm-4 col-md-3 col-lg-2 col-xl-4">
                    {!! Form::label('file', 'Attach an image'); !!}
                    <small>(Max. Size: <?php echo ini_get("upload_max_filesize"); ?>)</small>
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10 col-xl-8 full-width">
                    {!! Form::file('file'); !!}
                    <!-- file category will be '1' (songs) -->
                    {!! Form::hidden('file_category_id','1') !!}
                    <br>(Image name will be book ref. + title)
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

                        <span class="song-only">Lyrics</span><span class="slide-only" style="display: none;">Slides</span>:

                        @if ( !isset($song) || (isset($song) && $song->title_2!='slides') )
                            <a
                                tabindex="0" href="#" data-container="body" data-toggle="tooltip"
                                data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><pre class="tooltip-inner tooltip-wide"></pre></div>'
                                title="(Click 'Lyrics' to open!)
Song parts indicators must be enclosed with [], 
like [1] for verse 1 or [chorus] for a chorus. 

Blank lines force a new slide 
when the song is presented.">
                                <i class="fa fa-info-circle m-l-2"></i></a>
                            </a>
                        @endif
                  </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse{{ ( !isset($song) || (isset($song) && $song->title_2=='slides') ) ? ' in' : '' }}" role="tabpanel" aria-labelledby="headingOne">
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
                        <i class="fa fa-info-circle m-l-2"></i></a>
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
                            Usage History: <small>(used <strong>{{ count($plansUsingThisSong) }}</strong> times)</small>
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

        function showSlidesForm(what) {
            $('.song-only').hide();
            $('.song-or-video-only').hide();
            $('.song-or-slides-only').show();
            $('.slide-only').show();
            $('#all-items-button').text('Slides');
            if (what) 
                $("input[name='title_2']").val(what);
        }

        function showVideoForm(what) {
            $('.song-only').hide();
            $('.song-or-slides-only').hide();
            $('.song-or-video-only').show();
            $('.video-only').show();
            $('#all-items-button').text('Videoclips');
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


        /* change form content depending on tyep of song
        */
        @if (isset($song) && $song->title_2=='video')
            showVideoForm();
        @endif
        @if (isset($song) && $song->title_2=='slides' )
            showSlidesForm();
        @endif

    </script>

    
@stop
