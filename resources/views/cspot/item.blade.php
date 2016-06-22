
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<?php Use Carbon\Carbon; ?>

@extends('layouts.main')

@section('title', "Create or Update Plan Item")

@section('plan', 'active')



@section('content')


    @include('layouts.flashing')

    @if (isset($item))
        {!! Form::model( $item, array(
            'route'  => array('cspot.items.update', $item->id), 
            'method' => 'put', 
            'id'     => 'inputForm',
            'class'  => 'form-horizontal',
            'files'  => true,
            )) !!}
    @else
        {!! Form::open(
            array(
                'action' => 'Cspot\ItemController@store',
                'id' => 'inputForm',
                'files'  => true,
            )
        ) !!}
    @endif

    {!! Form::hidden('seq_no', $seq_no) !!}
    {!! Form::hidden('plan_id', isset($plan) ? $plan->id : $item->plan_id ) !!}

    @if ( isset($beforeItem) )
        <!-- remember before which item this new item should be inserted -->
        {!! Form::hidden('beforeItem_id', $beforeItem->id) !!}
    @endif


    <!-- 
        header area 
    -->
    <div class="row" id=title-bar>

        @if (isset($item))

            <!-- title text -->
            <div class="col-md-6">

                <div class="pull-xs-right">
                    <!-- hide until changes are made   -->
                    @if( Auth::user()->ownsPlan($item->plan_id) )
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
                    Review Item No {{$seq_no}}
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
                                <a class="dropdown-item nowrap text-danger"  item="button" href="{{ url('cspot/items/'. $item->id .'/delete') }}">
                                    <i class="fa fa-trash" > </i>&nbsp; Delete this item!
                                </a>
                            @endif
                            <hr>
                            @foreach ($items as $menu_item)
                                <a class="dropdown-item nowrap {{ $item->id==$menu_item->id ? 'bg-info' : '' }}"
                                    onclick="showSpinner()" 
                                    href="{{ url('cspot/plans/'.$plan->id.'/items').'/'.$menu_item->id.'/edit' }}">
                                    <small class="hidden-xs-down">{{ $menu_item->seq_no }} &nbsp;</small> 
                                    @if ($menu_item->song_id && $menu_item->song->title)
                                        <i class="fa fa-music">&nbsp;</i><strong>{{ $menu_item->song->title }}</strong>
                                    @else
                                        {{ $menu_item->comment }}
                                    @endif
                                </a>
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
            <div class="col-md-6 text-xs-right nowrap">

                @if( Auth::user()->ownsPlan($item->plan_id) )
                    &nbsp;
                    <span class="save-buttons submit-button" onclick="showSpinner()" style="display: none;">
                        {!! Form::submit('Save changes'); !!}
                    </span>
                @endif
                @if ($item->updated_at)
                    <br>
                    <small class="hidden-sm-down">Last updated:
                        {{ Carbon::now()->diffForHumans( $item->updated_at, true ) }} ago
                    </small>
                @endif
            </div>

        @else
            <div class="row">
                <div class="col-md-6">

                    @if ( isset($beforeItem) )
                        <h2>Insert Item</h2>
                    @else
                        <h2>Add Item No {{ $seq_no }}.0</h2>
                    @endif
                    <h5>into the 
                        <a href="{{ url('cspot/plans/'.$plan->id)}}/edit">
                            Service plan for {{ $plan->date->formatLocalized('%A, %d %B %Y') }}
                        </a>
                    </h5>

                </div>

                <!-- 
                    See if user wants to add even more items to this plan 
                -->
                <div class="col-md-6 pull-md-right">
                    <span class="save-buttons submit-button" onclick="showSpinner()" style="display: none;">
                        {!! Form::submit('Save!'); !!}
                    </span>
                    <input type="hidden" name="moreItems" value="false">
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" value="Y" 
                            @if (isset($moreItems))
                                checked="CHECKED" 
                            @endif
                            name="moreItems">
                        Tick to add more items after this one
                      </label>
                    </div>
                </div>
            </div>
        @endif

    </div>


    <hr class="bg-info">


    <!-- 
        ITEM area 
    -->
    <div class="row">


        <!-- 
            show only if a song is already linked to this item 
        -->
        @if ( isset($item->song->id) && $item->song_id<>0 )


            {!! Form::hidden('song_id', $item->song_id) !!}


            <!-- 
                show song details 
            -->
            <div id="col-2-song" class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="card card-block text-xs-center p-b-1" style="padding-top: 0.5rem; ">

                    <div class="row song-details form-group">
                        <h5 class="card-title">
                            <i class="pull-xs-left fa fa-music"></i>
                            <i class="pull-xs-right fa fa-music"></i>
                            {{ $item->song->title ? $item->song->title : '' }}
                            @if ($item->song->title_2)
                                <br>({{ $item->song->title_2 }})
                            @endif
                        </h5>
                        @if ($item->song->book_ref)
                            <h6>{{ $item->song->book_ref }}</h6>
                        @endif
                    </div>

                    <div class="card-text song-details">

                        <h6>Musical Instructions (e.g. Key)</h6>
                        @if( Auth::user()->ownsPlan($item->plan_id) || Auth::user()->isMusician() )
                            <p class="full-width">{!! Form::text('key'); !!}</p>
                        @else
                            <p>{!! Form::text('key', $item->key, ['disabled'=>'disabled']); !!}</p>
                        @endif

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
                        <br>

                        <div class="row">
                            <div class="col-xs-12 col-sm-4 full-btn">
                                @if ($item->song->youtube_id)
                                    <a href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}" 
                                        target="new" class="fully-width btn btn-primary-outline btn-sm" 
                                          title="Play on YouTube" data-toggle="tooltip">
                                    <i class="red fa fa-youtube-play"></i>&nbsp;play</a>
                                @else
                                    <a href="#" class="fully-width btn btn-secondary-outline btn-sm disabled"
                                          title="Missing YouTube Video" data-toggle="tooltip">
                                    <i class="red fa fa-youtube-play"></i>&nbsp;play</a>
                                @endif
                            </div>
                            @if ( Auth::user()->ownsPlan($item->plan_id) )
                                <div class="col-xs-12 col-sm-4 full-btn">
                                    <a href="#" class="fully-width btn btn-primary-outline btn-sm" 
                                        onclick="showSongSearchInput(this, '.song-search')" 
                                        title="Select another song" data-toggle="tooltip"
                                    ><i class="fa fa-exchange"></i>&nbsp;change song</a>
                                </div>
                            @endif
                            @if (Auth::user()->isEditor() )
                                <div class="col-xs-12 col-sm-4 full-btn">
                                    <a href="#" class="fully-width btn btn-primary-outline btn-sm" accesskey="69" id="go-edit"
                                        onclick="showSpinner();location.href='{{ route('cspot.songs.edit', $item->song_id) }}'" 
                                          title="Edit details of this song" data-toggle="tooltip"
                                    ><i class="fa fa-edit"></i>&nbsp;edit song</a>
                                </div>
                            @endif
                        </div>
                        

                    </div>

                    <script>
                        $(document).ready( function() {
                            $('.song-search').hide();
                        });
                    </script>


                    <!-- show song search input field if requested -->
                    <div class="row form-group song-search">
                        To search for another song,

                        @include('cspot.snippets.song_search')

                    </div>

                </div>


                @include('cspot.snippets.comment_input')


            </div>

            <!-- 
                show song lyrics and/or chords 
            -->
            <div id="col-3-lyrics" class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div id="tabs">
                    <ul>
                        <li><a href="#lyrics-tab">Lyrics</a></li>
                        <li><a href="#chords-tab">Chords</a></li>
                        <li><a href="#sheet-tab">Sheet Music</a></li>
                    </ul>
                        <div id="lyrics-tab">
                            <span class="text-info">({{ $item->song->sequence ? 'Sequence: '.$item->song->sequence : 'No sequence pre-defined' }})</span>
                            <pre>{{ $item->song->lyrics }}</pre>
                        </div>
                        <div id="chords-tab">
                            <pre id="chords">{{ $item->song->chords }}</pre>
                        </div>
                        <div id="sheet-tab">
                            @foreach ($item->song->files as $file)
                                @if ($item->song->license=='PD' || Auth::user()->isMusician() )
                                    @include ('cspot.snippets.present_files')
                                @else
                                    <span>(copyrighted material)</span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>



        <!-- 
            no song linked to this item yet 
        -->
        @else
            <!-- show only for authorized users -->
            @if( Auth::user()->ownsPlan($plan->id) )
                
                <div id="col-2-file-add" style="display: none;" 
                    class="col-lg-6 col-md-12 col-sm-12 col-xs-12 m-b-1 dropzone">

                        {!! Form::label('file', 'Add a file (e.g. for announcements)', ['class' => 'x']); !!}
                            <small>(Max. Size: <?php echo ini_get("upload_max_filesize"); ?>)</small><br>
                        {!! Form::file('file'); !!}
                        <br>
                        {!! Form::label('file_category_id', 'Select a Category for this file') !!}
                        <select name="file_category_id" id="file_category_id">
                            <option selected="TRUE" value=" ">select ...</option>
                            @foreach ( DB::table('file_categories')->get() as $fcat)
                                <option value="{{ $fcat->id }}">{{ $fcat->name }}</option>
                            @endforeach                        
                        </select>&nbsp;

                </div>

                <!-- show currently attached files/images -->
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 m-b-1">
                    @if ( isset($item)  &&  $item->files )
                        <?php 
                            // make sure the files are sorted by seq no
                            $files  = $item->files->sortBy('seq_no')->all(); 
                            $fcount = count($files);
                            $key    = 0; // we can't use a $key in the foreach statement as it's a re-sorted collection!
                        ?>
                        <div class="center" style="max-width: 380px;">
                        @foreach ($files as $file)
                            <div id="file-{{ $file->id }}" style="padding=2px;{{ ($key % 2 == 1) ? 'background-color: #eee;' : 'background-color: #ddd;' }}">
                            <!-- <hr class="narrow hr-big"> -->
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
                            </div>
                            @if ( $fcount>1)
                                <div class="center pull-xs-right">Order:<br>{{ $file->seq_no }}</div>
                            @endif
                                @include ('cspot.snippets.show_files')
                            </div>
                            <?php $key++; ?>
                        @endforeach
                        </div>
                    @endif
                </div>

                <div id="col-2-song-search" class="col-lg-6 col-md-12 col-sm-12 col-xs-12 m-b-1"
                    @if ( isset($item)  &&  $item->comment) 
                        style="display: none;"
                    @endif
                    >
                    To search for a song,
                    @include('cspot.snippets.song_search')
                </div>
            @endif

            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 m-b-1">
                @include('cspot.snippets.comment_input')
            </div>

        @endif


        <!-- 
            show bible reference data for new items 
            or for existing items with no song linked 
        -->
        @if ( !isset($item) || (isset($item) && ! $item->song_id) )
            <div id="col-1-comment" class="col-xs-12">

                <div class="row form-group bg-grey">
                    
                    <!-- allow bible verses selection for authorized users -->
                    @if( Auth::user()->ownsPlan($plan->id) )
                    <div class="col-xs-12 full-width p-b-1 p-t-1">

                        @if ( isset($item) )
                            <a href="#" onclick="showSongSearchInput(this, '#col-2-song-search');">
                                <i class="fa fa-music"></i>&nbsp;Add Song</a> &nbsp; &nbsp;

                            <a href="#" onclick="$(this).hide();$('#col-2-file-add').show();">
                                <i class="fa fa-file"></i>&nbsp;Add new file</a> &nbsp; &nbsp;
                            <a href="{{ url('cspot/files').'?item_id='.$item->id }}" style="white-space: nowrap">
                                <i class="fa fa-file-picture-o"></i>&nbsp;Add&nbsp;existing&nbsp;file</a>
                        @else
                            <a href="#" onclick="$(this).hide();$('#col-2-file-add').show();">
                                <i class="fa fa-file"></i>&nbsp;Add new file</a> &nbsp; &nbsp;
                        @endif
                        <br>
                        <br>

                        <h6><i class="fa fa-book">&nbsp;</i>Add Bible Reference:</h6>

                        <select name="from-book" id="from-book" class="pull-xs-left" onchange="showNextSelect('from', 'chapter')">
                            <option selected="TRUE" value=" ">select book...</option>
                            @foreach ($bibleBooks->getArrayOfBooks() as $book)
                                <option value="{{ $book }}">{{ $book }}</option>
                            @endforeach                        
                        </select>&nbsp;

                        <span class="select-reference" style="display: none;">                    
                            ch.
                            <select name="from-chapter" id="from-chapter" style="display: none;" 
                                    onchange="showNextSelect('from', 'verse')">
                                <option selected="" value=" "> </option>
                            </select>
                            verse 
                            <select name="from-verse" id="from-verse" style="display: none;"
                                    onchange="showNextSelect('to', 'verse')">
                                <option selected="" value=" "> </option>
                            </select>
                            to 
                            <select name="to-verse" id="to-verse" style="display: none;">
                                <option selected="" value=" "> </option>
                            </select>
                        </span>
                    </div>
                    @endif

                    <!-- select bible version to be used -->
                    <div class="col-xs-12 select-version" style="display: none;">
                        {!! Form::label('version', 'Select version:'); !!}
                        <select name="version" id="version" onchange="populateComment()">
                            <option {{ isset($item) ? '' : 'selected' }}>
                            </option>
                            @foreach ($versionsEnum as $vers)
                                <option value="{{ $vers }}">{{ $vers }}
                                </option>
                            @endforeach
                        </select>
                    </div>  

                    <!-- show the selected bible verses -->
                    <div class="col-lg-6 col-sm-12" id="bible-passages">
                        @foreach ($bibleTexts as $btext)
                            <h5>{{ $btext->display }} ({{ $btext->version_abbreviation }})</h5>
                            <div>
                                {!! $btext->text !!}
                            </div>
                            <div class="small">
                                {!! $btext->copyright !!}
                            </div>
                            <hr>
                        @endforeach
                        <div id="waiting" style="display: none;"><i class="fa fa-spinner fa-spin"></i> leafing through the pages....</div>
                    </div>

                </div>
            </div>
        @endif 

    </div>


    {!! Form::close() !!}


    @if (! isset($item))
        <script>
            // set focus on main input field (only when adding a new item)
            document.forms.inputForm.search.focus();
            // add class in order to identify main input field later after flash messages
            document.forms.inputForm.search.setAttribute('class', 'main-input');
        </script>
    @endif

    
@stop