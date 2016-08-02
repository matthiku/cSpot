
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Present Plan")

@section('plan', 'active')



@section('content')




    <!-- ================================================================================ -->
    <div id="main-content" class="bg-inverse">

        @if ($item->song_id )
            @if ($item->song->lyrics )
                <div class="text-present m-b-3 lyrics-parts" id="lyrics-title" style="display: none;">
                    {{ $item->song->title }}{{ $item->song->title2 ? '('.$item->song->title2.')' : '' }}
                </div>
                <div class="text-present m-b-3" id="present-lyrics" style="display: none;" >
                    {{ $item->song->lyrics }}
                </div>
                <div class="hidden-xs-up" id="sequence">{{ $item->song->sequence }}</div>
            @endif
        @endif

        @if ($item->files)
            <?php 
                // make sure the files are sorted by seq no
                $files  = $item->files->sortBy('seq_no')->all(); 
                $key    = 1; // we can't use a $key in the foreach statement as it's a re-sorted collection!
            ?>            
            @foreach ($files as $file)
                <img class="slide-background-image m-b-2" data-slides-id="{{ $key }}"  style="display: none;" 
                       src="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">
                <?php $key++; ?>
            @endforeach
        @endif

        @if ($bibleTexts)
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
            <li>
                <!-- potential buttons for lyric parts (cerses, chorusses etc) -->
                <a href="#" onclick="lyricsShow('start-lyrics');" 
                    title="show start lyrics" id="btn-show-start-lyrics" style="display: none;" 
                    class="nav-item btn btn-sm btn-info-outline lyrics-show-btns" role="button">S</a>
                <a href="#" onclick="lyricsShow('verse1');" 
                    title="show verse 1" id="btn-show-verse1" style="display: none;" 
                    class="nav-item btn btn-sm btn-info-outline lyrics-show-btns" role="button">1</a>                
                <a href="#" onclick="lyricsShow('verse2');" 
                    title="show verse 2" id="btn-show-verse2" style="display: none;" 
                    class="nav-item btn btn-sm btn-info-outline lyrics-show-btns" role="button">2</a>                
                <a href="#" onclick="lyricsShow('verse3');" 
                    title="show verse 3" id="btn-show-verse3" style="display: none;" 
                    class="nav-item btn btn-sm btn-info-outline lyrics-show-btns" role="button">3</a>                
                <a href="#" onclick="lyricsShow('verse4');" 
                    title="show verse 4" id="btn-show-verse4" style="display: none;" 
                    class="nav-item btn btn-sm btn-info-outline lyrics-show-btns" role="button">4</a>                
                <a href="#" onclick="lyricsShow('verse5');" 
                    title="show verse 5" id="btn-show-verse5" style="display: none;" 
                    class="nav-item btn btn-sm btn-info-outline lyrics-show-btns" role="button">5</a>
                <a href="#" onclick="lyricsShow('verse6');" 
                    title="show verse 6" id="btn-show-verse6" style="display: none;" 
                    class="nav-item btn btn-sm btn-info-outline lyrics-show-btns" role="button">6</a>                
                <a href="#" onclick="lyricsShow('verse7');" 
                    title="show verse 7" id="btn-show-verse7" style="display: none;" 
                    class="nav-item btn btn-sm btn-info-outline lyrics-show-btns" role="button">7</a>                
                <a href="#" onclick="lyricsShow('prechorus');" 
                    title="show pre-chorus" id="btn-show-prechorus" style="display: none;" 
                    class="nav-item btn btn-sm btn-info-outline lyrics-show-btns" role="button">P</a>
                <a href="#" onclick="lyricsShow('chorus1');" 
                    title="show chorus" id="btn-show-chorus1" style="display: none;" 
                    class="nav-item btn btn-sm btn-info-outline lyrics-show-btns" role="button">Ch</a>
                <a href="#" onclick="lyricsShow('chorus2');" 
                    title="show chorus 2" id="btn-show-chorus2" style="display: none;" 
                    class="nav-item btn btn-sm btn-info-outline lyrics-show-btns" role="button">Ch2</a>
                <a href="#" onclick="lyricsShow('bridge');" 
                    title="show bridge" id="btn-show-bridge" style="display: none;" 
                    class="nav-item btn btn-sm btn-info-outline lyrics-show-btns" role="button">B</a>
                <a href="#" onclick="lyricsShow('ending');" 
                    title="show ending" id="btn-show-ending" style="display: none;" 
                    class="nav-item btn btn-sm btn-info-outline lyrics-show-btns" role="button">E</a>
            </li>
        </ul>
        <ul class="nav navbar-nav pull-xs-right">
            <li>
                <!-- jump to next plan item -->
                <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/next/present') }}"
                    class="nav-item btn btn-sm btn-warning" role="button" id="go-next-item">
                    <i class="fa fa-angle-double-right fa-lg"></i>
                </a>
            </li>
        </ul>

        <!-- 'sequence' indicates the order in which the various lyric parts are to be shown -->
        <span class="navbar-brand pull-xs-right hidden-xs-down" id="lyrics-sequence-nav">
            <!-- {{-- this is currently resolved on the client side --}} -->
            @if ($item->song_id && $item->song->sequence)
                <a href="#" onclick="advancePresentation();" 
                    title="show next slide" id="btn-show-next" 
                    class="nav-item btn btn-sm btn-info" role="button"><i class="fa fa-chevron-right fa-lg"></i></a>
            @endif
        </span>

        <!-- <span class="navbar-brand center" id="show-linecount"></span> -->

        <!-- show song title or comment in first navbar on bigger screens only -->
        <span class="nav navbar-nav center hidden-sm-down">
            <small class="hidden-md-down text-muted">Item {{$item->seq_no}} -</small>
            <span class="hidden-md-down">
                {{ ($item->song_id && $item->song->title) ? $item->song->title : $item->comment }}
            </span>
            <span class="hidden-lg-up">
                {{ substr(($item->song_id && $item->song->title) ? $item->song->title : $item->comment, 0, 20) }}
            </span>
            <small class="hidden-lg-up hidden-xs-down text-muted">(next: {{ substr(getItemTitle($item),0,15) }})</small>
            <small class="hidden-md-down text-muted">(up next: {{ getItemTitle($item) }})</small>
        </span>
    
        <!-- button to reveal the second navbar at the bottom -->
        <button class="navbar-toggler btn btn-info active" type="button" data-toggle="collapse" data-target="#lyricsNavbar">
            &hellip;
        </button>        
        <button class="btn btn-secondary-outline" type="button" onclick="showBlankScreen()">
            Blank
        </button>        


        <!-- 
            the second navbar is hidden at first 
        -->
        <div class="collapse navbar-toggleable" id="lyricsNavbar">

            <!-- show song title or comment in secong navbar on smaller screens only -->
            <span class="nav navbar-nav center hidden-md-up hidden-xs-down">
                <small class="hidden-md-down text-muted">Item {{$item->seq_no}} -</small>
                @if ($item->song_id && $item->song->title)
                    {{ $item->song->title }}
                @else
                    {{ $item->comment }}
                @endif
            </span>

            
            <!-- 
                DropUP Menu Button
            -->
            <div class="btn-group dropup center">

                <button type="button" class="btn btn-sm btn-info dropdown-toggle" 
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Go to
                </button>
                <div class="dropdown-menu dropdown-menu-right bg-faded">
                    @foreach ($items as $menu_item)
                        <a class="dropdown-item nowrap 
                            @if ($item->id == $menu_item->id)
                                bg-info
                            @endif
                            "
                            href="{{ url('cspot/items/').'/'.$menu_item->id.'/present' }}">
                            <small class="hidden-md-down">{{ $menu_item->seq_no }}</small> &nbsp; 
                            @if ($menu_item->song_id && $menu_item->song->title)
                                <i class="fa fa-music">&nbsp;</i><strong>{{ $menu_item->song->title }}</strong>
                            @else
                                {{ substr($menu_item->comment, 0, 45) }}
                            @endif
                        </a>
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
                    </a>
                </div>

            </div>

            <!-- help button to show modal -->
            <a href="#" title="show keyboard shortcuts" data-toggle="modal" data-target=".help-modal"
                class="hidden-sm-down pull-xs-right btn btn-sm btn-success-outline m-r-1">
            <i class="fa fa-question-circle fa-lg"></i></a>


            <!-- 
                go to first/last slide 
            -->
            <a href="{{ url('cspot/items/').'/'.$item->plan->firstItem()->id.'/present' }}" id="go-first-item"></a>
            <a href="{{ url('cspot/items/').'/'.$item->plan->lastItem()->id.'/present'  }}" id="go-last-item" ></a>



            <ul class="nav navbar-nav pull-xs-left">
                <li>
                    <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/previous/present') }}"
                        class="nav-item btn btn-sm btn-warning" role="button" id="go-previous-item">
                        <i class="fa fa-angle-double-left fa-lg"></i>
                    </a> 
                    <a href="#" onclick="changeFontSize(['.text-present', '.bible-text-present', '.bible-text-present>h1', '.bible-text-present>p'], 'decrease');" 
                            title="decrease font size" id="decr-font"
                            class="nav-item btn btn-sm btn-info" role="button">
                        A <i class="fa fa-minus fa-lg"></i>
                    </a>
                    <a href="#" onclick="changeFontSize(['.text-present', '.bible-text-present', '.bible-text-present>h1', '.bible-text-present>p']);" 
                            title="increase font size" id="incr-font"
                            class="m-l-0 nav-item btn btn-sm btn-info" role="button">
                        A <i class="fa fa-plus fa-lg"></i>
                    </a>
                </li>
            </ul>

            <div class="nav-item dropup pull-xs-left">
                <button class="nav-link m-l-1 btn btn-sm btn-info dropdown-toggle" href="#" type="button" 
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Align</button>
                <div class="dropdown-menu bg-info">
                    <a onclick="changeTextAlign(['.text-present', '.bible-text-present'], 'left');" 
                        class="dropdown-item" href="#"><i class="fa fa-align-left fa-lg"></i> Left</a>
                    <a onclick="changeTextAlign(['.text-present', '.bible-text-present'], 'right');"
                        class="dropdown-item" href="#"><i class="fa fa-align-right fa-lg"></i> Right</a>
                    <a onclick="changeTextAlign(['.text-present', '.bible-text-present'], 'center');"
                        class="dropdown-item" href="#"><i class="fa fa-align-center fa-lg"></i> Center</a>
                </div>
            </div>

            <form class="form-inline nav-item m-l-1 pull-xs-left label label-info">
                <div class="checkbox" style="line-height: 2" onmouseup="configBlankSlides()">
                    <label class="checkbox-inline c-input c-checkbox" title="Show empty slides between items?">
                        <input type="checkbox" id="configBlankSlides">
                            <span class="c-indicator"></span>&nbsp;blank slides between items?
                    </label>
                </div>
            </form>


        @if( env('PRESENTATION_ENABLE_SYNC', 'false') )
            {{-- become MAIN presenter, if possible --}}
            <form class="form-inline nav-item m-l-1 pull-xs-left label label-info">
                <div class="checkbox" style="line-height: 2" onmouseup="configMainPresenter()">
                    <label class="checkbox-inline c-input c-checkbox" title="Become Main Presenter controlling other presentations">
                        <input type="checkbox" id="configMainPresenter">
                            <span class="c-indicator"></span>&nbsp;Main Presenter
                    </label>
                </div>
                <span class="small showPresenterName"> ({{ $serverSideMainPresenter ? $serverSideMainPresenter['name'] : 'none' }})</span>
            </form>

            {{-- synchronise this presentation with the Main Presenter --}}
            <form class="form-inline nav-item m-l-1 label label-info">
                <div class="checkbox" style="line-height: 2" onmouseup="configSyncPresentation()">
                    <label class="checkbox-inline c-input c-checkbox" title="Synchronise this presentation with Main Presenter">
                        <input type="checkbox" id="configSyncPresentation">
                            <span class="c-indicator"></span>&nbsp;Sync Presentation
                    </label>
                </div>
                <span class="small">&nbsp;with:</span>
                <span class="small showPresenterName"> ({{ $serverSideMainPresenter ? $serverSideMainPresenter['name'] : 'none' }})</span>
            </form>
        @endif


            {{-- TODO: this is currently not working properly --}}
            <form class="form-inline nav-item m-l-1 pull-xs-left label label-info" style="display: none">
                <div class="checkbox" style="line-height: 2" onchange="changeConfigShowVersCount()">
                    <label class="c-input" title="How many bible verses should be shown per slide?">
                        <select class="" id="configShowVersCount">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                            Number of verses per slide?
                    </label>
                </div>
            </form>

        </div>
    </nav>


@stop
