
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('users', 'active')



@section('content')


	@include('layouts.flashing')


	{{-- 	-	-	-	-	SONGS LIST Navigation Bar 
	--}}

	<nav class="navbar navbar-light bg-faded">

		<a class="navbar-brand bg-info px-1 rounded hidden-md-down big" href="#">{{ $heading }}</a>
		<a class="navbar-brand bg-info px-1 rounded hidden-sm-down hidden-lg-up" href="#">{{ $heading }}</a>

		@if( Auth::user()->isAdmin() )
			<a class="btn btn-outline-primary float-xs-right ml-1" href="{{ url('admin/users/create') }}">
				<i class="fa fa-user-plus"> </i> &nbsp; Add a user
			</a>
		@endif



		<form class="form-inline float-xs-right ml-1">
			<div class="form-group">
				<label for="instrumentfilter">Users playing</label>
				<select class="custom-select" id="instrumentfilter" onchange="location.href='{{url('admin/users')}}?filterby=instrument&filtervalue='+$(this).val()">
					<option {{ Request::has('filterby') && Request::get('filterby')=='instrument' ? '' : 'selected' }} value="all">instrument</option>
					@foreach ($instruments as $instrument)
						<option 
							{{ (Request::has('filterby') && Request::get('filterby')=='instrument' && Request::get('filtervalue')==$instrument->id) ? 'selected' : '' }} 
							value="{{$instrument->id}}">{{$instrument->name}}</option>
					@endforeach
				</select>
			</div>
		</form>


		<form class="form-inline float-xs-right">
			<div class="form-group">
				<label for="rolefilter">Show only</label>
				<select class="custom-select" id="rolefilter" onchange="location.href='{{url('admin/users')}}?filterby=role&filtervalue='+$(this).val()">
					<option {{ Request::has('filterby') && Request::get('filterby')=='role' ? '' : 'selected' }} value="all">(select role)</option>
					@foreach ($roles as $role)
						<option 
							{{ (Request::has('filterby') && Request::get('filterby')=='role' && Request::get('filtervalue')==$role->id) ? 'selected' : '' }} 
							value="{{$role->id}}">{{$role->name}}</option>
					@endforeach
				</select>
			</div>
		</form>




		<button type="button" class="btn btn-outline-primary"
			 onclick="location.href='{{ url('/admin/users' . (Request::has('active') ? '' : '?active=active')) }}'">
			Show
			{{ Request::has('active') ? 'all' : 'only active' }}
			users
		</button>

	</nav>



	<table class="table table-striped table-bordered 
				@if(count($users)>15)
				 table-sm
				@endif
				 ">


		<thead class="thead-default">
			<tr>

				@include('cspot.snippets.theader', ['thfname' => 'id', 'thdisp' => '#', 'thsearch'=>false, 'thclass'=>''])

				<th>First Name</th>

				<th class="hidden-lg-down">Last Name</th>

				@include('cspot.snippets.theader', ['thfname' => 'name', 'thdisp' => 'Display Name', 'thsearch'=>false, 'thclass'=>'hidden-md-down'])

				@if ( Auth::user()->isEditor() )
					<th class="hidden-sm-down">Email</th>
				@endif
				<th>Role(s)&nbsp; <a href="{{ route('roles.index') }}" title="Manage Roles"> &#128393; </a></th>

				<th>Instrument(s)&nbsp; <a href="{{ route('instruments.index') }}" title="Manage Instruments"> &#128393; </a></th>

				<th class="small center" title="involved in # of events as leader">Leader</th>

				<th class="small center" title="involved in # of events as teacher">Teacher</th>

				@if (Auth::user()->isAdmin())
					@include('cspot.snippets.theader', ['thfname' => 'last_access', 'thdisp' => 'Last Access', 'thsearch'=>false, 'thclass'=>'hidden-md-down center'])

					@include('cspot.snippets.theader', ['thfname' => 'last_login', 'thdisp' => 'Last Login', 'thsearch'=>false, 'thclass'=>'hidden-md-down center'])
				@endif

				@include('cspot.snippets.theader', ['thfname' => 'created_at', 'thdisp' => 'Joined', 'thsearch'=>false, 'thclass'=>'hidden-lg-down center'])

				<th> </th>
			</tr>
		</thead>



		<tbody>


        @foreach( $users as $user )

			<?php 
				$tdl = '';
				if (Auth::user()->isAdmin())
						$tdl = 'onclick="location.href =' . url('admin/users/'.$user->id) . '/edit';
					?>
			<tr>
				<th {{$tdl}} class="link" scope="row">{{ $user->id }}</th>


				<td {{$tdl}} class="link">{{ $user->first_name }}</td>


				<td {{$tdl}} class="link hidden-lg-down">{{ $user->last_name }}</td>


				<td {{$tdl}} class="link hidden-md-down">{{ $user->name  }}</td>


				@if ( Auth::user()->isEditor() )
					<td {{$tdl}} class="link hidden-sm-down small">{{ $user->email }}</td>
				@endif


				<td {{$tdl}} class="link small">@foreach ($user->roles as $key => $role)
                	{{ ucfirst($role->name) }}{{ $key+1<$user->roles->count() ? ',' : '' }}
					@endforeach</td>


				<td {{$tdl}} class="link small">@foreach ($user->instruments as $key => $instrument)
                	{{ ucfirst($instrument->name) }}{{ $key+1<$user->instruments->count() ? ',' : '' }}
					@endforeach</td>


				<td class="center">
					@if ($user->plans_as_leader->count())
						<a href="{{ url('cspot/plans?filterby=user&filtervalue='.$user->id) }}&show=all"
                            title="show all plans of this user as leader">{{ $user->plans_as_leader->count() }}</a>
                    @else
                    	{{ $user->plans_as_leader->count() }}
                    @endif
            	</td>


				<td class="center">
					@if ($user->plans_as_teacher->count())
						<a href="{{ url('cspot/plans?filterby=user&filtervalue='.$user->id) }}&show=all"
                            title="show all plans of this user as teacher">{{ $user->plans_as_teacher->count() }}</a>
                    @else
                    	{{ $user->plans_as_teacher->count() }}
                    @endif
                </td>


				@if (Auth::user()->isAdmin())

					<td class="hidden-md-down small center" title = "{{ $user->last_access }}">{{ 
						( $user->last_access && $user->last_access->ne(Carbon\Carbon::create(0,0,0,0,0,0)) )
							? $user->last_access->diffForHumans() 
							: 'never'
						}}</td>

					<td class="hidden-md-down small center" title = "{{ $user->last_login }}">{{ 
						( $user->last_login && $user->last_login->ne(Carbon\Carbon::create(0,0,0,0,0,0)) )
							? $user->last_login->diffForHumans() 
							: 'never'
						}}</td>

				@endif


				<td class="hidden-lg-down small center">{{ 
					( $user->created_at && $user->created_at->ne(Carbon\Carbon::create(0,0,0,0,0,0)) )
						? $user->created_at->formatLocalized('%d-%b-%y %H:%M') 
						: 'n/a'
					}}</td>


				<td class="nowrap">
					@if( Auth::user()->isAdmin() || (Auth::user()->isEditor() && $user->id > 1) )
						<a class="btn btn-outline-primary btn-sm hidden-lg-down" title="Edit" href='{{ url('admin/users/'. $user->id) }}/edit'  ><i class="fa fa-pencil"></i></a>
						@if(  Auth::user()->isAdmin()  &&  $user->id > 1  &&  !$user->plans_as_leader->count() &&  !$user->plans_as_teacher->count()  )
							<a class="btn btn-danger  btn-sm" 	   title="Delete!" href='{{ url('admin/users/'. $user->id) }}/delete'><i class="fa fa-trash" ></i></a>
						@endif
					@endif
					@if( $user->hasRole('teacher') || $user->hasRole('leader') )
						<a class="btn btn-secondary btn-sm" title="Show Upcoming Plans" href="{{ url('cspot/plans?filterby=user&filtervalue='.$user->id) }}&show=future"><i class="fa fa-filter"></i></a>					@endif
				</td>
			</tr>

        @endforeach

		</tbody>

	</table>


	
@stop
