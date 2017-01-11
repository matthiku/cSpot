
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<?php Use Carbon\Carbon; ?>

@extends('layouts.main')

@section('title', "Create or Update Plan Item")

@section('plans', 'active')




@section('content')



    @include('layouts.flashing')



    {!! Form::model( $item, array(
        'route'  => array('cspot.items.update', $item->id), 
        'method' => 'put', 
        'id'     => 'inputForm',
        'class'  => 'form-horizontal',
        'files'  => true,
        )) !!}

    {!! Form::hidden('seq_no', $seq_no) !!}
    {!! Form::hidden('plan_id', isset($plan) ? $plan->id : $item->plan_id ) !!}



    <!-- 
        header area 
    -->
    <div class="row" id=title-bar>



        <!-- title text -->
        <div class="col-md-6">


            <div class="float-right">

                <!-- hide SUBMIT button until changes are made   -->
                @if( false && Auth::user()->ownsPlan($item->plan_id) )
                    <span class="save-buttons submit-button hidden-lg-down" onclick="showSpinner()" style="display: none;">
                        {!! Form::submit('Save!'); !!}
                    </span>
                @endif

            </div>


            <h2 class="nowrap">

                <a href="{{ url('cspot/plans/'.$plan->id.'/items/'.$item->id.'/go/previous') }}"
                    onclick="showSpinner()" 
                    class="btn btn-secondary" role="button" id="go-previous-item"
                    title="go to previous item: '{{getItemTitle($item,'previous')}}'" data-toggle="tooltip" data-placement="right">
                    <i class="fa fa-angle-double-left fa-lg"></i>
                </a> 

                Manage Plan Item No {{$seq_no}}
                <a href="{{ url('cspot/plans/'.$plan->id.'/items/'.$item->id.'/go/next') }}"
                    onclick="showSpinner()" 
                    class="btn btn-secondary" role="button" id="go-next-item"
                    title="go to next item: '{{getItemTitle($item)}}'" data-toggle="tooltip" data-placement="right">
                    <i class="fa fa-angle-double-right fa-lg"></i>
                </a>

                <span class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="goToAnotherItem" 
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        &#9776;
                    </button>
                    <div class="dropdown-menu" aria-labelledby="goToAnotherItem">
                        <a class="dropdown-item" 
                            onclick="showSpinner()" 
                            href="{{ url('cspot/plans/'.$item->plan_id) }}/edit"><i class="fa fa-list-ul"></i>&nbsp;Back to Plan Overview</a>
                        <a class="dropdown-item"  
                            onclick="showSpinner()" 
                            href="{{ url('cspot/items/'.$item->id) }}/present"><i class="fa fa-tv"></i>&nbsp;Start presentation</a>
                        @if( Auth::user()->ownsPlan($item->plan_id) )
                            <a class="dropdown-item nowrap text-danger" item="button" href="{{ url('cspot/items/'. $item->id .'/delete') }}">
                                <i class="fa fa-trash" > </i>&nbsp; Delete this item!
                            </a>
                        @endif
                        <hr>
                        @foreach ($items as $menu_item)
                            @if ( Auth::user()->ownsPlan($plan->id) || ! $menu_item->forLeadersEyesOnly )
                                <a class="dropdown-item nowrap {{ $item->id==$menu_item->id ? 'bg-info' : '' }}"
                                    onclick="showSpinner()" 
                                    href="{{ url('cspot/plans/'.$plan->id.'/items').'/'.$menu_item->id.'/edit' }}">
                                    <small class="hidden-xs-down">{{ $menu_item->seq_no }} &nbsp;</small> 
                                    @if ($menu_item->song_id && $menu_item->song->title )
                                        @if ( $menu_item->song->title_2=='slides' || $menu_item->song->title_2=='video' )
                                            ({{ ucfirst($menu_item->song->title_2) }}) {{ $menu_item->song->title }}
                                        @else
                                            <i class="fa fa-music">&nbsp;</i><strong>{{ $menu_item->song->title }}</strong>
                                        @endif
                                    @else
                                        {{ $menu_item->comment }}
                                    @endif
                                </a>
                            @endif
                        @endforeach
                    </div>

                </span>


            </h2>
            <h5 class="hidden-md-down">
                of the 
                <a href="{{ url('cspot/plans/'.$plan->id)}}/edit" class="cancel-button">
                    Service plan for {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</a>
            </h5>
            <h4 class="hidden-lg-up">
                in
                <a href="{{ url('cspot/plans/'.$plan->id)}}/edit">
                    plan for {{ $plan->date->formatLocalized('%a, %d %b') }}</a>
            </h4>

        </div>


        <!-- action buttons -->
        <div class="col-md-6 float-right nowrap" data-item-id="{{ $item->id }}" 
            data-item-update-action="{{ route('cspot.api.items.update', $item->id) }}">

            @if( Auth::user()->ownsPlan($item->plan_id) )
                &nbsp;
                <span class="save-buttons submit-button" onclick="showSpinner()" style="display: none;">
                    {{-- {!! Form::submit('Save changes'); !!} --}}
                </span>
            @endif

            @if ($item->updated_at)
                <br>
                <small class="hidden-sm-down float-md-right float-xl-none">Last updated:
                    {{ Carbon::now()->diffForHumans( $item->updated_at, true ) }} ago
                </small>
            @endif

        </div>


    </div>







    <!-- 
        ITEM area 
    -->
    <div id="tabs"  style="max-width: 60rem; ">



        {{-- 
                ======================================================================================================    TABS headers 
        --}}


        <ul>

            @if ( $item->song_id )
                <li><a href="#song-details-tab">
                    <span class="hidden-sm-down">{{ ucfirst($item->itemType()) }} </span>Details
                </a></li>
            @endif

            <li>
                <a href="#notes-tab">Notes
                    <small class="text-muted">{!!
                        ( $item->comment || $item->itemNotes->where('user_id', Auth::user()->id)->first() ) ? 
                            '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' 
                        !!}</small>
                </a>
            </li>

            @if ( $bibleTexts )
                <li><a href="#scripture-tab">Scripture</a></li>
            @endif

            <li><a href="#bg-images-tab" id="tab-for-bg-images"><span class="hidden-md-down">Background </span><span class="hidden-lg-up hidden-xs-down">BG </span>Images
                <small class="text-muted">({{ $item->files->count() }})</small>
            </a></li>

            @if ( $item->song_id )
                @if ($item->itemType()=='song' && $item->song->onsongs->count()===0)
                <li><a href="#lyrics-tab">{{ $item->itemType()=='song' ? 'Lyrics' : ucfirst($item->itemType()).'(s)' }}
                    <small class="text-muted">{!! $item->song->lyrics ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' !!}</small>
                </a></li>
                @endif
                @if ( $item->itemType()=='song')
                    <li><a href="#onsong-tab">OnSong
                        <small class="text-muted">{!! $item->song->onsongs->count() ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' !!}</small>
                    </a></li>
                    @if ($item->song->onsongs->count()===0)
                        <li><a href="#chords-tab">Chords
                            <small class="text-muted">{!! $item->song->chords ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' !!}</small>
                        </a></li>
                    @endif
                    <li><a href="#sheet-tab"><span class="hidden-md-down">Sheet </span> Music
                        <small class="text-muted">{!! $item->song->files->count() ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' !!}</small>
                    </a></li>
                @endif
            @endif

        </ul>





        {{-- 
                ======================================================================================================   actual TABS 
        --}}

        @if ( $item->song_id )

            {!! Form::hidden('song_id', $item->song_id) !!}

            <div id = "song-details-tab">

                <div class="card card-block float-center p-b-1" style="max-width: 40rem; ">

                    <div class="row center song-details">
                        <h5 class="card-title">
                            @if ( $item->itemType()=='song')
                                <i class="float-left fa fa-music"></i> &nbsp;
                                <i class="float-right fa fa-music"></i>
                            @endif
                            @if ($item->song->book_ref)
                                <small>({{ $item->song->book_ref }})</small>
                            @endif
                            {{ $item->song->title ? $item->song->title : '' }}
                            @if ( $item->itemType()=='song' && $item->song->title_2)
                                <br>({{ $item->song->title_2 }})
                            @endif
                        </h5>
                    </div>

                    <div class="card-text song-details">


                        @if ( $item->itemType()=='song')

                            <div class="row center text-muted">
                                @if ($item->song_freshness)
                                    <strong>Song 'Freshness': {{ $item->song_freshness > 50 ? '&#127823;' : '&#127822;' }}<small>{{ $item->song_freshness }}%</small></strong>
                                    <br>
                                @endif
                                @if ( $usageCount )
                                    This song was used before in <strong>{{ $usageCount }}</strong> service(s) -<br>
                                    <strong>{{ $item->song->leadersUsingThisSong($plan->leader_id)->count() }}</strong> times by the leader of this plan and
                                    lastly <strong title="{{ $newestUsage->date }}">
                                        {{ Carbon::now()->diffForHumans( $newestUsage->date, true ) }} ago</strong>
                                @else
                                    Song was never used before in a service
                                @endif
                            </div>

                            <div class="row mt-1">
                                                            
                                <div class="card center mb-0">
                                    <div class="card-block p-0">
                                        <h5 class="card-title float-left">&#127896; Instructions for Music Team:
                                            <br>
                                            <small class="text-muted float-left">(e.g. for having a verse without music)</small>
                                        </h5>
                                        <div class="card-text">
                                            @if (Auth::user()->ownsPlan( $plan->id ))
                                                <pre id="key-item-id-{{ $item->id }}" class="editable-item-field form-control form-control-success mb-0">{{ $item->key }}</pre>
                                            @else
                                                 <pre class="w-100 mb-0">{{ $item->key }}</pre>
                                            @endif
                                        </div>
                                        <div class="card-text">
                                            @if (Auth::user()->ownsPlan( $plan->id ))
                                                <a      href="#" class="card-link float-right form-control" id="key-notes-erase-link"  
                                                        onclick="deleteItemNote('key', 'key-item-id-{{ $item->id }}', '{{ route('cspot.api.item.update') }}')" 
                                                        style="max-width: 150px; display: {{ $item->key ? 'initial' : 'none' }}">
                                                    <small><i class="fa fa-remove text-muted"></i> clear note</small>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>

                        @else

                            <span class="btn btn-secondary mb-1">
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" id="toggle-show-hideTitle" 
                                          class="custom-control-input" {{ $item->hideTitle ? 'checked="checked"' : '' }}
                                        onclick="toggleHideTitle(this, 'hideTitle-item-id-{{ $item->id }}', '{{ route('cspot.api.item.update') }}')"
                                        {{ Auth::user()->ownsPlan($plan->id) ? '' : ' disabled' }}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description" id="hideTitle-item-id-{{ $item->id }}"
                                        >title of this item will not be shown in the presentation</span>
                                </label>
                            </span>

                        @endif


                        <br>

                        <div class="row mb-1">                            
                            <div class="col-sm-12 col-md-3 full-btn">
                                @if ($item->song->youtube_id)
                                    <a href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}" 
                                        target="new" class="fully-width btn btn-outline-primary btn-sm">
                                    <i class="red fa fa-youtube-play"></i><br><small>play<span class="hidden-lg-down"> on YouTube</span></small></a>
                                @else
                                    <a href="#" class="fully-width btn btn-outline-secondary btn-sm disabled"
                                          title="Missing YouTube Video" data-toggle="tooltip">
                                    <i class="red fa fa-youtube-play"></i><br><small>no link found</small></a>
                                @endif
                            </div>
                            <div class="col-sm-12 col-md-3 full-btn">
                                @if ( $item->song->ccli_no > 1000 && 'MP'.$item->song->ccli_no != $item->song->book_ref )
                                    <a href="https://songselect.ccli.com/Songs/{{ $item->song->ccli_no }}" 
                                        target="new" class="fully-width btn btn-outline-primary btn-sm">
                                    <img src="{{ url('/') }}/images/songselectlogo.png" width="14"><br><small>show<span class="hidden-lg-down"> on SongSelect</span></small></a>
                                @else
                                    <a href="#" class="fully-width btn btn-outline-secondary btn-sm disabled"
                                          title="Missing SongSelect Link!" data-toggle="tooltip">
                                    <img src="{{ url('/') }}/images/songselectlogo.png" width="14"><br><small>CCLI N<sup>o</sup>. missing</small></a>
                                @endif
                            </div>
                            <div class="col-sm-12 col-md-3 full-btn">
                                @if ($item->song->hymnaldotnet_id!='')
                                    <a href="{{ $item->song->hymnaldotnet_id }}" 
                                        target="new" class="fully-width btn btn-outline-primary btn-sm">
                                    &#127929;<br><small>play<span class="hidden-lg-down"> on Hymnal.Net</span></small></a>
                                @else
                                    <a href="#" class="fully-width btn btn-outline-secondary btn-sm disabled"
                                          title="No Hymnal.Net Link" data-toggle="tooltip">
                                    &#127929;<br><small>no link found</small></a>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            @if ( Auth::user()->ownsPlan($item->plan_id) )
                                <div class="col-sm-12 col-md-3 full-btn">
                                    <a href="#" class="fully-width btn btn-outline-primary btn-sm" 
                                        onclick="showSongSearchInput(this, '.song-search')" 
                                    ><i class="fa fa-exchange"></i><br><small>change song/slide</small></a>
                                </div>
                                <div class="col-sm-12 col-md-3 full-btn">
                                    <a href="#" class="fully-width btn btn-outline-primary btn-sm" 
                                        onclick="unlinkSong(this, {{ $item->id.', '.$item->song_id.', \''.route('plans.edit', $item->plan_id)."'" }})" 
                                        title="Detach song from this item" data-toggle="tooltip"
                                    ><i class="fa fa-unlink"></i><br><small>unlink song/slide</small></a>
                                </div>
                            @endif
                            @if (Auth::user()->isEditor() )
                                <div class="col-sm-12 col-md-3 full-btn">
                                    <a href="#" class="fully-width btn btn-outline-primary btn-sm" accesskey="69" id="go-edit"
                                        onclick="showSpinner();location.href='{{ route('songs.edit', $item->song_id) }}'" 
                                          title="Edit details of this song" data-toggle="tooltip"
                                    ><i class="fa fa-edit"></i><br><small>edit song/slide</small></a>
                                </div>
                            @endif
                        </div>
                        

                    </div>

                    <!-- show song search input field if requested -->
                    <div class="row form-group song-search" style="display: none">
                        To search for another song/slide,

                        @include('cspot.snippets.song_search')

                    </div>

                </div>                        
            </div>
        @endif



        <div id="notes-tab">
            @include('cspot.snippets.comment_input')
        </div>



        <div id="scripture-tab">
            @foreach ($bibleTexts as $btext)
                <h5>{{ $btext->display }} ({{ $btext->version_abbreviation }})</h5>
                <div>
                    {!! $btext->text !!}
                </div>
                <div class="small">
                    {!! $btext->copyright !!}
                </div>
            @endforeach
        </div>



        <div id="bg-images-tab">

            <div class="show-bg-images-instructions hidden float-right">
                <span class="btn btn-sm btn-outline-info ml-1" onclick="
                    $('.bg-images-instructions').show();
                    $('.show-bg-images-instructions').hide();
                    $('.confirm-bg-images-instructions').show();
                    localStorage.setItem('config-imagesInstructionsConfirmed', false);
                    ">&#128161; Help!</span>
            </div>
            <div class="card bg-images-instructions">
                <p class="small">You can either upload a new image or select one of the images already stored in cSPOT.<br>
                    <strong>Images</strong> can be used as background for scripture items or song items or for presentations.</p>
                <p class="small">When used for song- or scripture-items and when more than one image is attached here, the 
                    images will change for each slide in the sequence given here, in a rotating fashion.
                </p>
                <p class="small">
                    <strong>Background</strong> images will be stretched/shrank in order to fill the whole background of the presentation space, but if you use 
                    images with a category name of <i>"Presentation"</i>, the images will retain their original width-to-height aspect ratio, 
                    with the height adapted to the height of the presentation area.
                    <span class="confirm-bg-images-instructions float-right">
                        <span class="btn btn-sm btn-outline-danger mr-1" onclick="
                            $('.bg-images-instructions').hide();
                            $('.show-bg-images-instructions').show();
                            $('.confirm-bg-images-instructions').hide();
                            localStorage.setItem('config-imagesInstructionsConfirmed', true);
                            ">&#128504; Got it!</span>
                    </span>
                </p>
            </div>

            {{-- link to open the Add File dialog --}}
            <span data-item-type="add-file" class="add-another-image-link btn btn-sm btn-success float-right" onclick="
                    $('#col-2-file-add').show();
                    $('#show-location-selection').hide();
                    $('.add-another-image-link').hide();
                    $('.image-selection-slideshow').hide();
                    $('.show-file-add-button').hide();
                    location.href='#select-category';">
                <i class="fa fa-file"></i>
                Add image
            </span>

            {!! $item->files->count() ? '' : '<p class="small center">(no images attached yet)</p>' !!}

            @if( Auth::user()->ownsPlan($plan->id) )
                <?php 
                    // make sure the files are sorted by seq no
                    $files  = $item->files->all(); 
                    $fcount = count($files);
                    $key    = 0; // we can't use a $key in the foreach statement as it's a re-sorted collection!
                ?>

                <div class="center" style="max-width: 380px;">

                    @foreach ($files as $file)
                        <div id="file-{{ $file->id }}" style="padding=2px;{{ ($key % 2 == 1) ? 'background-color: #eee;' : 'background-color: #ddd;' }}">

                            <div class="float-left" style="min-width: 60px;">
                                @if ( $fcount>1 && $key>0 )
                                    <a href="{{ url("cspot/items/$item->id/movefile/$file->id/up") }}" title="Move up" 
                                        onclick="showSpinner()" class="btn btn-info btn-sm move-button mb-1" role="button" >
                                        <i class="fa fa-angle-double-up fa-lg"> </i> 
                                    </a>
                                @endif
                                @if ( $fcount>1 && $key>0 && $fcount>1 && $key<$fcount-1 )
                                    <br>
                                @endif
                                @if ( $fcount>1 && $key<$fcount-1 )
                                    <a href="{{ url("cspot/items/$item->id/movefile/$file->id/down") }}" title="Move down" 
                                        onclick="showSpinner()" class="btn btn-info btn-sm move-button" role="button" >
                                        <i class="fa fa-angle-double-down fa-lg"> </i> 
                                    </a>
                                @endif
                                @if (session()->has('newFileAdded') && session('newFileAdded') == $file->id )
                                    <br><i class="fa fa-check"></i>Added.
                                @endif
                            </div>
                            @if ( $fcount>1)
                                <div class="center float-right">Order:<br>{{ $file->pivot->seq_no }}</div>
                            @endif

                            @include ('cspot.snippets.show_files')

                        </div>
                        <?php $key++; ?>

                    @endforeach

                </div>
                <br>
                
                {{-- link to open the Add File dialog --}}
                @if ($item->files->count() > 1)
                    <span data-item-type="add-file" class="add-another-image-link btn btn-secondary" onclick="
                            $('.add-another-image-link').hide();
                            $('#col-2-file-add').show();
                            location.href='#select-category';">
                        <i class="fa fa-file"></i>
                        Add another image
                    </span>
                @endif

                {{-- Form to add new (image) file --}}
                <div id="col-2-file-add" class="mb-1 dropzone hidden">
                    @include('cspot.snippets.add_files')
                </div>
            
            @else

                @foreach ($item->files as $file)
                    @include ('cspot.snippets.show_files')
                @endforeach
                
           @endif

        </div>




        @if ( $item->song_id )


            <div id="lyrics-tab">


                {{-- LYRICS content 
                     (only show when there is no OnSong content!)
                --}}
                @if ($item->itemType()=='song' && $item->song->onsongs->count()===0)
                    <p class="text-info">
                        ({{ $item->song->sequence ? 'Sequence: '.$item->song->sequence : 'No sequence predefined' }})
                        @if (Auth::user()->isEditor())
                            <small>(Edit the Sequence in the OnSong tab)</small>
                        @endif
                    </p>
                    <p class="small"><strong>NOTE: </strong>If there are also <strong>lyrics</strong> defined in the <em>OnSong</em> tab, 
                        the lyrics here will <strong>not be used!</strong></p>

                    <pre id="lyrics-song-id-{{ $item->song->id }}" {{ (Auth::user()->isEditor()) ? 'class=edit_area' : '' }}>{{ $item->song->lyrics }}</pre>

                    <small class="text-muted">(click to edit!)</small>
                @endif



                @if ($item->itemType()=='video')

                    <small>(possible time parameter was ignored!)</small>
                    <br>
                    <iframe width="560" height="315" 
                        src="https://www.youtube.com/embed/{{ strpos($item->song->youtube_id,'&')!= false ? explode('&', $item->song->youtube_id)[0] : $item->song->youtube_id }}" 
                        frameborder="0" allowfullscreen>                                    
                    </iframe>
                @endif


            </div>





            @if ( $item->itemType()=='song')



                {{-- CHORDS content 
                     (only show when there is no OnSong content!)
                --}}
                @if ($item->song->onsongs->count()===0)
                    <div id="chords-tab">
                        <pre id="chords-song-id-{{ $item->song->id }}" class="{{ (Auth::user()->isEditor()) ? 'edit_area' : '' }} show-chords">{{ $item->song->chords }}</pre>
                        <span class="btn btn-sm btn-outline-primary" 
                            onclick="$('#show-chords-as-onsong').text(joinLyricsAndChordsToOnSong($('#chords-song-id-{{ $item->song->id }}').text()));$(this).hide();">
                            show OnSong-encoded copy</span>
                        <pre id="show-chords-as-onsong"></pre>
                    </div>
                @endif



                {{-- OnSong content 
                --}}
                <div id="onsong-tab">

                    <div class="show-onsong-instructions hidden float-right">
                        <span class="btn btn-sm btn-outline-info ml-1" onclick="
                            $('.onsong-instructions').show();
                            $('.show-onsong-instructions').hide();
                            $('.confirm-onsong-instructions').show();
                            localStorage.setItem('config-onsongInstructionsConfirmed', false);
                            ">&#128161; Help!</span>
                    </div>


                    <div class="card onsong-instructions small">
                        <div class="card-block">
                            <h6>Why is c-SPOT using the OnSong Format?</h6>

                            <p> To provide musical information about a song for the musicians, various formats are available. 
                                Many musicians are familiar with and use the <strong>"Chords-over-Lyrics" format</strong> 
                                instead of music notes or sheet music.</p>

                            <p> With that format, the <strong>advantage</strong> for c-SPOT is that we do not have to store the lyrics in a separate
                                place but can use the lyrics of this format for the presentation to the congregation and the 
                                chords-over-lyrics-format for the musicians, both drawing from the same source.</p>

                            <p> <strong>OnSong</strong> encoded chords is a way to store these chords and lyrics in music apps. (More info 
                                <a target="new" href="http://www.onsongapp.com/docs/features/formats/onsong/" class="text-info">here <i class="fa fa-external-link"></i></a>).
                                c-SPOT uses this format but retains each song part (like verses, chorus, bridge etc) in seperate blocks (called <strong>song parts</strong>).
                                The main difference to the popular 'chords-over-lyrics' format is that the chords are interspersed within the lyrics,
                                enclosed in square brackets, like this:<br><i>Amazing [D]Grace, how [G]sweet the [D]sound</i></p>

                            @if (Auth::user()->isEditor())
                                <h6>Add new parts or edit existing parts</h6>

                                <p>When adding <strong>new song parts</strong>, you can just <strong>copy&amp;paste</strong> from an existing source and that can be either 
                                    in OnSong format or in the legacy "chords-over-lyrics" format.</p>

                                <p>When editing <strong>existing parts</strong>, you can choose between 3 different editors: 
                                    (just click into the song part to show the editor selection)
                                    <ul>
                                        <li>OnSong editor - here you can drag the chords to the left or right, leaving the lyrics alone</li>
                                        <li>Plain text editor - here you can edit the data in the original OnSong format</li>
                                        @endif
                                        <span class="confirm-onsong-instructions float-right btn btn-sm btn-outline-danger mr-1" onclick="
                                            $('.onsong-instructions').hide();
                                            $('.show-onsong-instructions').show();
                                            $('.confirm-onsong-instructions').hide();
                                            localStorage.setItem('config-onsongInstructionsConfirmed', true);
                                            ">&#128504; OK, understood!</span>
                                        @if (Auth::user()->isEditor())
                                        <li>Chords-over-Lyrics editor - this is helpful for editing just the lyrics.</li>
                                    </ul>
                                </p>
                            @endif
                        </div>
                    </div>

                    @if (Auth::user()->isEditor())
                        <span onclick="insertNewOnSongRow();" class="btn btn-sm btn-success float-right insertNewOnSongRow-link">
                            <i class="fa fa-plus"></i> Add new Part</span>
                    @endif

                    <p class="text-info">
                        @if (Auth::user()->isEditor())
                            <label class="mb-0">Sequence:
                                <span id="sequence-song-id-{{ $item->song->id }}" onclick="$('.show-input-hint').show();" 
                                    class="editable-song-field">{{ $item->song->sequence }}</span>
                                <i class="fa fa-pencil text-muted"> </i>
                            </label>
                            <br><small class="text-primary show-input-hint" style="display: none;">The sequence can only contain code as listed in the table below!!</small>
                        @else
                            ({{ $item->song->sequence ? 'Sequence: '.$item->song->sequence : 'No sequence predefined' }})
                        @endif
                    </p>


                    @php
                        $song = $item->song;
                        $songParts = App\Models\SongPart::orderby('sequence')->get();
                    @endphp
                    @include('cspot.snippets.onsong')

                </div>
                


                {{-- Sheetmusic content 
                --}}
                <div id="sheet-tab">
                    @foreach ($item->song->files as $file)
                        @if ($item->song->license=='PD' || Auth::user()->isMusician() )
                            @include ('cspot.snippets.show_files', ['edit' => false])
                        @else
                            <span>(copyrighted material)</span>
                        @endif
                    @endforeach
                </div>

            @endif


        @endif





    </div>



    {!! Form::close() !!}

    {{-- activate the tabs 
    --}}
    <script>
        $(document).ready( function() {
            $( "#tabs" ).tabs({
                event: "mouseover",
                active: {{ session()->has('newFileAdded') ? ($item->song_id ? '2' : '1') : '0' }}
            });
        });

        // provide item data on the client side
        cSpot.item = {!! json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT ) !!};
    </script>



@stop