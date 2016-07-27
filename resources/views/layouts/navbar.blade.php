
<nav id="main-navbar" class="tek-nav navbar navbar-dark bg-inverse navbar-full">

    <ul class="nav navbar-nav pull-xs-right">

        <!-- 
            RIGHT - Authentication Links 
        -->
        @if (Auth::guest())

            <li class="nav-item"><a class="nav-link" href="{{ url('login') }}">Sign in</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('register') }}">Register</a></li>

        @else

            <!-- show unread messages count -->
            <?php 
                $count = Auth::user()->newThreadsCount(); 
            ?>
            @if($count > 0)
               <li class="nav-item" title="You have new mail!">
                    <a class="nav-link mail-alert bg-danger" href="{{URL::to('messages')}}">{!! $count !!}</a>
                </li>
                <script>blink($('.mail-alert'))</script>
            @endif

            <li class="nav-item hidden-xs-down">
                <a class="nav-link" href="#" data-toggle="modal" data-target="#createMessage">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i> 
                    Page Feedback</a>
            </li>

            <li class="nav-item hidden-lg-down">
                <a class="nav-link" href="{{ url('admin/users') }}">User List</a>
            </li>

            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle{{ Request::is('admin/*') ? ' active' : '' }}" 
                   data-toggle="dropdown" role="button" aria-expanded="false">
                    <i class="fa fa-cogs"></i> <span class="caret"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" role="menu">
                    <a class="dropdown-item" href="{{ url('admin/default_items') }}"><i class="fa fa-btn fa-server fa-lg"></i> &nbsp; Default Items</a>
                    <a class="dropdown-item" href="{{ url('cspot/files') }}"><i class="fa fa-btn fa-clone fa-lg"></i> &nbsp; Files/Images</a>
                    <a class="dropdown-item" href="{{ url('admin/file_categories') }}"><i class="fa fa-btn fa-file-archive-o fa-lg"></i> &nbsp; File Categories</a>
                    <hr>
                    <a class="dropdown-item" href="{{ url('admin/users') }}"><i class="fa fa-btn fa-users fa-lg"></i> &nbsp; User List</a>
                    <a class="dropdown-item" href="{{ url('admin/roles') }}"><i class="fa fa-btn fa-check-square-o fa-lg"></i> &nbsp; User Roles</a>
                    <a class="dropdown-item" href="{{ url('admin/instruments') }}"><i class="fa fa-btn fa-music fa-lg"></i> &nbsp; User Instruments</a>
                    @if (Auth::user()->isAdmin())
                        <hr>
                        <a target="_new" class="dropdown-item" href="{{ url('admin/logs')  }}">
                            <i class="fa fa-btn fa-file-zip-o fa-lg"></i> &nbsp; Laravel Logs</a>
                        <a class="dropdown-item" href="{{ url('admin/runjob/batch')  }}">
                            <i class="fa fa-btn fa-cubes"></i> &nbsp; Run Batch Job(s)</a>
                        <a class="dropdown-item" href="{{ url('admin/customize')  }}">
                            <i class="fa fa-btn fa-cog fa-lg"></i> &nbsp; Customization</a>
                    @endif
                </div>
            </li>

            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle " 
                   data-toggle="dropdown" role="button" aria-expanded="false">
                    {{ Auth::user()->first_name }}
                    <span class="caret"></span>
                </a>

                <div class="dropdown-menu dropdown-menu-right" role="menu">
                    <a class="dropdown-item" href="{{ url('admin/users/'.Auth::user()->id) }}">
                        <i class="fa fa-btn fa-user fa-lg"></i>
                        Profile</a>
                    <hr>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#createMessage">
                        <i class="fa fa-btn fa-pencil-square-o fa-lg"></i>
                        Page Feedback</a>
                    <a class="dropdown-item" href="{{ URL::to('messages/create') }}">
                        <i class="fa fa-btn fa-pencil-square-o fa-lg"></i>
                        New Message</a>
                    <a class="dropdown-item" href="{{ URL::to('messages') }}">
                        <i class="fa fa-btn fa-inbox fa-lg"></i>
                        Messages @include('messenger.unread-count')</a>
                    <hr>
                    <a class="dropdown-item" href="{{ url('logout') }}">
                        <i class="fa fa-btn fa-sign-out fa-lg"></i>
                        Logout</a>
                </div>
                
            </li>

        @endif

    </ul>

    <!--  
        RIGHT - Login form
    -->
    @if ( Auth::guest() )
        <form class="form-inline pull-xs-right hidden-md-down" method="POST" role="form" action="{{ url('login') }}">
            Log in using @include('auth.social', ['hideLblText' => 'true']) or: 
            {!! csrf_field() !!}
            <div class="form-group">
                <input type="email" name="email" class="form-control-sm" id="inputEmail" placeholder="Enter email">
            </div>
            <div class="form-group small-pw-input">
                <input type="password" name="password" class="form-control-sm small-pw-input" id="inputPassword" placeholder="Password">
            </div>
            <div class="checkbox hidden-xs-up">
                <label>
                    <input type="checkbox" name="remember" checked="checked"> Remember me
                </label>
            </div>
            <button type="submit" class="btn btn-sm btn-primary">Sign in</button> &nbsp; &nbsp; &nbsp;
        </form>
    @endif


    <!-- 
        LEFT - Home button
     -->
    <a class="navbar-brand" href="{{ Auth::guest() ? url('.') : url('home') }}">
        <img src="{{ url('images/xs-cspot.png') }}" height="20" width="30"></a>



    <!-- 
        LEFT - Main menu items
     -->
    <ul class="nav navbar-nav">
        @if (Auth::user())
        <li class="nav-item dropdown {{ Request::is('cspot/plans*') ? 'active' : '' }}">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                Planning <span class="caret"></span>
            </a>
            <div class="dropdown-menu" role="menu">
                <a class="dropdown-item {{ Request::is('cspot/plans/next') ? 'active' : '' }}" href="{{ url('cspot/plans/next') }}">
                    <i class="fa fa-btn fa-bell-o fa-lg"></i> &nbsp; Next Sunday</a>
                <a class="dropdown-item" href="{{ url('cspot/plans?filterby=future') }}">
                    <i class="fa fa-btn fa-calendar fa-lg"></i> &nbsp; Upcoming Plans</a>
                <hr>
                <a class="dropdown-item {{ Request::is('cspot/plans') ? 'active' : '' }}" href="{{ url('cspot/plans') }}">
                    <i class="fa fa-btn fa-calendar-check-o fa-lg"></i> &nbsp; Your Service Plans</a>
                @if( Auth::user()->isEditor() )
                <a class="dropdown-item" href="{{ url('cspot/plans/create') }}">
                    <i class="fa fa-btn fa-calendar-plus-o fa-lg"></i> &nbsp; Add New Plan</a>
                @endif
                <a class="dropdown-item" href="{{ url('admin/types') }}">
                    <i class="fa fa-btn fa-tasks fa-lg"></i> &nbsp; Service Types</a>
                <hr>
                <a class="dropdown-item" href="{{ url('cspot/songs') }}">
                <i class="fa fa-btn fa-music fa-lg"></i> &nbsp; Songs</a>
            </div>
        </li>
        <li class="nav-item hidden-sm-down">
            <a class="nav-link {{ Request::is('cspot/plans/next') ? 'active' : '' }}" href="{{ url('cspot/plans/next') }}">Next Sunday</a>
        </li>
        <li class="nav-item hidden-sm-down">
            <a class="nav-link" href="{{ url('cspot/plans?filterby=future') }}">Upcoming Plans</a>
        </li>
        <li class="nav-item hidden-lg-down">
            <a class="nav-link {{ Request::is('cspot/plans') ? 'active' : '' }}" href="{{ url('cspot/plans') }}">Your Plans</a>
        </li>
        @endif

        <!-- 
            CENTER - Show church logo and name 
        -->
        <li class="hidden-md-down center">
            <a class="nav-link" target="new" href="{{ env('CHURCH_URL') }}">
                <img src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}" height="20">
                {{ env('CHURCH_NAME') }}
            </a>
        </li>

    </ul>


</nav>
