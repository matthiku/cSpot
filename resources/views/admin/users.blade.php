@extends('layouts.main')

@section('title', $heading)

@section('users', 'active')



@section('content')

	@include('layouts.sidebar')

	@include('layouts.flashing')

    <h2>{{ $heading }}</h2>


	@if (count($users))

		<table class="table table-striped table-bordered 
					@if(count($users)>5)
					 table-sm
					@endif
					 ">
			<thead class="thead-default">
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Username</th>
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
					<td>{{ $user->name }}</td>
					<td>{{ $user->username }}</td>
					<td>{{ $user->email }}</td>
					<td>
						@foreach ($user->roles as $role)
							{{ $role->name }},
						@endforeach
					</td>
					<td>{{ $user->created_at }}</td>
					<td>{{ $user->provider }}</td>
					<td>{{ $user->provider_id }}</td>
					<td>
						<!-- <a class="btn btn-secondary btn-sm" title="Show Tasks" href='tasks/user/{{$user->id}}'><i class="fa fa-filter"></i></a> -->
						<a class="btn btn-primary-outline btn-sm" title="Edit" href='/admin/users/{{$user->id}}/edit'  ><i class="fa fa-pencil"></i></a>
						<a class="btn btn-danger  btn-sm" 	   title="Delete!" href='/admin/users/{{$user->id}}/delete'><i class="fa fa-trash" ></i></a>
					</td>
				</tr>
	        @endforeach
			</tbody>
		</table>

    @else

    	No users found!

	@endif

	<a class="btn btn-primary-outline" href='/admin/users/create'>
		<i class="fa fa-plus"> </i> &nbsp; Add a user
	</a>

	
@stop
