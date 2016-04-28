
<div class="modal fade help-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Use your keyboard to:</h4>
            </div>
            <div class="modal-body text-x s-center">
                <p><kbd>Esc</kbd> go back to plan overview</p>
                <p><kbd> <i class="fa fa-arrow-right"> </i></kbd> go to next plan item</p>
                <p><kbd> <i class="fa fa-arrow-left"> </i></kbd> go to previous plan item</p>
                <p><kbd>1</kbd>, <kbd>2</kbd>, <kbd>3</kbd>... jump to verse 1...n</p>
                <p><kbd>c</kbd> jump to chorus</p>
                <p><kbd>b</kbd> jump to bridge</p>
                On <strong>tablets</strong> or <strong>phones</strong>, you should instead use the buttons provided at the bottom of this screen!
            </div>
        </div>
    </div>
</div>


    <nav class="navbar navbar-fixed-bottom bg-primary center p-b-0 p-t-0">

        <ul class="nav navbar-nav pull-xs-right">
            <li>
                <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/next/'.$type) }}"
                    class="nav-item btn btn-sm btn-warning" role="button" id="go-next-item">
                    <i class="fa fa-angle-double-right fa-lg"></i>
                </a>
            </li>
        </ul>

        <span class="nav navbar-nav center">
            <small>{{$item->seq_no}} </small>
            @if ($item->song_id && $item->song->title)
                {{ $item->song->title }}
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
                Go to
            </button>
            <div class="dropdown-menu dropdown-menu-right bg-faded">
                @foreach ($items as $menu_item)
                    <a class="dropdown-item nowrap 
                        @if ($item->id == $menu_item->id)
                            bg-info
                        @endif
                        "
                        href="{{ url('cspot/items/').'/'.$menu_item->id.'/'.$type }}">
                        <small class="hide-sm-down">{{ $menu_item->seq_no }}</small> &nbsp; 
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



        <!-- 
            Youtube button 
        -->
        <div class="btn-group pull-xs-right m-r-1">
            @if ($item->song_id && $item->song->youtube_id)
                <a href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}" 
                    target="new" class="pull-xs-right btn btn-sm btn-info">
                <i class="red fa fa-youtube-play fa-lg"></i>&nbsp;</a>
            @else
                <a href="#" disabled=""
                   class="pull-xs-right btn btn-sm btn-secondary-outline">
                <i class="fa fa-youtube-play fa-lg"></i>&nbsp;</a>
            @endif
        </div>

        <a href="#" title="help and keyboard shortcuts" data-toggle="modal" data-target=".help-modal"
            class="pull-xs-right btn btn-sm btn-success-outline m-r-1">
        <i class="fa fa-question-circle fa-lg"></i></a>



        <!-- 
            go to first/last slide 
        -->
        <a href="{{ url('cspot/items/').'/'.$item->plan->firstItem()->id.'/'.$type }}" id="go-first-item"></a>
        <a href="{{ url('cspot/items/').'/'.$item->plan->lastItem()->id.'/'.$type  }}" id="go-last-item" ></a>

        <ul class="nav navbar-nav pull-xs-left">
            <li>
                <!-- go to previous slide -->
                <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/previous/'.$type) }}"
                    class="nav-item btn btn-sm btn-warning" role="button" id="go-previous-item">
                    <i class="fa fa-angle-double-left fa-lg"></i>
                </a> 

                <!-- decrease font size -->
                <a href="#" onclick="decFontSize('.text-song');" 
                        title="decrease font size" style="display: none"
                        class="nav-item btn btn-sm btn-info edit-show-buttons" role="button">
                    A <i class="fa fa-minus"></i>
                </a>
                <!-- increase font size -->
                <a href="#" onclick="incFontSize('.text-song');" 
                        title="increase font size" style="display: none"
                        class="nav-item btn btn-sm btn-info edit-show-buttons" role="button">
                    A <i class="fa fa-plus"></i>
                </a>
                <!-- swap between chords and sheetmusic -->
                <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/swap/'.$type) }}" 
                        style="display: none" id="show-chords-or-music"
                        title="swap between chords and sheetmusic"
                        class="nav-item btn btn-sm btn-warning edit-show-buttons" role="button">
                    <i class="fa fa-file-text"></i> <i class="fa fa-refresh fa-lg"></i> <i class="fa fa-music"></i>
                </a>
            </li>
        </ul>


        <!-- 
            DropUP Menu "Go to..."
        -->
        <div class="btn-group dropup pull-xs-left m-l-1" id="jumplist" style="display: none">

            <button type="button" class="btn btn-sm btn-info dropdown-toggle" 
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Jump to
            </button>
            <div class="dropdown-menu dropdown-menu-right bg-faded">
                @foreach (range(1,7) as $num)
                    <a class="dropdown-item" href="#verse{{ $num }}" 
                            id="jump-verse{{ $num }}" style="display: none">
                        Verse {{ $num }}</a>
                @endforeach
                <a class="dropdown-item" href="#chorus" id="jump-chorus" style="display: none">Chorus</a>
                <a class="dropdown-item" href="#bridge" id="jump-bridge" style="display: none">Bridge</a>
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
        });
    </script>

