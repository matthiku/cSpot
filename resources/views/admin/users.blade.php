@extends('layouts.main')

@section('title', $heading)

@section('users', 'active')



@section('content')

	@include('layouts.sidebar')

	@include('layouts.flashing')

    <h2>{{ $heading }}</h2>

	<p>
		<a href="/admin/users{{ Request::is('*/active') ? '' : '/active' }}">
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
				<th>Last Name</th>
				{{ Auth::user()->isEditor() ? '<th>Email</th>' : '' }}
				<th>Role(s)</th>
				<th>Joined</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
        @foreach( $users as $user )
			<tr>
				<th scope="row">{{ $user->id }}</th>
				<td>{{ $user->first_name }}</td>
				<td>{{ $user->last_name }}</td>
				{{ Auth::user()->isEditor() ? '<td>'.$user->email.'</td>' : '' }}
				<td>@foreach ($user->roles as $role)
						{{ ucfirst($role->name) }},
					@endforeach</td>
				<td>{{ $user->created_at }}</td>
				<td>
					@if( Auth::user()->isAdmin() || (Auth::user()->isEditor() && $user->id > 1) )
						<a class="btn btn-primary-outline btn-sm" title="Edit" href='/admin/users/{{$user->id}}/edit'  ><i class="fa fa-pencil"></i></a>
						@if( Auth::user()->isAdmin() && $user->id > 1 )
							<a class="btn btn-danger  btn-sm" 	   title="Delete!" href='/admin/users/{{$user->id}}/delete'><i class="fa fa-trash" ></i></a>
						@endif
					@endif
					@if( $user->hasRole('teacher') || $user->hasRole('leader') )
						<a class="btn btn-secondary btn-sm" title="Show Upcoming Plans" href='/cspot/plans/by_user/{{$user->id}}'><i class="fa fa-filter"></i></a>
					@endif
				</td>
			</tr>
        @endforeach
		</tbody>
	</table>


	@if( Auth::user()->isEditor() )
		<a class="btn btn-primary-outline" href='/admin/users/create'>
			<i class="fa fa-plus"> </i> &nbsp; Add a user
		</a>
	@endif

	
@stop
