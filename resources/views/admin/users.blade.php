@extends('layouts.main')

@section('title', $heading)

@section('users', 'active')



@section('content')

	@include('layouts.sidebar')

	@include('layouts.flashing')

    <h2>{{ $heading }}</h2>


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
				<th>Email</th>
				<th>Role(s)</th>
				<th>Joined</th>
				<th>Provider</th>
				<th>Prov.ID</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
        @foreach( $users as $user )
			<tr>
				<th scope="row">{{ $user->id }}</th>
				<td>{{ $user->first_name }}</td>
				<td>{{ $user->last_name }}</td>
				<td>{{ $user->email }}</td>
				<td>@foreach ($user->roles as $role)
						{{ $role->name }},
					@endforeach</td>
				<td>{{ $user->created_at }}</td>
				<td>{{ '' }}</td>
				<td>{{ '' }}</td>
				<td>
					<!-- <a class="btn btn-secondary btn-sm" title="Show Tasks" href='tasks/user/{{$user->id}}'><i class="fa fa-filter"></i></a> -->
					@if( Auth::user()->isAdmin() || (Auth::user()->isEditor() && $user->id > 1) )
						<a class="btn btn-primary-outline btn-sm" title="Edit" href='/admin/users/{{$user->id}}/edit'  ><i class="fa fa-pencil"></i></a>
						@if( Auth::user()->isAdmin() && $user->id > 1 )
							<a class="btn btn-danger  btn-sm" 	   title="Delete!" href='/admin/users/{{$user->id}}/delete'><i class="fa fa-trash" ></i></a>
						@endif
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
