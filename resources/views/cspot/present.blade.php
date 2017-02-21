
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Present Plan")

@section('plans', 'active')



@section('content')




    {{-- 
            provide popup to add/insert new item 
    --}}
    {{-- modal will be re-positioned to the bottom in document.ready.js! --}}
    @include('cspot.snippets.add_item_modal')




    <!-- ================================================================================ -->
    <div id="main-content" class="main-lyrics-presentation" style="background-color: #373a3c;">



        {{-- ========================== SONG  or videoclip or slides ====================
        --}}
        @if ( $item->song_id )


            @if ($item->song->title )

                <div style="position: relative; width: 100%; height: 100%;">

                    @if ($item->files->count())
                        <?php // make sure the files are sorted by seq no
                            $files  = $item->files->sortBy('seq_no')->all(); 
                            $key    = 1; // we can't use a $key in the foreach statement as it's a re-sorted collection!
                        ?>            
                        @foreach ($files as $file)
                            <img class="slide-background-image song-background-image mb-2" data-slides-id="{{ $key }}" style="display: none;" 
                                   src="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">
                            <?php $key++; ?>
                        @endforeach
                    @endif

                    {{-- show song title --}}
                    @if (! $item->hideTitle)
                        <div class="text-present mb-3 lyrics-parts" id="lyrics-title"
                             style="display: none; position: absolute; left: auto; top: 0px; width: 100%; font-style: italic;">
                            {{ $item->song->title }}
                            <span style="font-size: 90%">
                                {!! ($item->song->title_2 && $item->song->title_2 != 'video' && $item->song->title_2 != 'slides') ? '<br>('.$item->song->title_2.')' : '' !!}
                            </span>
                        </div>
                    @endif

                    {{-- insert videoclip or lyrics --}}
                    @if ($item->song->title_2=='video')

                        <div class="hidden-xs-up" id="videoclip-url">{{ $item->song->title_2}}</div>
                        <div class="text-present mb-3 pt-1" id="present-lyrics">
                            <iframe width="560" height="315" src="https://www.youtube.com/embed/{{ $item->song->youtube_id }}" frameborder="0" allowfullscreen></iframe>
                        </div>

                    @elseif ($item->song->onsongs->count() ) 

                        <div class="text-present mb-3" id="present-lyrics"
                             style="display: none; position: absolute; left: auto; top: 0px; width: 100%;"
                             >{{ $item->song->onSongLyrics() }}</div>
                    @else

                        <div class="text-present mb-3" id="present-lyrics"
                             style="display: none; position: absolute; left: auto; top: 0px; width: 100%;"
                             >{{ $item->song->lyrics }}
                        </div>

                    @endif

                </div>

                <div class="hidden-xs-up" id="sequence">{{ $item->song->sequence }}</div>

            @endif





        {{-- ========================== Images ====================
        --}}
        @elseif ($item->files->count())
        
            {{-- prepare div as background for overlaying the comment text --}}
            <div style="position: relative; width: 100%; height: 100%;">
                <?php // make sure the files are sorted by seq no
                    $files  = $item->files->sortBy('seq_no')->all(); 
                    $key    = 1; // we can't use a $key in the foreach statement as it's a re-sorted collection!
                ?>            
                @foreach ($files as $file)
                    <img class="slide-background-image hidden{{ ($bibleTexts || strtolower($file->file_category->name) != 'presentation') ? ' song-background-image ' : ' ' }}mb-2" 
                        data-slides-id="{{ $key }}"
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



        {{-- ========================== Comments/Notes ====================
        --}}
        @elseif ( $item->show_comment )

            <pre class="text-present">{{ $item->comment }}</pre>



        {{-- ========================== Bibletexts ====================
        --}}
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


        {{-- ========================== Announcements Slide ====================
        --}}
        @elseif ( $item->key=='announcements' )

            @include('cspot.snippets.announcements')

        @endif



    </div>
    {{-- ================================================================================ --}}


    @php 
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
        <p><kbd>-</kbd> &nbsp; decrease font size</p>
        <p><kbd>.</kbd> &nbsp; go to next bible verse (if not included in the initial reference!)</p>
        On <strong class="bg-warning"> tablets </strong> or <strong class="bg-warning"> phones </strong>, you should instead use the buttons provided at the bottom of this screen!'; 
    @endphp

    @include( 'cspot/snippets/modal', ['modalContent' => $modalContent, 'modalTitle' => $modalTitle ] )





    <!-- 
        the second navbar is hidden at first 
    -->
    <div class="collapse bg-black fixed-bottom justify-content-between pb-4 mb-1" id="lyricsNavbar">


        <div>            
            {{-- go previous and change font size 
            --}}
            <div class="btn-group">
                <a 
                    @if ($item->id == $item->plan->firstItem()->id)
                        href="#" disabled="disabled"
                    @else
                        href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/previous/present') }}"
                    @endif                        
                    class="nav-item nav-link btn btn-sm btn-warning" role="button" id="go-previous-item">
                    <i class="fa fa-angle-double-left fa-lg"></i>
                </a>
            </div>
            <div class="btn-group">
                <a href="#" title="decrease font size" id="decr-font"
                    onclick="changeFontSize([
                        '.announce-text-present', '.text-present', '.bible-text-present', '.bible-text-present>h1', '.bible-text-present>p'
                            ], 'decrease');"                             
                        class="nav-item btn btn-sm btn-info" role="button">
                    A <i class="fa fa-minus fa-lg"></i>
                </a>
            </div>
            <div class="btn-group">
                <a href="#" title="increase font size" id="incr-font"
                    onclick="changeFontSize([
                        '.announce-text-present', '.text-present', '.bible-text-present', '.bible-text-present>h1', '.bible-text-present>p'
                        ]);"                             
                        class="ml-0 nav-item btn btn-sm btn-info" role="button">
                    A <i class="fa fa-plus fa-lg"></i>
                </a>
            </div>

            {{-- text alignment 
            --}}
            <div class="btn-group">
                <div class="nav-item dropup">
                    <button class="nav-link ml-1 py-1 btn btn-sm btn-info dropdown-toggle" href="#" type="button" 
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
            </div>

            {{-- change text color of lyrics presentation 
            --}}
            <div class="btn-group ml-1">
                <button class="btn btn-sm btn-info narrow">
                    Text: 
                    <input onchange="changeColor(['.announce-text-present', '.text-present', '.bible-text-present'], this.value);" type='text' class="colorPicker" />
                </button class="btn btn-sm btn-info">
            </div>

            {{-- change BG color of lyrics presentation 
            --}}
            <div class="btn-group ml-1">
                <button class="btn btn-sm btn-info narrow">
                    BG: 
                    <input onchange="changeColor(['.main-lyrics-presentation', 'body'], this.value, 'BG');" type='text' class="BGcolorPicker" />
                </button class="btn btn-sm btn-info">
            </div>

            {{-- configuration menu 
            --}}
            <div class="btn-group dropup">

                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                     id="presentConfigDropUpMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="text-muted hidden-sm-down">Config </span><i class="fa fa-cog"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-right dropdown-menu-presentation" aria-labelledby="presentConfigDropUpMenu">

                    <a      href="#" class="dropdown-item" onclick="resetLocalFormatting()" 
                            title="Reset all locally defined formatting values (reload the page then in order to make it have an effect!) ">
                        &#128472; Reset all locally defined formatting values</a>

                    <h6 class="dropdown-header">Show Configuration</h6>

                    <a      href="#" class="dropdown-item" onclick="changeBlankSlidesConfig()" 
                            title="Show empty slides between plan items">
                        <i id="config-BlankSlidesItem" class="fa fa-square-o">&nbsp;</i>insert blank slides between items?</a>

                    <a      href="#" class="dropdown-item" onclick="changeOfflineModeConfig()" 
                            title="Work off-line and get slides from local storage instead of from the server">
                        <i id="config-OfflineModeItem" class="fa fa-square-o">&nbsp;</i>use locally cached slides?</a>

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
            
            
        <div>
            {{-- DropUP Menu Button 
            --}}       
            <div id="popup-goto-menu" class="btn-group dropup">

                <button type="button" class="btn btn-sm btn-info dropdown-toggle" 
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Go to
                </button>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-presentation">
                    <div class="dropdown-menu scroll-menu">
                        @foreach ($items as $menu_item)
                            @if (! $menu_item->forLeadersEyesOnly)
                                <a class="dropdown-item nowrap{{ $item->id == $menu_item->id ? ' bg-info' : '' }}"
                                    id="menu-item-seq-no-{{ $menu_item->seq_no }}"
                                    href="{{ url('cspot/items/').'/'.$menu_item->id.'/present' }}">
                                    <small class="hidden-md-down">{{ $menu_item->seq_no }}</small> &nbsp; 
                                    @if ($menu_item->song_id && $menu_item->song->title)
                                        {!! $menu_item->song->title_2=='slides'
                                            ? '&#128464;'.$menu_item->song->title
                                            : ( $menu_item->song->title_2=='video'
                                                ? '<i class="fa fa-youtube">&nbsp;</i>'.$menu_item->song->title
                                                : '<i class="fa fa-music">&nbsp;</i><strong>'.$menu_item->song->title.'</strong>' )
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
                            <div><a class="dropdown-item" id="go-edit"
                                    href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/edit') }}">
                                    <i class="fa fa-pencil"></i> Edit this item
                            </a></div>
                        @endif
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" id="go-back"
                            href="{{ url('cspot/plans/'.$item->plan_id) }}">
                            <i class="fa fa-undo"></i>
                            Back to plan overview
                            <small class="float-right"><span class="font-weight-bold">*</span>item in local cache)</small>
                        </a>
                    </div>
                </div>
            </div>
       
            {{-- Add New Item into Plan!  
            --}}       
            @if (Auth::user()->ownsPlan($item->plan_id))
                <div class="btn-group dropup hidden-xs-down ml-2">
                    {{-- new MODAL POPUP to add song, scripture or comment --}}
                    <button type="button" class="btn btn-sm btn-outline-info btn-sm" title="Add New Item (Song etc)" 
                         data-toggle="modal" data-target="#searchSongModal"
                        data-plan-id="{{$item->plan_id}}" data-item-id="after-{{$item->id}}" 
                         data-seq-no="after-{{ $item->seq_no }}"
                               title="Select new Song, Scripture or Comment">
                        <i class="fa fa-plus"></i> song etc.
                    </button>
                </div>
            @endif
        </div>


        <div>
            {{-- Personal Notes 
            --}}
            <div class="btn-group dropup hidden-xs-down">
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

            {{-- start auto advance mode 
            --}}
            <a href="#" title="start auto-advance mode" onclick="startAutoAdvance();" 
                class="hidden-sm-down btn btn-sm btn-outline-success ml-3">&#9193;</a>
            
            {{-- help button to show modal 
            --}}
            <a href="#" title="show keyboard shortcuts" data-toggle="modal" data-target=".help-modal"
                class="hidden-sm-down btn btn-sm btn-outline-success ml-3">
            <i class="fa fa-question-circle fa-lg"></i></a>
            {{-- link to song data on CCLI songselect 
            --}}
            @if ($item->song_id && $item->song->ccli_no)
                <div class="btn-group ml-3">                
                    <a href="{{ env('SONGSELECT_URL', 'https://songselect.ccli.com/Songs/').$item->song->ccli_no }}" 
                        target="new" class="btn btn-sm btn-info hidden-sm-down py-0">
                    <img src="{{ url('/') }}/images/songselectlogo.png" width="30"></a>
                </div>
            @endif
        </div>

    </div>



    {{-- go to first/last slide
    --}}
    <a href="{{ url('cspot/items/').'/'.$item->plan->firstItem()->id.'/present' }}" id="go-first-item"></a>
    <a href="{{ url('cspot/items/').'/'.$item->plan->lastItem()->id.'/present'  }}" id="go-last-item" ></a>




    <nav class="navbar navbar-toggleable-sm fixed-bottom navbar-dark bg-black mx-auto lh-1 p-0" id="bottom-fixed-navbar">


        <a class="navbar-brand" href="#">
            {{-- show configurable clock 
            --}}
            <div class="dropup d-inline-block">
                <span title="Click to design this" class="link"                     
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span id="time-of-day" class="show-time-of-day">hh:mm</span>
                </span>

                <div class="dropdown-menu dropdown-menu-left bg-inverse text-white">

                    <div>
                        <button class="btn btn-sm btn-info narrow">Text: 
                            <input onchange="changeColor('.show-time-of-day', this.value);" type='text' class="colorPicker" />
                        </button class="btn btn-sm btn-info">
                        <button class="btn btn-sm btn-info narrow">BG: 
                            <input onchange="changeColor('.show-time-of-day', this.value, 'BG');" type='text' class="BGcolorPicker" />
                        </button class="btn btn-sm btn-info">
                    </div>
                    <div>Font size:
                        <button class="btn btn-sm btn-outline-info narrow"> 
                            <span class="link" onmousedown="changeFontSize('.show-time-of-day', 'increase')">&#10133;</span>
                        </button>
                        <button class="btn btn-sm btn-outline-info narrow"> 
                            <span class="link" onmousedown="changeFontSize('.show-time-of-day', 'decrease')">&#10134;</span>
                        </button>
                    </div>

                </div>
            </div>

            {{-- show song title or comment in second navbar on smaller screens only 
            --}}
            <div class="navbar-text hidden-md-up small">
                <small class="hidden-xs-down text-muted">Item {{$item->seq_no}} -</small>
                <span class="w-50 limited-width">
                    {{ ($item->song_id && $item->song->title)  ?  $item->song->title  :  $item->comment }}
                </span>
                <small class="hidden-sm-up text-muted limited-width">(next: {{ substr(getItemTitle($item),0,15) }})</small>
                <small class="hidden-lg-up hidden-xs-down text-muted limited-width">(<span class="font-weight-bold">up next</span>: {{ getItemTitle($item) }})</small>
            </div>            
        </a>


        {{-- toggler button to show navbar content on smaller devices 
        --}}
        <button class="navbar-toggler navbar-toggler-right navbar-inverse" type="button" data-toggle="collapse" data-target="#presentationBottomNavbar" 
                aria-controls="presentationBottomNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="presentationBottomNavbar">


            {{-- potential buttons for lyric parts (verses, chorusses etc) 
            --}}
            <ul class="navbar-nav">
                <li id="lyrics-parts-indicators">
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


            {{-- show song title or comment in first navbar on bigger screens only 
            --}}
            <span class="navbar-nav ml-auto hidden-sm-down" id="item-navbar-label">
                <small class="hidden-md-down text-muted">Item {{$item->seq_no}} -</small>
                <span class="text-success hidden-md-down limited-width">
                    {{ ($item->song_id && $item->song->title) ? $item->song->title : $item->comment }}
                </span>
                <span class="text-success hidden-lg-up limited-width">
                    {{ substr(($item->song_id && $item->song->title) ? $item->song->title : $item->comment, 0, 20) }}
                </span>
            </span>

        
            {{-- button to reveal the second navbar at the bottom 
            --}}
            <button class="btn btn-sm btn-info px-3" onclick="toggleLyricsNavbar()">&uArr;
            </button>


            {{-- hide all presentation content 
            --}}
            <button class="btn btn-sm btn-outline-secondary" id="show-blank-screen" type="button" onclick="showBlankScreen()">
                <span class="hidden-sm-down text-muted">Blank</span><i class="fa fa-tv hidden-md-up"></i>
            </button>     


            {{-- what's coming next? (Show unless we are on the last item!) 
            --}}
            <span class="navbar-nav" id="item-navbar-next-label">
                @if ($item->id != $item->plan->lastItem()->id)
                    <small class="hidden-lg-up text-muted limited-width">(next: {{ substr(getItemTitle($item),0,15) }})</small>
                    <small class="hidden-md-down text-muted limited-width">(<span class="font-weight-bold">up next</span>: {{ getItemTitle($item) }})</small>
                @endif
            </span>
            

            {{-- 'sequence' indicates the order in which the various lyric parts are to be shown 
            --}}
            <span class="navbar-nav ml-auto hidden-xs-down text-success" id="lyrics-sequence-nav">
                <!-- {{-- this is currently resolved on the client side --}} -->
                @if ($item->song_id && $item->song->sequence)
                    <a href="#" onclick="advancePresentation();" 
                        title="show next slide" id="btn-show-next" 
                        class="nav-item btn btn-sm btn-info" role="button"><i class="fa fa-chevron-right fa-lg"></i></a>
                @endif
            </span>


            {{-- jump to next plan item 
            --}}
            <ul class="navbar-nav" id="next-item-button">
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

        </div>
    </nav>


    <script>        
        // make type of presentation globally available
        $(document).ready(function() {
            cSpot.presentationType = 'lyrics';

            // control the activation of a blank screen
            var screenBlank = true;
        });

        function toggleLyricsNavbar()
        {
            if ($('#lyricsNavbar').is(':visible'))
                $('#lyricsNavbar').css('display', 'none')
            else
                $('#lyricsNavbar').css('display', 'flex')
        }

    </script>


@stop
