
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Present Plan")

@section('plan', 'active')



@section('content')





    <!-- ================================================================================ -->
    <div id="main-content" class="bg-inverse">



        @if ( $item->song_id )


            @if ($item->song->lyrics )

                <div style="position: relative; width: 100%; height: 100%;">

                    @if ($item->files->count())
                        <?php // make sure the files are sorted by seq no
                            $files  = $item->files->sortBy('seq_no')->all(); 
                            $key    = 1; // we can't use a $key in the foreach statement as it's a re-sorted collection!
                        ?>            
                        @foreach ($files as $file)
                            <img class="slide-background-image song-background-image m-b-2" data-slides-id="{{ $key }}" style="display: none;" 
                                   src="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">
                            <?php $key++; ?>
                        @endforeach
                    @endif

                    <div class="text-present m-b-3 lyrics-parts" id="lyrics-title"
                         style="display: none; position: absolute; left: auto; top: 0px; width: 100%;">
                        {{ $item->song->title }}
                        {!! ($item->song->title_2 && $item->song->title_2 != 'video' && $item->song->title_2 != 'infoscreen') ? '<br>('.$item->song->title_2.')' : '' !!}
                    </div>

                    {{-- insert videoclip or lyrics --}}
                    @if ($item->song->title_2=='video')

                        <div class="hidden-xs-up" id="videoclip-url">{{ $item->song->title_2}}</div>
                        <div class="text-present m-b-3" id="present-lyrics">
                            <iframe width="560" height="315" src="https://www.youtube.com/embed/{{ $item->song->youtube_id }}" frameborder="0" allowfullscreen></iframe>
                        </div>

                    @else 

                        <div class="text-present m-b-3"
                             style="display: none; position: absolute; left: auto; top: 0px; width: 100%;"
                                id="present-lyrics">{{ $item->song->lyrics }}
                        </div>

                    @endif

                </div>

                <div class="hidden-xs-up" id="sequence">{{ $item->song->sequence }}</div>

            @endif





        @elseif ($item->files->count())
        
            {{-- prepare div as background for overlaying the comment text --}}
            <div style="position: relative; width: 100%; height: 100%;">
                <?php // make sure the files are sorted by seq no
                    $files  = $item->files->sortBy('seq_no')->all(); 
                    $key    = 1; // we can't use a $key in the foreach statement as it's a re-sorted collection!
                ?>            
                @foreach ($files as $file)
                    <img class="slide-background-image  {{ $bibleTexts ? 'song-background-image' : '' }}  m-b-2" data-slides-id="{{ $key }}" style="display: none;" 
                           src="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">
                    <?php $key++; ?>
                @endforeach

                {{-- show comment text as overlaying the background image --}}
                @if ($item->show_comment)
                    <div class="text-present"
                         style="position: absolute; left: auto; top: 0px; width: 100%;"
                        >{{ $item->comment }}</div>
                @endif

                @if ($bibleTexts)
                    <div class="bible-text-present" id="bible-text-present-all" 
                         style="position: absolute; left: auto; top: 0px; width: 100%; display: none;" >
                        @foreach ($bibleTexts as $btext)
                            <p class="item-comment" id="item-comment" style="display: none;" >{{ $item->comment }}</p>
                            <p class="bible-text-present-ref" style="display: none;" >{{ $btext->display }}</p>
                            <h1>{{ $btext->display }}</h1> 
                            <div class="bible-text-present" style="display: none;" >{!! $btext->text !!}</div>
                            <!-- {!! $btext->copyright !!} -->
                            <hr>
                        @endforeach
                    </div>
                @endif

            </div>



        @elseif ( $item->show_comment )

            <pre class="text-present">{{ $item->comment }}</pre>



        @elseif ( $bibleTexts )

            <div class="bible-text-present" id="bible-text-present-all" style="display: none;" >
                @foreach ($bibleTexts as $btext)
                    <p class="item-comment" id="item-comment" style="display: none;" >{{ $item->comment }}</p>
                    <p class="bible-text-present-ref" style="display: none;" >{{ $btext->display }}</p>
                    <h1>{{ $btext->display }}</h1> 
                    <div class="bible-text-present" style="display: none;" >{!! $btext->text !!}</div>
                    <!-- {!! $btext->copyright !!} -->
                    <hr>
                @endforeach
            </div>


        @elseif ( $item->key=='announcements' )

            @include('cspot.snippets.announcements')

        @endif



    </div>
    <!-- ================================================================================ -->


    <?php 
    $modalTitle = 'Use your keyboard!';
    $modalContent = '
        <p><kbd>Esc</kbd> &nbsp; go back to plan overview</p>
        <p><kbd> space bar </i></kbd> &nbsp; go to next slide or plan item (or left mouse click)</p>
        <p><kbd> <i class="fa fa-arrow-right"> </i></kbd> &nbsp; go to next slide or plan item (or left mouse click)</p>
        <p><kbd> <i class="fa fa-arrow-left"> </i></kbd> &nbsp; go to previous slide or plan item (or right mosue click)</p>
        <p><kbd>1</kbd>, <kbd>2</kbd>, <kbd>3</kbd> &nbsp; ... jump to verse 1...n</p>
        <p><kbd>c</kbd> &nbsp; jump to chorus</p>
        <p><kbd>b</kbd> &nbsp; jump to bridge</p>
        <p><kbd>PgDn</kbd> &nbsp; go to next item (skip remaining slides)</p>
        <p><kbd>PgUp</kbd> &nbsp; go to previous item (skip remaining slides)</p>
        <p><kbd>+</kbd> &nbsp; increase font size</p>
        <p><kbd>+</kbd> &nbsp; decrease font size</p>
        On <strong class="bg-warning">tablets</strong> or <strong class="bg-warning">phones</strong>, you should instead use the buttons provided at the bottom of this screen!'; 
    ?>

    @include( 'cspot/snippets/modal', ['modalContent' => $modalContent, 'modalTitle' => $modalTitle ] )



    <nav class="navbar navbar-fixed-bottom navbar-dark bg-black center" id="bottom-fixed-navbar" style="padding: 0;">


        <ul class="nav navbar-nav">

            <li id="lyrics-parts-indicators">
                <!-- potential buttons for lyric parts (verses, chorusses etc) -->
                <a href="#" onclick="lyricsShow('start-lyrics');" 
                    title="show start lyrics" id="btn-show-start-lyrics" style="display: none;" 
                    class="nav-item btn btn-sm btn-outline-info lyrics-show-btns" role="button">S</a>

                <a href="#" onclick="lyricsShow('verse1');" 
                    title="show verse 1" id="btn-show-verse1" style="display: none;" 
                    class="nav-item btn btn-sm btn-outline-info lyrics-show-btns" role="button">1</a>                
                <a href="#" onclick="lyricsShow('verse2');" 
                    title="show verse 2" id="btn-show-verse2" style="display: none;" 
                    class="nav-item btn btn-sm btn-outline-info lyrics-show-btns" role="button">2</a>                
                <a href="#" onclick="lyricsShow('verse3');" 
                    title="show verse 3" id="btn-show-verse3" style="display: none;" 
                    class="nav-item btn btn-sm btn-outline-info lyrics-show-btns" role="button">3</a>                
                <a href="#" onclick="lyricsShow('verse4');" 
                    title="show verse 4" id="btn-show-verse4" style="display: none;" 
                    class="nav-item btn btn-sm btn-outline-info lyrics-show-btns" role="button">4</a>                
                <a href="#" onclick="lyricsShow('verse5');" 
                    title="show verse 5" id="btn-show-verse5" style="display: none;" 
                    class="nav-item btn btn-sm btn-outline-info lyrics-show-btns" role="button">5</a>
                <a href="#" onclick="lyricsShow('verse6');" 
                    title="show verse 6" id="btn-show-verse6" style="display: none;" 
                    class="nav-item btn btn-sm btn-outline-info lyrics-show-btns" role="button">6</a>                
                <a href="#" onclick="lyricsShow('verse7');" 
                    title="show verse 7" id="btn-show-verse7" style="display: none;" 
                    class="nav-item btn btn-sm btn-outline-info lyrics-show-btns" role="button">7</a>   

                <a href="#" onclick="lyricsShow('prechorus');" 
                    title="show pre-chorus" id="btn-show-prechorus" style="display: none;" 
                    class="nav-item btn btn-sm btn-outline-info lyrics-show-btns" role="button">P</a>
                <a href="#" onclick="lyricsShow('chorus1');" 
                    title="show chorus" id="btn-show-chorus1" style="display: none;" 
                    class="nav-item btn btn-sm btn-outline-info lyrics-show-btns" role="button">Ch</a>
                <a href="#" onclick="lyricsShow('chorus2');" 
                    title="show chorus 2" id="btn-show-chorus2" style="display: none;" 
                    class="nav-item btn btn-sm btn-outline-info lyrics-show-btns" role="button">Ch2</a>

                <a href="#" onclick="lyricsShow('bridge');" 
                    title="show bridge" id="btn-show-bridge" style="display: none;" 
                    class="nav-item btn btn-sm btn-outline-info lyrics-show-btns" role="button">B</a>

                <a href="#" onclick="lyricsShow('ending');" 
                    title="show ending" id="btn-show-ending" style="display: none;" 
                    class="nav-item btn btn-sm btn-outline-info lyrics-show-btns" role="button">E</a>
            </li>
        </ul>


        <!-- jump to next plan item -->
        <ul class="nav navbar-nav pull-xs-right">
            <li>
                <a 
                    @if ($item->id == $item->plan->lastItem()->id)
                        href="#" disabled="disabled"
                    @else
                        href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/next/present') }}"
                    @endif
                    class="nav-item btn btn-sm btn-warning" role="button" id="go-next-item">
                    <i class="fa fa-angle-double-right fa-lg"></i>
                </a>
            </li>
        </ul>


        {{-- 'sequence' indicates the order in which the various lyric parts are to be shown --}}
        <span class="navbar-nav pull-xs-right hidden-xs-down text-success" id="lyrics-sequence-nav">
            <!-- {{-- this is currently resolved on the client side --}} -->
            @if ($item->song_id && $item->song->sequence)
                <a href="#" onclick="advancePresentation();" 
                    title="show next slide" id="btn-show-next" 
                    class="nav-item btn btn-sm btn-info" role="button"><i class="fa fa-chevron-right fa-lg"></i></a>
            @endif
        </span>


        {{-- show song title or comment in first navbar on bigger screens only --}}
        <span class="nav navbar-nav center hidden-sm-down" id="item-navbar-label">
            <small class="hidden-md-down text-muted">Item {{$item->seq_no}} -</small>
            <span class="text-success hidden-md-down limited-width">
                {{ ($item->song_id && $item->song->title) ? $item->song->title : $item->comment }}
            </span>
            <span class="text-success hidden-lg-up limited-width">
                {{ substr(($item->song_id && $item->song->title) ? $item->song->title : $item->comment, 0, 20) }}
            </span>
        </span>

    
        {{-- button to reveal the second navbar at the bottom --}}
        <button class="navbar-toggler presentation-navbar-toggler btn btn-info active" 
                 type="button" data-toggle="collapse" data-target="#lyricsNavbar">
            &dArr;
        </button>

        <button class="btn btn-sm btn-outline-secondary" type="button" onclick="showBlankScreen()">
            <span class="hidden-sm-down text-muted">Blank</span><i class="fa fa-tv hidden-md-up"></i>
        </button>     


        {{-- what's coming next? (Show unless we are on the last item!) --}}
        <span class="nav navbar-nav">
            @if ($item->id != $item->plan->lastItem()->id)
                <small class="hidden-lg-up hidden-xs-down text-muted limited-width">(next: {{ substr(getItemTitle($item),0,15) }})</small>
                <small class="hidden-md-down text-muted limited-width">(up next: {{ getItemTitle($item) }})</small>
            @endif
        </span>
        



        <!-- 
            the second navbar is hidden at first 
        -->

        <div class="collapse navbar-toggleable" id="lyricsNavbar">

            <!-- show song title or comment in second navbar on smaller screens only -->
            <span class="nav navbar-nav center hidden-md-up hidden-xs-down">
                <small class="hidden-md-down text-muted">Item {{$item->seq_no}} -</small>
                <span style="max-width: 250px">
                    {{ ($item->song_id && $item->song->title)  ?  $item->song->title  :  $item->comment }}
                </span>
            </span>

            
            <!-- 
                DropUP Menu Button
            -->
            <div id="popup-goto-menu" class="btn-group dropup center">

                <button type="button" class="btn btn-sm btn-info dropdown-toggle" 
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Go to
                </button>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-presentation">
                    @foreach ($items as $menu_item)
                        @if (! $menu_item->forLeadersEyesOnly)
                            <a class="dropdown-item nowrap{{ $item->id == $menu_item->id ? ' bg-info' : '' }}"
                                id="menu-item-seq-no-{{ $menu_item->seq_no }}"
                                href="{{ url('cspot/items/').'/'.$menu_item->id.'/present' }}">
                                <small class="hidden-md-down">{{ $menu_item->seq_no }}</small> &nbsp; 
                                @if ($menu_item->song_id && $menu_item->song->title)
                                    {!! $menu_item->song->title_2=='infoscreen'
                                        ? $menu_item->song->title
                                        : '<i class="fa fa-music">&nbsp;</i><strong>'.$menu_item->song->title.'</strong>' 
                                    !!}                                    
                                @else
                                    {{ substr($menu_item->comment, 0, 45) }}
                                @endif
                                <sup id="in-cache-seq-no-{{ $menu_item->seq_no }}" style="display: none">*</sup>
                            </a>
                        @endif
                    @endforeach
                    @if (Auth::user()->ownsPlan($item->plan_id))
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" id="go-edit"
                            href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/edit') }}">
                            <i class="fa fa-pencil"></i>
                            Edit this item
                        </a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" id="go-back"
                        href="{{ url('cspot/plans/'.$item->plan_id) }}">
                        <i class="fa fa-undo"></i>
                        Back to plan overview
                        <small class="pull-xs-right">(* = item in local cache)</small>
                    </a>
                </div>

            </div>


            <!-- 
                Personal Notes 
            -->
            <div class="btn-group dropup hidden-xs-down m-l-1">
                <button    type="button" title="Your Private Notes"
                          class="btn btn-sm btn{{ $item->itemNotes->where('user_id', Auth::user()->id)->first() ? '' : '-outline' }}-success dropdown-toggle"                     
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-sticky-note-o fa-lg"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-right bg-faded">

                    <h6 class="dropdown-header">Your Private Notes</h6>

                    <pre id="notes-item-id-{{ $item->id }}" class="editable-item-field-present center">{{ 
                        $item->itemNotes->where('user_id', Auth::user()->id)->first() ? $item->itemNotes->where('user_id', Auth::user()->id)->first()->text : '' }}</pre>

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item disabled" href="#">(Click to edit)</a>

                </div>

            </div>


            <!-- 
                Add New Item into Plan! 
            -->
            @if (Auth::user()->ownsPlan($item->plan_id))
                <div class="btn-group dropup hidden-xs-down m-l-1">
                    {{-- new MODAL POPUP to add song, scripture or comment --}}
                    <button type="button" class="btn btn-sm btn-outline-info btn-sm" title="Add New Item (Song)" 
                         data-toggle="modal" data-target="#searchSongModal"
                        data-plan-id="{{$item->plan_id}}" data-item-id="{{$item->id}}" 
                         data-seq-no="after-{{ $item->seq_no }}"
                               title="Select new Song, Scripture or Comment">
                        <i class="fa fa-plus"></i> song etc.
                    </button>
                </div>
            @endif


            {{-- link to show linked YT video --}}
            @if ($item->song_id && $item->song->youtube_id)
                <a href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}" 
                    target="new" class="pull-xs-right btn btn-sm btn-info hidden-sm-down m-l-1">
                <i class="red fa fa-youtube-play fa-lg"></i>&nbsp;</a>
            @endif

            {{-- help button to show modal --}}
            <a href="#" title="show keyboard shortcuts" data-toggle="modal" data-target=".help-modal"
                class="hidden-sm-down pull-xs-right btn btn-sm btn-outline-success">
            <i class="fa fa-question-circle fa-lg"></i></a>




            {{-- 
                    go to first/last slide
            --}}
            <a href="{{ url('cspot/items/').'/'.$item->plan->firstItem()->id.'/present' }}" id="go-first-item"></a>
            <a href="{{ url('cspot/items/').'/'.$item->plan->lastItem()->id.'/present'  }}" id="go-last-item" ></a>



            <ul class="nav navbar-nav pull-xs-left">
                <li>
                    <a 
                        @if ($item->id == $item->plan->firstItem()->id)
                            href="#" disabled="disabled"
                        @else
                            href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/previous/present') }}"
                        @endif                        
                        class="nav-item btn btn-sm btn-warning" role="button" id="go-previous-item">
                        <i class="fa fa-angle-double-left fa-lg"></i>
                    </a> 
                    <a href="#" title="decrease font size" id="decr-font"
                        onclick="changeFontSize([
                            '.announce-text-present', '.text-present', '.bible-text-present', '.bible-text-present>h1', '.bible-text-present>p'
                                ], 'decrease');"                             
                            class="nav-item btn btn-sm btn-info" role="button">
                        A <i class="fa fa-minus fa-lg"></i>
                    </a>
                    <a href="#" title="increase font size" id="incr-font"
                        onclick="changeFontSize([
                            '.announce-text-present', '.text-present', '.bible-text-present', '.bible-text-present>h1', '.bible-text-present>p'
                            ]);"                             
                            class="m-l-0 nav-item btn btn-sm btn-info" role="button">
                        A <i class="fa fa-plus fa-lg"></i>
                    </a>
                </li>
            </ul>

            <div class="nav-item dropup pull-xs-left">
                <button class="nav-link m-l-1 btn btn-sm btn-info dropdown-toggle" href="#" type="button" 
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Align</button>
                <div class="dropdown-menu bg-info">
                    <a onclick="changeTextAlign(['.announce-text-present', '.text-present', '.bible-text-present'], 'left');" 
                        class="dropdown-item" href="#"><i class="fa fa-align-left fa-lg"></i> Left</a>
                    <a onclick="changeTextAlign(['.announce-text-present', '.text-present', '.bible-text-present'], 'right');"
                        class="dropdown-item" href="#"><i class="fa fa-align-right fa-lg"></i> Right</a>
                    <a onclick="changeTextAlign(['.announce-text-present', '.text-present', '.bible-text-present'], 'center');"
                        class="dropdown-item" href="#"><i class="fa fa-align-center fa-lg"></i> Center</a>
                </div>
            </div>

            {{-- configuration menu --}}
            <div class="nav-item btn-group dropup pull-xs-left m-l-1">

                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                     id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="text-muted hidden-sm-down">Config </span><i class="fa fa-cog"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-right dropdown-menu-presentation" aria-labelledby="dropdownMenuButton">

                    <h6 class="dropdown-header">Show Configuration</h6>

                    <a      href="#" class="dropdown-item" onclick="changeBlankSlidesConfig()" 
                            title="Show empty slides between plan items">
                        <i id="configBlankSlidesItem" class="fa fa-square-o">&nbsp;</i>insert blank slides between items?</a>

                    <a      href="#" class="dropdown-item" onclick="changeOfflineModeConfig()" 
                            title="Work off-line and get slides from local storage instead of from the server">
                        <i id="configOfflineModeItem" class="fa fa-square-o">&nbsp;</i>use locally cached slides?</a>

                    <a      href="#" class="dropdown-item small" onclick="clearLocalCache();"
                            title="delete all locally cached items">
                        <i class="fa fa-trash-o red"></i>&nbsp;</i>delete all locally cached slides</a>


                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">Synchronisation Settings</h6>

                    <a      href="#" class="dropdown-item{{ env('PRESENTATION_ENABLE_SYNC', 'false') ? '' : ' disabled' }}" 
                            onclick="changeSyncPresentation()" title="Synchronise this presentation with Main Presenter">
                        <i id="syncPresentationIndicator" class="fa fa-square-o">&nbsp;</i>Sync presentation?
                        <span class="small">&nbsp;with:</span>
                        <span class="small showPresenterName"> ({{ $serverSideMainPresenter ? $serverSideMainPresenter['name'] : 'none' }})</span>
                    </a>

                    <a      href="#" class="dropdown-item{{ env('PRESENTATION_ENABLE_SYNC', 'false') ? '' : ' disabled' }}" 
                            onclick="changeMainPresenter()" title="Become Main Presenter controlling other presentations">
                        <i id="setMainPresenterItem" class="fa fa-square-o">&nbsp;</i>Be Main Presenter?
                        <span class="small showPresenterName"> ({{ $serverSideMainPresenter ? $serverSideMainPresenter['name'] : 'none' }})</span>
                    </a>

                </div>
            </div>



        </div>
    </nav>



    {{-- 
            provide popup to add/insert new item 
    --}}
    @include('cspot.snippets.add_item_modal')



    <script>        
        // make type of presentation globally available
        cSpot.presentation.type = 'lyrics';

        {{-- load cached items from server - if there are any --}}
        @if ($item->plan->has('planCaches'))
            loadCachedPresentation({{ $item->plan->id }});
        @endif

    </script>


@stop
