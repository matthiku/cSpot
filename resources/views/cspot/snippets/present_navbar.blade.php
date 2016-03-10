

    <nav class="navbar navbar-fixed-bottom bg-primary center p-b-0 p-t-0">

        <ul class="nav navbar-nav pull-xs-right">
            <li>
                <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/next/'.$type) }}"
                    class="nav-item btn btn-warning" role="button" id="go-next-item">
                    <i class="fa fa-angle-double-right fa-lg"></i>
                </a>
            </li>
        </ul>

        <span class="nav navbar-nav big center">
            <small>Item {{$item->seq_no}} -</small>
            @if ($item->song_id && $item->song->title)
                {{ $item->song->title }}
            @else
                {{ $item->comment }}
            @endif
        </span>

        <!-- 
            Dropdown Menu Button
        -->
        <div class="btn-group dropup pull-xs-right m-r-1">

            <button type="button" class="btn btn-info dropdown-toggle" 
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
                    target="new" class="pull-xs-right btn btn-info">
                <i class="red fa fa-youtube-play fa-lg"></i>&nbsp;</a>
            @else
                <a href="#" disabled=""
                   class="pull-xs-right btn btn-secondary-outline">
                <i class="fa fa-youtube-play fa-lg"></i>&nbsp;</a>
            @endif
        </div>



        <!-- 
            go to first/last slide 
        -->
        <a href="{{ url('cspot/items/').'/'.$item->plan->firstItem()->id.'/'.$type }}" id="go-first-item"></a>
        <a href="{{ url('cspot/items/').'/'.$item->plan->lastItem()->id.'/'.$type  }}" id="go-last-item" ></a>


        <ul class="nav navbar-nav pull-xs-left">
            <li>
                <a href="{{ url('cspot/plans/'.$item->plan_id.'/items/'.$item->id.'/go/previous/'.$type) }}"
                    class="nav-link btn btn-warning" role="button" id="go-previous-item">
                    <i class="fa fa-angle-double-left fa-lg"></i>
                </a> 
            </li>
        </ul>

    </nav>

