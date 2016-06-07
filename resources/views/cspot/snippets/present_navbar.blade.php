
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


<nav class="navbar navbar-fixed-bottom bg-primary center p-b-0 p-t-0">

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
            <small class="hidden-xs-down">{{ $item->song->title }}</small>
        @else
            {{ $item->comment }}
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
                <a class="dropdown-item nowrap 
                    @if ($item->id == $menu_item->id)
                        bg-info
                    @endif
                    @if (! $menu_item->song_id)
                        hidden-md-down
                    @endif
                    "
                    onclick="$('#show-spinner').modal({keyboard: false});" 
                    href="{{ url('cspot/items/').'/'.$menu_item->id.'/'.$type }}">
                    <small class="hidden-xs-down">{{ $menu_item->seq_no }} &nbsp;</small> 
                    @if ($menu_item->song_id && $menu_item->song->title)
                        <i class="fa fa-music">&nbsp;</i><strong>{{ $menu_item->song->title }}</strong>
                    @else
                        {{ $menu_item->comment }}
                    @endif
                </a>
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
            <a class="dropdown-item" id="go-back"
                    onclick="$('#show-spinner').modal({keyboard: false});" 
                href="{{ url('cspot/plans/'.$item->plan_id) }}">
                <i class="fa fa-undo"></i>
                Back to plan overview
            </a>
        </div>

    </div>



    <!-- 
        Youtube button 
    -->
    <div class="btn-group pull-xs-right m-r-1">
        @if ($item->song_id && $item->song->youtube_id)
            <a href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}" 
                target="new" class="pull-xs-right btn btn-sm btn-info hidden-sm-down ">
            <i class="red fa fa-youtube-play fa-lg"></i>&nbsp;</a>
        @else
            <a href="#" disabled="" 
               class="pull-xs-right btn btn-sm btn-secondary-outline hidden-lg-down">
            <i class="fa fa-youtube-play fa-lg"></i>&nbsp;</a>
        @endif
    </div>

    <!-- 
        help button 
    -->
    <a href="#" title="show keyboard shortcuts" data-toggle="modal" data-target=".help-modal"
        class="hidden-sm-down pull-xs-right btn btn-sm btn-success-outline m-r-1">
    <i class="fa fa-question-circle fa-lg"></i></a>



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

<script>
    $(document).ready(function() {
        // check if user has changed the default font size for the presentation
        fontSize = localStorage.getItem('.text-song_font-size');
        if (fontSize) {
            $('.text-song').css('font-size', parseInt(fontSize));
        }

        // make sure the main content covers all the display area
        $('#main').css('min-height', window.screen.height);

        // intercept mouse clicks into the presentation area
        $('body').contextmenu( function() {
            return false;
        });

        // Allow mouse click (or finger touch) to move forward
        $('#main').click(function(){
            navigateTo('next-item');
        });
        // allow rght-mouse-click to move one slide or item back
        $('#main').on('mouseup', function(event){
            if (event.which == 3) {
                event.preventDefault();
                navigateTo('previous-item');
            }
        });
    });
</script>

