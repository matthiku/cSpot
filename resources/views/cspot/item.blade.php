
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<?php Use Carbon\Carbon; ?>

@extends('layouts.main')

@section('title', "Create or Update Plan Item")

@section('plan', 'active')



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


            <div class="pull-xs-right">

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
                            @if (! $menu_item->forLeadersEyesOnly)
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
                <a href="{{ url('cspot/plans/'.$plan->id)}}/edit">
                    Service plan for {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</a>
            </h5>
            <h4 class="hidden-lg-up">
                in
                <a href="{{ url('cspot/plans/'.$plan->id)}}/edit">
                    plan for {{ $plan->date->formatLocalized('%a, %d %b') }}</a>
            </h4>

        </div>


        <!-- action buttons -->
        <div    class="col-md-6 text-xs-right nowrap"
                data-item-id="{{ $item->id }}" 
                data-item-update-action="{{ route('cspot.api.items.update', $item->id) }}">

            @if( Auth::user()->ownsPlan($item->plan_id) )
                &nbsp;
                <span class="save-buttons submit-button" onclick="showSpinner()" style="display: none;">
                    {{-- {!! Form::submit('Save changes'); !!} --}}
                </span>

                {{-- is this item for leader's eyes only? --}}
                <a      href="#" class="hidden-sm-down link" onclick="changeForLeadersEyesOnly(this)" 
                        data-value="{{ $item->forLeadersEyesOnly }}"
                        title="Make item visible for {{ $item->forLeadersEyesOnly ? 'everyone': "leader's eyes only (useful for personal notes etc.)" }}">
                    @if ($item->forLeadersEyesOnly)
                        <i class="fa fa-eye-slash"></i>
                    @else
                        <i class="fa fa-eye"></i>
                    @endif
                    <small style="display: {{ $item->forLeadersEyesOnly ? 'initial' : 'none' }}">(for your eyes only)</small>
                    <small style="display: {{ $item->forLeadersEyesOnly ? 'none' : 'initial' }}">(item visible to all)</small>
                </a>
            @endif

            @if ($item->updated_at)
                <br>
                <small class="hidden-sm-down">Last updated:
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

            <li><a href="#bg-images-tab"><span class="hidden-sm-down">Background </span>Images
                <small class="text-muted">({{ $item->files->count() }})</small>
            </a></li>

            @if ( $item->song_id )
                <li><a href="#lyrics-tab">{{ $item->itemType()=='song' ? 'Lyrics' : ucfirst($item->itemType()).'(s)' }}
                    <small class="text-muted">{!! $item->song->lyrics ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' !!}</small>
                </a></li>
                @if ( $item->itemType()=='song')
                    <li><a href="#chords-tab">Chords
                        <small class="text-muted">{!! $item->song->chords ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' !!}</small>
                    </a></li>
                    <li><a href="#sheet-tab">Sheet Music
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

                <div class="card card-block text-xs-center p-b-1" style="max-width: 40rem; ">

                    <div class="row song-details form-group">
                        <h5 class="card-title">
                            @if ( $item->itemType()=='song')
                                <i class="pull-xs-left fa fa-music"></i>
                                <i class="pull-xs-right fa fa-music"></i>
                            @endif
                            {{ $item->song->title ? $item->song->title : '' }}
                            @if ( $item->itemType()=='song' && $item->song->title_2)
                                <br>({{ $item->song->title_2 }})
                            @endif
                        </h5>
                        @if ($item->song->book_ref)
                            <h6>{{ $item->song->book_ref }}</h6>
                        @endif
                    </div>

                    <div class="card-text song-details">

                        @if ( $item->itemType()=='song')
                            <div class="row">
                                Note: 
                                @if ( $usageCount )
                                    Song was used before in <strong>{{ $usageCount }}</strong> service(s) -
                                    lastly <strong title="{{ $newestUsage->date }}">
                                        {{ Carbon::now()->diffForHumans( $newestUsage->date, true ) }} ago</strong>
                                @else
                                    Song was never used before in a service
                                @endif
                            </div>
                        @else
                            <span class="btn btn-secondary m-b-1">
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

                        <div class="row m-b-1">                            
                            <div class="col-sm-12 col-md-3 full-btn">
                                @if ($item->song->youtube_id)
                                    <a href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}" 
                                        target="new" class="fully-width btn btn-outline-primary btn-sm">
                                    <i class="red fa fa-youtube-play"></i><br><small>play<span class="hidden-lg-down"> on YouTube</span></small></a>
                                @else
                                    <a href="#" class="fully-width btn btn-outline-secondary btn-sm disabled"
                                          title="Missing YouTube Video" data-toggle="tooltip">
                                    <i class="red fa fa-youtube-play"></i><br><small>play</small></a>
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
                                    <img src="{{ url('/') }}/images/songselectlogo.png" width="14"><br><small>CCLI N<sup>o</sup>. missing!</small></a>
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

            {{ $item->files->count() ? '' : '(no images attached yet)' }}

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

                            <div class="pull-xs-left" style="min-width: 60px;">
                                @if ( $fcount>1 && $key>0 )
                                    <a href="{{ url("cspot/items/$item->id/movefile/$file->id/up") }}" title="Move up" 
                                        onclick="showSpinner()" class="btn btn-info btn-sm move-button m-b-1" role="button" >
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
                                <div class="center pull-xs-right">Order:<br>{{ $file->pivot->seq_no }}</div>
                            @endif

                            @include ('cspot.snippets.show_files')

                        </div>
                        <?php $key++; ?>

                    @endforeach

                </div>
                <br>
                
                {{-- link to open the Add File dialog --}}
                <a href="#" onclick="$(this).hide();$('#col-2-file-add').show();" id="add-another-image-link" data-item-type="add-file">
                    <i class="fa fa-file"></i>&nbsp;Add another image</a> &nbsp; &nbsp;

                {{-- Form to add new (image) file --}}
                <div id="col-2-file-add" style="display: none;" class="m-b-1 dropzone">
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

                @if ($item->itemType()=='song')
                    <span class="text-info">
                        @if (Auth::user()->isEditor())
                            Sequence:
                            <span id="sequence-song-id-{{ $item->song->id }}" class="editable-song-field">{{ $item->song->sequence }}</span>
                            <i class="fa fa-pencil text-muted"> </i>
                        @else
                            ({{ $item->song->sequence ? 'Sequence: '.$item->song->sequence : 'No sequence predefined' }})
                        @endif
                    </span>
                @endif

                @if ($item->itemType()=='video')
                    <small>(possible time parameter was ignored!)</small>
                    <br>
                    <iframe width="560" height="315" 
                        src="https://www.youtube.com/embed/{{ strpos($item->song->youtube_id,'&')!= false ? explode('&', $item->song->youtube_id)[0] : $item->song->youtube_id }}" 
                        frameborder="0" allowfullscreen>                                    
                    </iframe>
                @endif

                <pre id="lyrics-song-id-{{ $item->song->id }}" {{ (Auth::user()->isEditor()) ? 'class=edit_area' : '' }}>{{ $item->song->lyrics }}</pre>

                <small class="text-muted">(click to edit!)</small>
            </div>


            @if ( $item->itemType()=='song')

                <div id="chords-tab">
                    <pre id="chords-song-id-{{ $item->song->id }}" class="{{ (Auth::user()->isEditor()) ? 'edit_area' : '' }} show-chords">{{ $item->song->chords }}</pre>
                </div>
                

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

    {{-- activate the tabs --}}
    <script>

        cSpot.item = {!! json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT ) !!};

        $( function() {
            $( "#tabs" ).tabs({
                event: "mouseover",
                active: {{ session()->has('newFileAdded') ? ($item->song_id ? '2' : '1') : '0' }}
            });
        });
    </script>

    {!! Form::close() !!}



@stop