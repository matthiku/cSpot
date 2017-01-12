
<?php
$modalContent = '
    <p><kbd>Esc</kbd> go back to plan overview</p>
    <p><kbd> <i class="fa fa-arrow-right"> </i></kbd> go to next plan item</p>
    <p><kbd> <i class="fa fa-arrow-left"> </i></kbd> go to previous plan item</p>
    <p><kbd>1</kbd>, <kbd>2</kbd>, <kbd>3</kbd>... jump to verse 1...n</p>
    <p><kbd>c</kbd> jump to chorus</p>
    <p><kbd>b</kbd> jump to bridge</p>
    <p class="float-right">On <strong>tablets</strong> or <strong>phones</strong>, you should instead use the buttons provided at the bottom of this screen!</p>
    <hr>
    <h5>Capo Usage:</h5>
    <p><img width="100%" src="'. url('/') .'/images/transpose.png"></p>';
?>

@include( 'cspot/snippets/modal', ['modalContent' => $modalContent, 'modalTitle' => 'Use your keyboard to:' ] )


<nav class="navbar navbar-toggleable-sm fixed-bottom bg-primary center p-0" id="present-navbar">

    <div class="navbar-brand hidden-md-up text-left" href="#">
        {{-- go to previous --}}
        <span class="navbar-text my-0 py-0">        
            <!-- go to previous slide -->
            <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/previous/'.$type) }}"
                    onclick="$('#show-spinner').modal({keyboard: false});" 
                class="nav-item btn btn-sm btn-warning" role="button">
                <i class="fa fa-angle-double-left fa-lg"></i>
            </a> 
        </span>
        {{-- show setup menu --}}
        <div class="btn-group dropup ml-1">
            <button type="button" class="btn btn-sm btn-info dropdown-toggle" 
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Show
            </button>

            <div class="dropdown-menu dropdown-menu-left bg-faded lh-2">
                @foreach (range(1,7) as $num)
                    <a class="dropdown-item" href="#verse{{ $num }}" 
                            id="jump-verse{{ $num }}" style="display: none">
                        Verse {{ $num }}</a>
                @endforeach
                <a class="dropdown-item" href="#chorus" style="display: none">Chorus</a>
                <a class="dropdown-item" href="#bridge" style="display: none">Bridge</a>


                <div class="hidden-lg-up dropdown-divider"></div>                

                <!-- change number of columns for chords display -->
                <span class="hidden-lg-up dropdown-item edit-show-buttons" >
                    <span>Cols:</span>
                    <span class="ml-0 btn btn-sm btn-info edit-show-buttons hidden" onclick="$('#onsongs').css('column-count', 1);"
                        title="show chords in 1 column" >&#9783;1</span>
                    <span class="ml-0 btn btn-sm btn-info edit-show-buttons hidden" onclick="$('#onsongs').css('column-count', 2);" 
                        title="show chords in 2 columns">&#9783;2</span>
                    <span class="ml-0 btn btn-sm btn-info edit-show-buttons hidden" onclick="$('#onsongs').css('column-count', 3);" 
                        title="show chords in 3 columns">&#9783;3</span>
                </span>


                <div class="hidden-lg-up dropdown-divider"></div>                

                <a class="dropdown-item hidden-lg-up edit-show-buttons" 
                        href="#" style="display: none"
                        onclick="changeFontSize(['.text-song', '.show-onsong-text'], 'decrease');" >
                    A <i class="fa fa-minus"></i> decrease font
                </a>
                <a class="dropdown-item hidden-lg-up edit-show-buttons" 
                        href="#" style="display: none"
                        onclick="changeFontSize(['.text-song', '.show-onsong-text']);" >
                    A <i class="fa fa-plus"></i> increase font
                </a>


                <a class="dropdown-item hidden-lg-up edit-show-buttons" id="goswap-dropup"
                        href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/swap/'.$type) }}">
                    <i class="fa fa-file-text"></i> <i class="fa fa-refresh fa-lg"></i> <i class="fa fa-music"></i>
                    sheetmusic/chords
                </a>

            </div>
        </div>
        {{-- help button --}}
        <a href="#" title="show keyboard shortcuts" data-toggle="modal" data-target=".help-modal"
            class="btn-group btn btn-sm btn-outline-success mx-3">
        <i class="fa fa-question-circle fa-lg"></i></a>
        {{-- Go to other items --}}
        <div class="btn-group dropup mr-1">
            <button type="button" class="btn btn-sm btn-info dropdown-toggle" 
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Go<span class="hidden-lg-down"> to</span>
            </button>
            <div class="dropdown-menu dropdown-menu-left bg-faded">
                @foreach ($items as $menu_item)
                    @if ( Auth::user()->ownsPlan($item->plan_id)  ||  ! $menu_item->forLeadersEyesOnly )
                        <a class="dropdown-item nowrap 
                            {{ $item->id == $menu_item->id ? 'bg-info' : '' }}
                            {{ ! $menu_item->song_id || $menu_item->song->title_2=='slide' ? 'hidden-md-down' : '' }}
                            {{ count($items) > 15 ? 'dropup-menu-item' : '' }}"
                            onclick="$('#show-spinner').modal({keyboard: false});" 
                            href="{{ url('cspot/items/').'/'.$menu_item->id.'/'.$type }}">
                            <small class="hidden-xs-down">{{ $menu_item->seq_no }} &nbsp;</small> 
                            @if ( $menu_item->song_id && $menu_item->song->title )
                                {!! $menu_item->song->title_2=='slides'
                                    ? '&#128464;'.$menu_item->song->title
                                    : ( $menu_item->song->title_2=='video'
                                        ? '<i class="fa fa-youtube">&nbsp;</i>'.$menu_item->song->title
                                        : '<i class="fa fa-music">&nbsp;</i><strong>'.$menu_item->song->title.'</strong>' )
                                    !!}
                            @else
                                {{ $menu_item->comment }}
                            @endif
                        </a>
                    @endif
                @endforeach
                @if (Auth::user()->ownsPlan($item->plan_id))
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" onclick="$('#show-spinner').modal({keyboard: false});" 
                        href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/edit') }}">
                        <i class="fa fa-pencil"></i>
                        Edit this item
                    </a>
                @endif
                @if ($item->song_id && $item->song->youtube_id)
                    <a class="dropdown-item" target="new" 
                        href="{{ env('YOUTUBE_PLAY', 'https://www.youtube.com/watch?v=').$item->song->youtube_id }}">
                        <i class="red fa fa-youtube-play fa-lg"></i>Play on Youtube
                    </a>
                @endif
                <div class="dropdown-divider"></div>
                <a      class="dropdown-item" id="go-back"
                        onclick="$('#show-spinner').modal({keyboard: false});" 
                        href="{{ url('cspot/plans/'.$item->plan_id) }}">
                    <i class="fa fa-undo"></i>
                    Back to plan overview
                </a>
            </div>
        </div>
        {{-- go to next --}}
        <span class="navbar-text my-0 py-0">        
            <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/next/'.$type) }}"
                    onclick="$('#show-spinner').modal({keyboard: false});" 
                class="nav-item btn btn-sm btn-warning" role="button" id="go-next-item">
                <i class="fa fa-angle-double-right fa-lg"></i>
            </a>
        </span>
    </div>

    {{-- toggler button to show navbar content on smaller devices 
    --}}
    <button class="navbar-toggler navbar-toggler-right navbar-inverse" type="button" data-toggle="collapse" data-target="#presentationBottomNavbar" 
            aria-controls="presentationBottomNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="presentationBottomNavbar">


        <!-- 
            LEFT part of navbar 
        -->
        <ul class="navbar-nav">
            <li>
                <!-- go to previous slide -->
                <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/previous/'.$type) }}"
                        onclick="$('#show-spinner').modal({keyboard: false});" 
                    class="nav-item btn btn-sm btn-warning hidden-sm-down" role="button" id="go-previous-item">
                    <i class="fa fa-angle-double-left fa-lg"></i>
                </a> 

                <!-- change number of columns for chords display -->
                <span class="hidden-md-down nav-item ml-2 btn btn-sm btn-info edit-show-buttons hidden" onclick="$('#onsongs').css('column-count', 1);"
                    title="show chords in 1 column" >&#9783;1</span>
                <span class="hidden-md-down nav-item mx-0 btn btn-sm btn-info edit-show-buttons hidden" onclick="$('#onsongs').css('column-count', 2);" 
                    title="show chords in 2 columns">&#9783;2</span>
                <span class="hidden-md-down nav-item ml-0 btn btn-sm btn-info edit-show-buttons hidden" onclick="$('#onsongs').css('column-count', 3);" 
                    title="show chords in 3 columns">&#9783;3</span>


                <!-- decrease font size -->
                <a href="#" onclick="changeFontSize(['.text-song', '.show-onsong-text'], 'decrease');" id="decr-font"
                        title="decrease font size" style="display: none" 
                        class="hidden-md-down nav-item btn btn-sm btn-info edit-show-buttons ml-1 mr-0" role="button">
                    A <i class="fa fa-minus"></i>
                </a>

                <!-- increase font size -->
                <a href="#" onclick="changeFontSize(['.text-song', '.show-onsong-text']);" id="incr-font"
                        title="increase font size" style="display: none" 
                        class="hidden-md-down nav-item ml-0 btn btn-sm btn-info edit-show-buttons" role="button">
                    A <i class="fa fa-plus"></i>
                </a>

                <!-- swap between chords and sheetmusic -->
                <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/swap/'.$type) }}" 
                        style="display: none" id="show-chords-or-music"
                        onclick="$('#show-spinner').modal({keyboard: false});" 
                        title="swap between chords and sheetmusic"
                        class="ml-2 hidden-md-down nav-item btn btn-sm btn-warning edit-show-buttons" role="button">
                    <i class="fa fa-file-text"></i> <i class="fa fa-refresh fa-lg"></i> <i class="fa fa-music"></i>
                </a>

                @if( env('PRESENTATION_ENABLE_SYNC', 'false') && ! Auth::user()->ownsPlan($item->plan_id))
                    {{-- synchronise this presentation with the Main Presenter --}}
                    <form class="form-inline nav-item ml-1 label label-info">
                        <div class="checkbox" onmouseup="changeSyncPresentation()">
                            <label class="checkbox-inline c-input c-checkbox mb-0" style="" 
                                    title="Synchronise this presentation with Main Presenter">
                                <input type="checkbox" id="configSyncPresentation">
                                    <span class="c-indicator"></span>&nbsp;Sync Presentation
                            </label>
                            <span class="small">(with </span>
                            <span class="small showPresenterName">{{ $serverSideMainPresenter ? $serverSideMainPresenter['name'] : 'none' }}</span>
                        </div>
                    </form>
                @endif

            </li>
        </ul>



        {{-- configuration menu --}}
        <div class="nav-item btn-group dropup mx-2">

            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                 id="presentConfigDropUpMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="text-muted hidden-md-down">Conf.&nbsp;</span><i class="fa fa-cog"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-presentation" aria-labelledby="presentConfigDropUpMenu">

                <h6 class="dropdown-header">Local Caching</h6>

                    <a      href="#" class="dropdown-item" onclick="changeOfflineModeConfig()" 
                            title="Work off-line and get slides from local storage instead of from the server">
                        <i id="config-OfflineModeItem" class="fa fa-square-o">&nbsp;</i>use locally cached slides?</a>

                    <a      href="#" class="dropdown-item small" onclick="clearLocalCache();"
                            title="delete all locally cached items">
                        <i class="fa fa-trash-o red"></i>&nbsp;</i>delete all locally cached slides</a>

            @if (Auth::user()->ownsPlan($item->plan_id))
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
            @endif

            </div>
        </div>



        <!-- 
            DropUP Menu "Show"
        -->
        <div class="hidden-sm-down btn-group dropup ml-1" id="jumplist">

            <button type="button" class="btn btn-sm btn-info dropdown-toggle" 
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Show
            </button>

            <div class="dropdown-menu dropdown-menu-left bg-faded lh-2">
                @foreach (range(1,7) as $num)
                    <a class="dropdown-item" href="#verse{{ $num }}" 
                            id="jump-verse{{ $num }}" style="display: none">
                        Verse {{ $num }}</a>
                @endforeach
                <a class="dropdown-item" href="#chorus" id="jump-chorus" style="display: none">Chorus</a>
                <a class="dropdown-item" href="#bridge" id="jump-bridge" style="display: none">Bridge</a>


                <div class="hidden-lg-up dropdown-divider"></div>                

                <!-- change number of columns for chords display -->
                <span class="hidden-lg-up dropdown-item edit-show-buttons" >
                    <span>Cols:</span>
                    <span class="ml-0 btn btn-sm btn-info edit-show-buttons hidden" onclick="$('#onsongs').css('column-count', 1);"
                        title="show chords in 1 column" >&#9783;1</span>
                    <span class="ml-0 btn btn-sm btn-info edit-show-buttons hidden" onclick="$('#onsongs').css('column-count', 2);" 
                        title="show chords in 2 columns">&#9783;2</span>
                    <span class="ml-0 btn btn-sm btn-info edit-show-buttons hidden" onclick="$('#onsongs').css('column-count', 3);" 
                        title="show chords in 3 columns">&#9783;3</span>
                </span>


                <div class="hidden-lg-up dropdown-divider"></div>                

                <a class="dropdown-item hidden-lg-up edit-show-buttons" 
                        href="#" style="display: none"
                        onclick="changeFontSize(['.text-song', '.show-onsong-text'], 'decrease');" >
                    A <i class="fa fa-minus"></i> decrease font
                </a>
                <a class="dropdown-item hidden-lg-up edit-show-buttons" 
                        href="#" style="display: none"
                        onclick="changeFontSize(['.text-song', '.show-onsong-text']);" >
                    A <i class="fa fa-plus"></i> increase font
                </a>


                <a class="dropdown-item hidden-lg-up edit-show-buttons" id="goswap-dropup"
                        href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/swap/'.$type) }}">
                    <i class="fa fa-file-text"></i> <i class="fa fa-refresh fa-lg"></i> <i class="fa fa-music"></i>
                    sheetmusic/chords
                </a>

            </div>
        </div>



        {{-- show title and next title 
        --}}
        <span class="nav navbar-nav mx-auto">

            <small class="hidden-lg-down">{{$item->seq_no}} </small>
            <small class="hidden-md-up">{{$item->seq_no}} </small>

            @if ($item->song_id && $item->song->title)
                <span>{{ $item->song->title }}</span>
            @else
                <span class="limited-width">{{ $item->comment }}</span>
            @endif

            @if ($item->id != $item->plan->lastItem()->id)
                <small class="hidden-lg-down">(next: {{ substr(getItemTitle($item),0,15) }})</small>
                <small class="hidden-md-up">(next: {{ getItemTitle($item) }})</small>
            @endif

            <!-- 
                Add New Item into Plan! 
            -->
            @if (Auth::user()->ownsPlan($item->plan_id))
                <div class="btn-group dropup ml-4">
                    {{-- new MODAL POPUP to add song, scripture or comment --}}
                    <button type="button" class="btn btn-sm btn-outline-info btn-sm" title="Add New Item (Song etc.)" 
                         data-toggle="modal" data-target="#searchSongModal"
                        data-plan-id="{{$item->plan_id}}" data-item-id="after-{{$item->id}}" 
                         data-seq-no="after-{{ $item->seq_no }}"
                               title="Select new Song, Scripture or Comment">
                        <i class="fa fa-plus"></i> song<span class="hidden-lg-down"> etc.</span><span class="hidden-md-up"> etc.</span>
                    </button>
                </div>
            @endif
        </span>




        <!-- 
            Personal Notes 
        -->
        <div class="btn-group dropup hidden-xs-down{{ $item->itemNotes->where('user_id', Auth::user()->id)->first() ? ' open' : '' }}">
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
            go to first/last slide 
            (invisible, only used for keyboard shortcuts 'Home' and 'End')
        -->
        <a href="{{ url('cspot/items/').'/'.$item->plan->firstItem()->id.'/'.$type }}" id="go-first-item"></a>
        <a href="{{ url('cspot/items/').'/'.$item->plan->lastItem()->id.'/'.$type  }}" id="go-last-item" ></a>


        <!-- 
            help button 
        -->
        <a href="#" title="show keyboard shortcuts" data-toggle="modal" data-target=".help-modal"
            class="hidden-sm-down btn btn-sm btn-outline-success mx-3">
        <i class="fa fa-question-circle fa-lg"></i></a>



        <!-- 
            link to song data on CCLI songselect 
        -->
        <div class="btn-group mr-1">
            @if ($item->song_id && $item->song->ccli_no)
                <a href="{{ env('SONGSELECT_URL', 'https://songselect.ccli.com/Songs/').$item->song->ccli_no }}" 
                    target="new" class="float-right btn btn-sm btn-info hidden-md-down py-0">
                <img src="{{ url('/') }}/images/songselectlogo.png" width="25"></a>
            @elseif ($item->song_id && $item->song->youtube_id)
                <a class="float-right btn btn-sm btn-info hidden-md-down" target="new" 
                    href="{{ env('YOUTUBE_PLAY', 'https://www.youtube.com/watch?v=').$item->song->youtube_id }}">
                    <i class="red fa fa-youtube-play fa-lg"></i>
                </a>
            @else
                <a href="#" disabled="" 
                   class="float-right btn btn-sm btn-outline-secondary hidden-lg-down">
                <i class="fa fa-youtube-play fa-lg"></i>&nbsp;</a>
            @endif
        </div>



        <!-- 
            DropUP Menu "Go to..."
        -->
        <div class="hidden-sm-down btn-group dropup mr-1">

            <button type="button" class="btn btn-sm btn-info dropdown-toggle" 
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Go<span class="hidden-lg-down"> to</span>
            </button>
            <div class="dropdown-menu dropdown-menu-right bg-faded">
                @foreach ($items as $menu_item)
                    @if ( Auth::user()->ownsPlan($item->plan_id)  ||  ! $menu_item->forLeadersEyesOnly )
                        <a class="dropdown-item nowrap 
                            {{ $item->id == $menu_item->id ? 'bg-info' : '' }}
                            {{ ! $menu_item->song_id || $menu_item->song->title_2=='slide' ? 'hidden-md-down' : '' }}
                            {{ count($items) > 15 ? 'dropup-menu-item' : '' }}"
                            onclick="$('#show-spinner').modal({keyboard: false});" 
                            href="{{ url('cspot/items/').'/'.$menu_item->id.'/'.$type }}">
                            <small class="hidden-xs-down">{{ $menu_item->seq_no }} &nbsp;</small> 
                            @if ( $menu_item->song_id && $menu_item->song->title )
                                {!! $menu_item->song->title_2=='slides'
                                    ? '&#128464;'.$menu_item->song->title
                                    : ( $menu_item->song->title_2=='video'
                                        ? '<i class="fa fa-youtube">&nbsp;</i>'.$menu_item->song->title
                                        : '<i class="fa fa-music">&nbsp;</i><strong>'.$menu_item->song->title.'</strong>' )
                                    !!}
                            @else
                                {{ $menu_item->comment }}
                            @endif
                            <sup id="in-cache-seq-no-{{ $menu_item->seq_no }}" style="display: none">*</sup>
                        </a>
                    @endif
                @endforeach
                @if (Auth::user()->ownsPlan($item->plan_id))
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" id="go-edit"
                        onclick="$('#show-spinner').modal({keyboard: false});" 
                        href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/edit') }}">
                        <i class="fa fa-pencil"></i>
                        Edit this item
                    </a>
                @endif
                @if ($item->song_id && $item->song->youtube_id)
                    <a class="dropdown-item" target="new" 
                        href="{{ env('YOUTUBE_PLAY', 'https://www.youtube.com/watch?v=').$item->song->youtube_id }}">
                        <i class="red fa fa-youtube-play fa-lg"></i>Play on Youtube
                    </a>
                @endif
                <div class="dropdown-divider"></div>
                <a      class="dropdown-item" id="go-back"
                        onclick="$('#show-spinner').modal({keyboard: false});" 
                        href="{{ url('cspot/plans/'.$item->plan_id) }}">
                    <i class="fa fa-undo"></i>
                    Back to plan overview
                    <small class="float-right">(* = item in local cache)</small>
                </a>
            </div>
        </div>




        {{-- click to next item 
        --}}
        <ul class="nav navbar-nav hidden-sm-down">
            <li>
                <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/next/'.$type) }}"
                        onclick="$('#show-spinner').modal({keyboard: false});" 
                    class="nav-item btn btn-sm btn-warning" role="button" id="go-next-item">
                    <i class="fa fa-angle-double-right fa-lg"></i>
                </a>
            </li>
        </ul>

    </div>
</nav>



{{-- 
        provide popup to add/insert new item 
--}}
@include('cspot.snippets.add_item_modal')



<script>
    $(document).ready(function() {

        cSpot.presentationType = '{{ $type }}';
        
    });
</script>

