
<div class="row">

    <div class="col-sm-3 col-md-2 sidebar">
        <ul class="nav nav-sidebar">
            <li class="@yield('tasks') ">
                <a href="/tasks">{{ (Auth::user()->id===1 || Auth::user()->is_admin) ? 'All' : 'Your' }} Tasks
                <span class="sr-only">(current)</span></a>
            </li>
            @if(!Auth::guest())
            <li class="@yield('create'    )"><a href="/tasks/create">New Task</a></li>
            @endif
            <li class="@yield('roles')"><a href="/admin/roles">User Roles</a></li>
            @if(Auth::user()->id===1 || Auth::user()->isAdmin())
            <li class="@yield('users'     )"><a href="/admin/users">Users</a></li>
            @endif
            </ul>
    </div>

    
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
