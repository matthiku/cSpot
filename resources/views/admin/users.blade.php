
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('users', 'active')



@section('content')


	@include('layouts.flashing')


	@if( Auth::user()->isAdmin() )
		<a class="btn btn-outline-primary pull-xs-right" href="{{ url('admin/users/create') }}">
			<i class="fa fa-user-plus"> </i> &nbsp; Add a user
		</a>
	@endif

    <h2>{{ $heading }}</h2>

	<p>
		<a href="{{ url('/admin/users' . (Request::is('*/active') ? '' : '/active')) }}">
		<input type="checkbox" {{Request::is('*/active') ? 'checked' : ''}}>
		Show only active users</a>
	</p>

	<table class="table table-striped table-bordered 
				@if(count($users)>15)
				 table-sm
				@endif
				 ">
		<thead class="thead-default">
			<tr>
				<th>#</th>
				<th>First Name</th>
				<th class="hidden-lg-down">Last Name</th>
				<th class="hidden-md-down">Display Name</th>
				@if ( Auth::user()->isEditor() )
					<th class="hidden-sm-down">Email</th>
				@endif
				<th>Role(s)</th>
				<th>Instrument(s)</th>
				<th class="hidden-md-down center">Last Login</th>
				<th class="hidden-lg-down center">Joined</th>
				<th> </th>
			</tr>
		</thead>
		<tbody>
        @foreach( $users as $user )
			<tr @if(Auth::user()->isAdmin())
						class="link" onclick="location.href ='{{ url('admin/users/'.$user->id) }}/edit'"
					@endif
					>					
				<td scope="row">{{ $user->id }}</td>

				<td>{{ $user->first_name }}</td>

				<td class="hidden-lg-down">{{ $user->last_name }}</td>

				<td class="hidden-md-down">{{ $user->name  }}</td>

				@if ( Auth::user()->isEditor() )
					<td class="hidden-sm-down small">{{ $user->email }}</td>
				@endif

				<td class="small">@foreach ($user->roles as $key => $role)
                	{{ ucfirst($role->name) }}{{ $key+1<$user->roles->count() ? ',' : '' }}
					@endforeach</td>

				<td class="small">@foreach ($user->instruments as $key => $instrument)
                	{{ ucfirst($instrument->name) }}{{ $key+1<$user->instruments->count() ? ',' : '' }}
					@endforeach</td>


				<td class="hidden-md-down small center">{{ 
					$user->last_login && $user->last_login->ne(Carbon\Carbon::create(0,0,0,0,0,0))
						? $user->last_login->diffForHumans() 
						: 'never'
					}}</td>

				<td class="hidden-lg-down small center">{{ $user->created_at->formatLocalized('%d-%b-%y %H:%M') }}</td>


				<td class="nowrap">
					@if( Auth::user()->isAdmin() || (Auth::user()->isEditor() && $user->id > 1) )
						<a class="btn btn-outline-primary btn-sm hidden-lg-down" title="Edit" href='{{ url('admin/users/'. $user->id) }}/edit'  ><i class="fa fa-pencil"></i></a>
						@if( Auth::user()->isAdmin() && $user->id > 1 )
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
