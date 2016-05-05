
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('setup', 'active')



@section('content')


	@include('layouts.flashing')

	@if( Auth::user()->isEditor() )
		<a class="btn btn-primary-outline pull-xs-right" href='{{ url('admin/instruments/create') }}'>
			<i class="fa fa-plus"> </i> &nbsp; Add a new instrument
		</a>
	@endif

    <h2>
    	{{ $heading }}
	</h2>


	@if (count($instruments))

		<table class="table table-striped table-bordered 
					@if(count($instruments)>15)
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
	        @foreach( $instruments as $instrument )
				<tr class="link" onclick="location.href='{{ url('/admin/instruments/' . $instrument->id) }}/edit'">
					<td scope="row">{{ $instrument->id }}</td>
					<td>{{ ucfirst($instrument->name) }}</td>
					<td onclick="location.href='{{ url('admin/instruments/'.$instrument->id) }}'" class="link" title="Show users with that instrument">
						{{ $instrument->users->count() }}</td>
					<td class="nowrap">
						<a class="btn btn-secondary btn-sm" title="Show Users" href='{{ url('admin/instruments/'.$instrument->id) }}'><i class="fa fa-filter"></i></a>
						 @if( Auth::user()->isEditor() )
							<a class="btn btn-primary-outline btn-sm" title="Edit" href='{{ url('admin/instruments/'.$instrument->id) }}/edit'><i class="fa fa-pencil"></i></a>
							<a class="btn btn-danger btn-sm" title="Delete!" href='{{ url('admin/instruments/'.$instrument->id) }}/delete'><i class="fa fa-trash"></i></a>
						@endif
					</td>
				</tr>
	        @endforeach
			</tbody>
		</table>

    @else

    	No instruments found! Add one ...

	@endif

	
@stop
