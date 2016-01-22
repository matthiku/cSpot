@extends('layouts.main')

@section('title', $heading)

@section('roles', 'active')



@section('content')

	@include('layouts.sidebar')

	@include('layouts.flashing')

    <h2>{{ $heading }}</h2>


	@if (count($roles))

		<table class="table table-striped table-bordered 
					@if(count($roles)>15)
					 table-sm
					@endif
					 ">
			<thead class="thead-default">
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
	        @foreach( $roles as $role )
				<tr>
					<th scope="row">{{ $role->id }}</th>
					<td>{{ $role->name }}</td>
					<td>
						<a class="btn btn-secondary btn-sm" title="Show Users" href='/admin/roles/{{$role->id}}'><i class="fa fa-filter"></i></a>
						 @if( $role->id>3 &&   Auth::user()->isEditor() )
							<a class="btn btn-primary-outline btn-sm" title="Edit" href='/admin/roles/{{$role->id}}/edit'><i class="fa fa-pencil"></i></a>
							<a class="btn btn-danger btn-sm" title="Delete!" href='/admin/roles/{{$role->id}}/delete'><i class="fa fa-trash"></i></a>
						@endif
						@if( $role->id < 4  &&   Auth::user()->isEditor() )
							<span>(System default roles cannot be modified)</span>
						@endif
					</td>
				</tr>
	        @endforeach
			</tbody>
		</table>

    @else

    	No roles found!

	@endif

	@if( Auth::user()->isEditor() )
	<a class="btn btn-primary-outline" href='/admin/roles/create'>
		<i class="fa fa-plus"> </i> &nbsp; Add a new role
	</a>
	@endif

	
@stop
