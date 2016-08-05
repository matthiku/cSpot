
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('roles', 'active')



@section('content')


	@include('layouts.flashing')

	@if( Auth::user()->isEditor() )
		<a class="btn btn-outline-primary pull-xs-right" href='{{ url('admin/roles/create') }}'>
			<i class="fa fa-plus"> </i> &nbsp; Add a new role
		</a>
	@endif

    <h2>
    	{{ $heading }}
    	<small class="text-muted">
    		<a tabindex="0" href="#"
    			data-container="body" data-toggle="popover" data-placement="bottom" data-trigger="focus"
    			data-content="User roles determine what users can do and whether they will be listed as teachers or leaders on a new service plan.">
    			<i class="fa fa-question-circle"></i></a>
		</small>
	</h2>


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
					<th>No. of Users</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
	        @foreach( $roles as $role )
				<tr class="link" onclick="location.href='{{ url('/admin/roles/' . $role->id) }}/edit'">
					<td scope="row">{{ $role->id }}</td>
					<td>{{ ucfirst($role->name) }}</td>
					<td onclick="location.href='{{ url('admin/roles/'.$role->id) }}'" class="link" title="Show users with that role">
						{{ $role->users->count() }}</td>
					<td class="nowrap">
						<a class="btn btn-secondary btn-sm" title="Show Users" href='{{ url('admin/roles/'.$role->id) }}'><i class="fa fa-filter"></i></a>
						 @if( $role->id>3 &&   Auth::user()->isEditor() )
							<a class="btn btn-outline-primary btn-sm" title="Edit" href='{{ url('admin/roles/'.$role->id) }}/edit'><i class="fa fa-pencil"></i></a>
							<a class="btn btn-danger btn-sm" title="Delete!" href='{{ url('admin/roles/'.$role->id) }}/delete'><i class="fa fa-trash"></i></a>
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

	
@stop
