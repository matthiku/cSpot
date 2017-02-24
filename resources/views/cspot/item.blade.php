
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<?php Use Carbon\Carbon; ?>

@extends('layouts.main')

@section('title', "Create or Update Plan Item")

@section('plans', 'active')




@section('content')



    @include('layouts.flashing')



    <!-- 
        header area 
    -->
    <div class="d-flex justify-content-around bg-faded py-xl-2 py-md-1 mb-lg-2 mb-md-1" id="title-bar">

        <a href="{{ url('cspot/plans/'.$plan->id.'/items/'.$item->id.'/go/previous') }}"
            class="btn btn-primary" role="button" id="go-previous-item"
            title="go to previous item: '{{getItemTitle($item,'previous')}}'" data-toggle="tooltip" data-placement="right">
            <i class="fa fa-angle-double-left fa-lg"></i>
        </a> 

        <a class="btn btn-primary hidden-xs-down" role="button" title="Start presentation" data-toggle="tooltip" data-placement="left"
            href="{{ url('cspot/items/'.$item->id) }}/present"><i class="fa fa-tv"></i></a>

        <h2 class="nowrap">
            Edit Plan Item N<sup>o</sup> {{$seq_no}}
            <small class="hidden-md-down">
                <a href="{{ url('cspot/plans/'.$plan->id)}}/edit" class="small cancel-button">
                    for {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</a>
            </small>
            <small class="small hidden-lg-up">
                <a href="{{ url('cspot/plans/'.$plan->id)}}/edit" class="small">
                    for {{ $plan->date->formatLocalized('%a, %d %b') }}</a>
            </small>
        </h2>


        <span class="dropdown">

            <button class="btn btn-primary dropdown-toggle h-100" type="button" id="goToAnotherItem" 
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                &#9776;
            </button>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="goToAnotherItem">
                <a class="dropdown-item" 
                    href="{{ url('cspot/plans/'.$item->plan_id) }}/edit"><i class="fa fa-list-ul"></i>&nbsp;Back to Plan Overview</a>
                <a class="dropdown-item hidden-sm-up"  
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


        <a href="{{ url('cspot/plans/'.$plan->id.'/items/'.$item->id.'/go/next') }}"
            class="btn btn-primary" role="button" id="go-next-item"
            title="go to next item: '{{getItemTitle($item)}}'" data-toggle="tooltip" data-placement="left">
            <i class="fa fa-angle-double-right fa-lg"></i>
        </a>
    </div>








    <!-- 
        ITEM area 
    -->
    <div class="d-flex justify-content-center">
        <div id="tabs"  style="min-width: 60%;">

            {{-- 
                    ======================================================================================================    TABS headers 
                    show only the tabs that are needed accoring to the item type
            --}}
            <ul>

                @if ( $item->song_id )
                    <li>
                        <a href="#song-details-tab">
                            <span class="hidden-sm-down">{{ ucfirst($item->itemType()) }} </span>Details
                        </a></li>
                @endif


                <li>
                    <a href="#notes-tab">
                        Notes
                        <small class="text-muted">{!!
                            ( $item->comment || $item->itemNotes->where('user_id', Auth::user()->id)->first() ) ? 
                                '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' 
                            !!}</small>
                    </a></li>


                @if ( $bibleTexts )
                    <li>
                        <a href="#scripture-tab">
                            Scripture
                        </a></li>
                @endif


                <li>
                    <a href="#bg-images-tab" id="tab-for-bg-images"><span class="hidden-sm-down">
                        Background </span><span class=" hidden-xs-down hidden-md-up">BG </span>Images
                        <small class="text-muted">({{ $item->files->count() }})</small>
                    </a></li>



                @if ( $item->song_id )

                    @if ($item->itemType()=='slides' || ($item->itemType()=='song' && $item->song->onsongs->count()===0) )
                        <li>
                            <a href="#lyrics-tab">{{ $item->itemType()=='song' ? 'Lyrics' : ucfirst($item->itemType()).'(s)' }}
                                <small class="text-muted">{!! $item->song->lyrics ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' !!}</small>
                            </a></li>
                    @endif

                    @if ( $item->itemType()=='song')
                        <li>
                            <a href="#onsong-tab">
                                OnSong
                                <small class="text-muted">{!! $item->song->onsongs->count() ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' !!}</small>
                            </a></li>

                        @if ($item->song->onsongs->count()===0)
                            <li>
                                <a href="#chords-tab">Chords
                                    <small class="text-muted">{!! $item->song->chords ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' !!}</small>
                                </a></li>
                        @endif

                        <li>
                            <a href="#sheet-tab"><span class="hidden-md-down">
                                Sheet </span> Music
                                <small class="text-muted">{!! $item->song->files->count() ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' !!}</small>
                            </a></li>
                    @endif

                @endif

            </ul>





            {{-- 
                    ======================================================================================================   actual TABS 
            --}}
            @if ( $item->song_id )

                <div id = "song-details-tab" class="p-0">

                    <div class="card card-block p-1 mt-4" style="max-width: 45rem; ">

                        <div class="card-title text-center song-details px-2">
                            <h4>
                                @if ( $item->itemType()=='song')
                                    <i class="fa fa-music float-left"></i>
                                    <i class="ml-auto fa fa-music float-right"></i>
                                @endif
                                @if ($item->song->book_ref)
                                    ({{ $item->song->book_ref }})
                                @endif
                                {{ $item->song->title ? $item->song->title : '' }}
                                @if ( $item->itemType()=='song' && $item->song->title_2)
                                    <br>
                                    <small>({{ $item->song->title_2 }})</small>
                                @endif
                            </h4>
                        </div>


                        <div class="card-text container song-details">


                            @if ( $item->itemType()=='song')

                                <div class="row justify-content-between text-muted">
                                    <div class="col-12 text-center">
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
                                </div>

                                <div class="row mt-4 mx-md-4">                                                            
                                    <div class="col-12 card mb-0 p-1">
                                        <div class="card-block p-0">
                                            <h5 class="card-title">&#127896; Instructions for Music Team:
                                                <br>
                                                <small class="text-muted">(e.g. for having a verse without music)</small>
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


                            <div class="row my-3 justify-content-center">                            
                                <div class="col-6 col-sm-4 col-md-3 mx-md-1 mx-lg-2 mx-xl-4">
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
                                <div class="col-6 col-sm-4 col-md-3 mx-md-1 mx-lg-2 mx-xl-4">
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
                                <div class="col-6 col-sm-4 col-md-3 mx-md-1 mx-lg-2 mx-xl-4">
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

                            <div class="row justify-content-center">
                                @if ( Auth::user()->ownsPlan($item->plan_id) )
                                    <div class="col-6 col-sm-4 col-md-3 mx-md-1 mx-lg-2 mx-xl-4 disabled">
                                        <a href="#" class="fully-width btn btn-outline-secondary btn-sm" 
                                            onclick="showSongSearchInput(this, '.song-search')" 
                                        ><i class="fa fa-exchange"></i><br><small>change song/slide</small></a>
                                    </div>
                                    <div class="col-6 col-sm-4 col-md-3 mx-md-1 mx-lg-2 mx-xl-4">
                                        <a href="#" class="fully-width btn btn-outline-primary btn-sm" 
                                            onclick="unlinkSong(this, {{ $item->id.', '.$item->song_id.', \''.route('plans.edit', $item->plan_id)."'" }})" 
                                            title="Detach song from this item" data-toggle="tooltip"
                                        ><i class="fa fa-unlink"></i><br><small>unlink song/slide</small></a>
                                    </div>
                                @endif
                                @if (Auth::user()->isEditor() )
                                    <div class="col-6 col-sm-4 col-md-3 mx-md-1 mx-lg-2 mx-xl-4">
                                        <a href="#" class="fully-width btn btn-outline-primary btn-sm" accesskey="69" id="go-edit"
                                            onclick="showSpinner();location.href='{{ route('songs.edit', $item->song_id) }}'" 
                                              title="Edit details of this song" data-toggle="tooltip"
                                        ><i class="fa fa-edit"></i><br><small>edit song/slide</small></a>
                                    </div>
                                @endif
                            </div>
                            

                        </div>
                    </div>                        
                </div>
            @endif



            <div id="notes-tab" class="mt-4 p-1">
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



            <div id="bg-images-tab" class="px-0 px-sm-1">

                <div class="card bg-images-instructions mb-2 p-1" style="max-width: 50rem;">
                    <p class="small">You can either upload a new image or select one of the images already stored in cSPOT.<br>
                        <strong>Images</strong> can be used as background for scripture items or song items or for presentations.</p>
                    <p class="small">When used for song- or scripture-items and when more than one image is attached here, the 
                        images will change for each slide in the sequence given here, in a rotating fashion.
                    </p>
                    <p class="small">
                        <strong>Background</strong> images will be stretched/shrank in order to fill the whole background of the presentation space, but if you use 
                        images with a category name of <i>"Presentation"</i>, the images will retain their original width-to-height aspect ratio, 
                        with the height adapted to the height of the presentation area.
                        <span class="confirm-bg-images-instructions link float-right">
                            <span class="btn btn-sm btn-outline-danger mr-1" onclick="
                                $('.bg-images-instructions').hide();
                                $('.show-bg-images-instructions').show();
                                $('.confirm-bg-images-instructions').hide();
                                localStorage.setItem('config-imagesInstructionsConfirmed', true);
                                ">&#128504; Got it!</span>
                        </span>
                    </p>
                </div>

                {!! $item->files->count() ? '' : '<p class="small mx-auto">(no images attached yet)</p>' !!}

                @if( Auth::user()->ownsPlan($plan->id) )
                    <?php 
                        // make sure the files are sorted by seq no
                        $files  = $item->files->all(); 
                        $fcount = count($files);
                        $key    = 0; // we can't use a $key in the foreach statement as it's a re-sorted collection!
                    ?>

                    <div class="mx-auto" style="max-width: 380px;">

                        @foreach ($files as $file)
                            <div class="rounded mb-1" id="file-{{ $file->id }}" style="padding=2px;{{ ($key % 2 == 1) ? 'background-color: #eee;' : 'background-color: #ddd;' }}">

                                <div class="mt-1 ml-2 pr-1 float-left">
                                    @if ( $fcount>1 && $key>0 )
                                        <div class="mb-2"><a href="{{ url("cspot/items/$item->id/movefile/$file->id/up") }}" title="Move up" 
                                            onclick="showSpinner()" class="btn btn-info btn-sm move-button" role="button" >
                                            <i class="fa fa-angle-double-up fa-lg"> </i> 
                                        </a></div>
                                    @endif
                                    @if ( $fcount>1 && $key<$fcount-1 )
                                        <div><a href="{{ url("cspot/items/$item->id/movefile/$file->id/down") }}" title="Move down" 
                                            onclick="showSpinner()" class="btn btn-info btn-sm move-button" role="button" >
                                            <i class="fa fa-angle-double-down fa-lg"> </i> 
                                        </a></div>
                                    @endif
                                    @if (session()->has('newFileAdded') && session('newFileAdded') == $file->id )
                                        <br><i class="fa fa-check"></i>Added.
                                    @endif
                                </div>

                                @include ('cspot.snippets.show_files')

                            </div>
                            <?php $key++; ?>

                        @endforeach

                    </div>
                    <br>
                    
                    {{-- link to open the Add File dialog --}}
                    @if ($item->files->count() >= 1)
                        <span data-item-type="add-file" class="add-another-image-link btn btn-secondary" onclick="
                                $('.add-another-image-link').hide();
                                $('#col-2-file-add').show();
                                location.href='#select-category';">
                            <i class="fa fa-file"></i>
                            Add another image
                        </span>
                    @endif

                    <div class="show-bg-images-instructions show-file-add-button add-another-image-link hidden float-right">
                        <span class="btn btn-sm btn-outline-info link ml-1" onclick="
                            $('.bg-images-instructions').show();
                            $('.show-bg-images-instructions').hide();
                            $('.confirm-bg-images-instructions').show();
                            localStorage.setItem('config-imagesInstructionsConfirmed', false);
                            ">&#128161; Help!</span>
                    </div>

                    {{-- link to open the Add File dialog --}}
                    @if ($item->files->count() == 0)
                    <span data-item-type="add-file" class="add-another-image-link link btn btn-sm btn-success float-right" onclick="
                            $('#col-2-file-add').show();
                            $('#show-location-selection').hide();
                            $('.add-another-image-link').hide();
                            $('.image-selection-slideshow').hide();
                            $('.show-file-add-button').hide();
                            location.href='#select-category';">
                        <i class="fa fa-file"></i>
                        Add image
                    </span>
                    @endif


                    {{-- Form to add new (image) file --}}
                    <div id="col-2-file-add" class="mb-1 dropzone hidden">

                        {!! Form::model( $item, array(
                            'route'  => array('cspot.items.update', $item->id), 
                            'method' => 'put', 
                            'id'     => 'inputForm',
                            'class'  => 'form-horizontal',
                            'files'  => true,
                            )) !!}
                        {!! Form::hidden('seq_no', $seq_no) !!}
                        {!! Form::hidden('plan_id', isset($plan) ? $plan->id : $item->plan_id ) !!}

                        @include('cspot.snippets.add_files')

                        {!! Form::close() !!}

                    </div>
                
                @else

                    @foreach ($item->files as $file)
                        @include ('cspot.snippets.show_files')
                    @endforeach
                    
               @endif

            </div>




            @if ( $item->song_id )


                <div id="lyrics-tab" class="px-0 px-sm-1">


                    {{-- LYRICS content 
                         (only show when there is no OnSong content!)
                    --}}
                    @if ( $item->itemType()=='slides' ||  ($item->itemType()=='song' && $item->song->onsongs->count()===0))
                        <p class="text-info">
                            ({{ $item->song->sequence ? 'Sequence: '.$item->song->sequence : 'No sequence predefined' }})
                            @if (Auth::user()->isEditor()  &&  $item->itemType()=='song')
                                <small>(Edit the Sequence in the OnSong tab)</small>
                            @endif
                            @if (Auth::user()->isEditor()  &&  $item->itemType()=='slides')
                                <br>Edit Slides Sequence:
                                <span id="sequence-song-id-{{ $item->song->id }}" onclick="$('.show-input-hint').show();" 
                                   class="editable-song-field lora link">{{ $item->song->sequence }}</span>
                            @endif
                        </p>

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
                    <div id="onsong-tab" class="px-0 px-sm-1">

                        <div class="show-onsong-instructions hidden float-right">
                            <span class="btn btn-sm link btn-outline-info float-right ml-1" onclick="
                                $('.onsong-instructions').show();
                                $('.show-onsong-instructions').hide();
                                $('.confirm-onsong-instructions').show();
                                localStorage.setItem('config-onsongInstructionsConfirmed', false);
                                ">&#128161; OnSong Help</span>
                        </div>


                        <div class="card onsong-instructions small">
                            <div class="card-block">
                                <h6>Why is c-SPOT using the OnSong (also called ChordPro) Format?</h6>

                                <p> To provide musical information about a song for the musicians, various formats are available. 
                                    Many musicians are familiar with and use the <strong>"Chords-over-Lyrics" format</strong> 
                                    instead of music notes or sheet music.</p>

                                <p> With that format, the <strong>advantage</strong> for c-SPOT is that we do not have to store the lyrics in a separate
                                    place but can use the lyrics of this format for the presentation to the congregation and the 
                                    chords-over-lyrics-format for the musicians, both drawing <strong>from the same source</strong>.</p>

                                <div class="card float-right">
                                    <div class="card-block bg-white p-1 rounded mb-3">
                                        <i>Amazing [D]Grace, how [G]sweet the [D]sound</i>
                                    </div>
                                    <div class="card-block bg-white p-1 rounded">
                                        Will be presented like this:
                                        <pre class="mx-4 m-0 red">        D          G         D    </pre>
                                        <pre class="mx-4 m-0    ">Amazing Grace, how sweet the sound</pre>
                                    </div></div>

                                <p> The main difference to the popular 'chords-over-lyrics' format (which we still use for <i>presenting</i> the chords!)
                                    is that the chords are interspersed within the lyrics
                                    and enclosed in square brackets.</p>

                                <p> The <strong>OnSong</strong> (or ChordPro) format is a common way to store chords and lyrics together in music apps. (More info 
                                    <a target="new" href="http://www.onsongapp.com/docs/features/formats/onsong/" class="text-info">here <i class="fa fa-external-link"></i></a>).
                                    While using this format, we still keep each <strong>song part</strong> (like verses, chorus, bridge etc) in seperate blocks.

                                @if (Auth::user()->isEditor())
                                    <h6>How to Add New Parts or Edit Existing Parts</h6>

                                    <p>When adding <strong>new song parts</strong>, you can just <strong>copy&amp;paste</strong> from an existing source and that can be either 
                                        in OnSong format or in the legacy "chords-over-lyrics" format.</p>

                                    <p>When editing <strong>existing parts</strong>, you can choose between 3 different editors: 
                                        (just click into the song part to show the editor selection)
                                        <ul>
                                            <li><strong>OnSong editor</strong> - Drag just the chords to the left or right, leaving the lyrics alone</li>
                                            <li><strong>Plain text editor</strong> - Edit the lyrics and chords data in the original OnSong format</li>
                                            @endif
                                            <span class="confirm-onsong-instructions float-right btn btn-sm btn-outline-danger link mr-1" onclick="
                                                $('.onsong-instructions').hide();
                                                $('.show-onsong-instructions').show();
                                                $('.confirm-onsong-instructions').hide();
                                                localStorage.setItem('config-onsongInstructionsConfirmed', true);
                                                ">&#128504; OK, understood!</span>
                                            @if (Auth::user()->isEditor())
                                            <li><strong>Chords-over-Lyrics editor</strong> - this is helpful for editing just the lyrics.</li>
                                        </ul>
                                        (For more information about the editor choices, check the help provided when adding or editing a song part here)
                                    </p>
                                @endif
                            </div>
                        </div>


                        <p class="show-onsong-instructions hidden text-info my-0">
                            @if (! Auth::user()->isEditor())
                                ({{ $item->song->sequence ? 'Sequence: '.$item->song->sequence : 'No sequence predefined' }})
                            @endif
                        </p>


                        @php
                            $song = $item->song;
                            $songParts = getRemainingSongParts($song);
                        @endphp
                        
                        <div class="show-onsong-instructions hidden">                        
                            @include('cspot.snippets.onsong')
                        </div>

                    </div>
                    


                    {{-- Sheetmusic content 
                    --}}
                    <div id="sheet-tab" class="px-0 px-sm-1">
                        @foreach ($item->song->files as $file)
                            @if ($item->song->license=='PD' || Auth::user()->isMusician() )
                                @include ('cspot.snippets.show_files', ['edit' => false])
                            @else
                                <span>(copyrighted material)</span>
                            @endif
                        @endforeach
                        @if (! $item->song->files->count())
                            <span class="nofile-attached">No sheetmusic attached yet</span>
                        @endif

                        {{-- provide UPLOAD facility for a full song 
                        --}}
                        <div class="show-sheetmusic-upload-form rounded-bottom py-2 px-3 bg-faded text-primary small">
                            Select (or drop here) an image file containing sheet music for this song:
                            <input id="fileuploadsheetmusic" type="file" name="file" data-url="{{ route('uploadsheetmusicfile', isset($song) ? $song->id : '0') }}">
                            <input id="sheetmusicfile-submit-method" type="hidden" name="_method" value="POST">
                            <div id="upload-progress">
                                <div class="bar" style="width: 0%;"></div>
                            </div>
                        </div>

                    </div>

                @endif


            @endif




        </div>
    </div>

    @if ($item->updated_at)
        <small class="ml-4 d-flex justify-content-center hidden-xs-down">Item last updated
            {{ Carbon::now()->diffForHumans( $item->updated_at, true ) }} ago
        </small>
    @endif    



    <script>
        {{-- activate the tabs 
        --}}
        $(document).ready( function() {
            $( "#tabs" ).tabs({
                // event: "mouseover",
                active: {{ session()->has('newFileAdded') ? ($item->song_id ? '2' : '1') : '0' }}
            });
        });

        @if (isset($song))
            $(function () {
                $('#fileuploadsheetmusic').fileupload({
                    dropZone: $('.show-sheetmusic-upload-form'),
                    type: 'POST',
                    dataType: 'json',
                    /* show progress */
                    progressall: function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        $('#upload-progress .bar').css(
                            'width',
                            progress + '%'
                        );
                    },
                    done: function (e, data) {
                        if (data.textStatus=='success') {
                            //TODO: show file in the UI
                            var file = JSON.parse(data.result.data);
                            console.log(file);
                            $('#upload-progress').html('<div>Success! File uploaded.</div>');
                            $('.nofile-attached').hide();
                            var showfile = document.createElement('div');
                            $(showfile).html('<span>File attached as <strong>' + file.filename + '</strong> File size: <i>' + file.filesize + ' bytes</i></span>');
                            var img = document.createElement('img');
                            $(img).addClass('mb-0 figure-img img-fluid img-rounded img-thumbnail');
                            $(img).attr('src', '/'+file.webpath+'/thumb-'+file.token);
                            var a = document.createElement('a');
                            $(a).attr('href', '/'+file.webpath+'/'+file.token);
                            $(a).append(img);
                            $('#sheet-tab').append(a);
                            $('#sheet-tab').append(showfile);
                        }
                        else
                            console.log(data);                  
                    },
                });
            });
        @endif

        // provide item data on the client side
        cSpot.item = {!! json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT ) !!};
    </script>



@stop