
<?php
$modalContent = '
    <p><kbd>Esc</kbd> go back to plan overview</p>
    <p><kbd> <i class="fa fa-arrow-right"> </i></kbd> go to next plan item</p>
    <p><kbd> <i class="fa fa-arrow-left"> </i></kbd> go to previous plan item</p>
    <p><kbd>1</kbd>, <kbd>2</kbd>, <kbd>3</kbd>... jump to verse 1...n</p>
    <p><kbd>c</kbd> jump to chorus</p>
    <p><kbd>b</kbd> jump to bridge</p>
    On <strong>tablets</strong> or <strong>phones</strong>, you should instead use the buttons provided at the bottom of this screen!';
?>

@include( 'cspot/snippets/modal', ['modalContent' => $modalContent, 'modalTitle' => 'Use your keyboard to:' ] )


<nav class="navbar navbar-fixed-bottom bg-primary center p-b-0 p-t-0" id="present-navbar">

    <ul class="nav navbar-nav pull-xs-right">
        <li>
            <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/next/'.$type) }}"
                    onclick="$('#show-spinner').modal({keyboard: false});" 
                class="nav-item btn btn-sm btn-warning" role="button" id="go-next-item">
                <i class="fa fa-angle-double-right fa-lg"></i>
            </a>
        </li>
    </ul>

    <span class="nav navbar-nav center">
        <small class="hidden-sm-down">{{$item->seq_no}} </small>
        @if ($item->song_id && $item->song->title)
            <span class="hidden-xs-down">{{ $item->song->title }}</span>
        @else
            {{ $item->comment }}
        @endif
        @if ($item->id != $item->plan->lastItem()->id)
            <small class="hidden-lg-up hidden-xs-down">(next: {{ substr(getItemTitle($item),0,15) }})</small>
            <small class="hidden-md-down">(up next: {{ getItemTitle($item) }})</small>
        @endif
    </span>

    <!-- 
        DropUP Menu "Go to..."
    -->
    <div class="btn-group dropup pull-xs-right m-r-1">

        <button type="button" class="btn btn-sm btn-info dropdown-toggle" 
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Go<span class="hidden-sm-down"> to</span>
        </button>
        <div class="dropdown-menu dropdown-menu-right bg-faded">
            @foreach ($items as $menu_item)
                @if (! $menu_item->forLeadersEyesOnly)
                    <a class="dropdown-item nowrap 
                        {{ $item->id == $menu_item->id ? 'bg-info' : '' }}
                        {{ ! $menu_item->song_id || $menu_item->song->title_2=='infoscreen' ? 'hidden-md-down' : '' }}
                        {{ count($items) > 15 ? 'dropup-menu-item' : '' }}"
                        onclick="$('#show-spinner').modal({keyboard: false});" 
                        href="{{ url('cspot/items/').'/'.$menu_item->id.'/'.$type }}">
                        <small class="hidden-xs-down">{{ $menu_item->seq_no }} &nbsp;</small> 
                        @if ( $menu_item->song_id && $menu_item->song->title )
                            {!! $menu_item->song->title_2=='infoscreen'
                                ? $menu_item->song->title
                                : '<i class="fa fa-music">&nbsp;</i><strong>'.$menu_item->song->title.'</strong>' 
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
                <a class="dropdown-item hidden-md-up" target="new" 
                    href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}">
                    <i class="red fa fa-youtube-play fa-lg"></i>Play on Youtube
                </a>
            @endif
            <div class="dropdown-divider"></div>
            <a      class="dropdown-item" id="go-back"
                    onclick="$('#show-spinner').modal({keyboard: false});" 
                    href="{{ url('cspot/plans/'.$item->plan_id) }}">
                <i class="fa fa-undo"></i>
                Back to plan overview
                <small class="pull-xs-right">(* = item in local cache)</small>
            </a>
        </div>

    </div>



    <!-- 
        link to song data on CCLI songselect 
    -->
    <div class="btn-group pull-xs-right m-r-1">
        @if ($item->song_id && $item->song->ccli_no)
            <a href="https://songselect.ccli.com/Songs/{{ $item->song->ccli_no }}" 
                target="new" class="pull-xs-right btn btn-sm btn-info hidden-sm-down ">
            <i class="red fa fa-youtube-play fa-lg"></i>&nbsp;</a>
        @else
            <a href="#" disabled="" 
               class="pull-xs-right btn btn-sm btn-outline-secondary hidden-lg-down">
            <i class="fa fa-youtube-play fa-lg"></i>&nbsp;</a>
        @endif
    </div>


    <!-- 
        help button 
    -->
    <a href="#" title="show keyboard shortcuts" data-toggle="modal" data-target=".help-modal"
        class="hidden-sm-down pull-xs-right btn btn-sm btn-outline-success m-r-1">
    <i class="fa fa-question-circle fa-lg"></i></a>


    <!-- 
        Personal Notes 
    -->
    <div class="dropup hidden-xs-down pull-xs-right m-r-1">
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
        <div class="btn-group dropup hidden-xs-down pull-xs-right m-r-1">
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


    <!-- 
        go to first/last slide 
        (invisible, only used for keyboard shortcuts 'Home' and 'End')
    -->
    <a href="{{ url('cspot/items/').'/'.$item->plan->firstItem()->id.'/'.$type }}" id="go-first-item"></a>
    <a href="{{ url('cspot/items/').'/'.$item->plan->lastItem()->id.'/'.$type  }}" id="go-last-item" ></a>


    <!-- 
        LEFT part of navbar 
    -->
    <ul class="nav navbar-nav pull-xs-left">
        <li>
            <!-- go to previous slide -->
            <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/previous/'.$type) }}"
                    onclick="$('#show-spinner').modal({keyboard: false});" 
                class="nav-item btn btn-sm btn-warning" role="button" id="go-previous-item">
                <i class="fa fa-angle-double-left fa-lg"></i>
            </a> 

            <!-- decrease font size -->
            <a href="#" onclick="changeFontSize('.text-song', 'decrease');" id="decr-font"
                    title="decrease font size" style="display: none" 
                    class="hidden-sm-down nav-item btn btn-sm btn-info edit-show-buttons" role="button">
                A <i class="fa fa-minus"></i>
            </a>
            <!-- increase font size -->
            <a href="#" onclick="changeFontSize('.text-song');" id="incr-font"
                    title="increase font size" style="display: none" 
                    class="hidden-sm-down nav-item btn btn-sm btn-info edit-show-buttons" role="button">
                A <i class="fa fa-plus"></i>
            </a>
            <!-- swap between chords and sheetmusic -->
            <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/swap/'.$type) }}" 
                    style="display: none" id="show-chords-or-music"
                    onclick="$('#show-spinner').modal({keyboard: false});" 
                    title="swap between chords and sheetmusic"
                    class="hidden-sm-down nav-item btn btn-sm btn-warning edit-show-buttons" role="button">
                <i class="fa fa-file-text"></i> <i class="fa fa-refresh fa-lg"></i> <i class="fa fa-music"></i>
            </a>

            @if( env('PRESENTATION_ENABLE_SYNC', 'false') )
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

        </li>
    </ul>


    <!-- 
        DropUP Menu "Show"
    -->
    <div class="btn-group dropup pull-xs-left m-l-1" id="jumplist">

        <button type="button" class="btn btn-sm btn-info dropdown-toggle" 
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Show
        </button>

        <div class="dropdown-menu dropdown-menu-left bg-faded">
            @foreach (range(1,7) as $num)
                <a class="dropdown-item" href="#verse{{ $num }}" 
                        id="jump-verse{{ $num }}" style="display: none">
                    Verse {{ $num }}</a>
            @endforeach
            <a class="dropdown-item" href="#chorus" id="jump-chorus" style="display: none">Chorus</a>
            <a class="dropdown-item" href="#bridge" id="jump-bridge" style="display: none">Bridge</a>
            <div class="hidden-md-up dropdown-divider"></div>                
            <a class="dropdown-item hidden-md-up edit-show-buttons" 
                    href="#" style="display: none"
                    onclick="decFontSize('.text-song');" >
                A <i class="fa fa-minus"></i> decrease font
            </a>
            <a class="dropdown-item hidden-md-up edit-show-buttons" 
                    href="#" style="display: none"
                    onclick="incFontSize('.text-song');" >
                A <i class="fa fa-plus"></i> increase font
            </a>
            <a class="dropdown-item hidden-md-up edit-show-buttons" id="goswap-dropup"
                    href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/swap/'.$type) }}">
                <i class="fa fa-file-text"></i> <i class="fa fa-refresh fa-lg"></i> <i class="fa fa-music"></i>
                sheetmusic/chords
        </div>

    </div>


</nav>



{{-- 
        provide popup to add/insert new item 
--}}
@include('cspot.snippets.add_item_modal')



<script>
    $(document).ready(function() {

        prepareChordsPresentation('{{ $type }}');
        
    });
</script>

