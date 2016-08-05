
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('resources', 'active')



@section('content')


	@include('layouts.flashing')

	@if( Auth::user()->isEditor() )
		<a class="btn btn-outline-primary pull-xs-right" href='{{ url('admin/resources/create') }}'>
			<i class="fa fa-plus"> </i> &nbsp; Add a new resource
		</a>
	@endif

    <h2>
    	{{ $heading }}
    	<small class="text-muted">
    		<a tabindex="0" href="#"
    			data-container="body" data-toggle="popover" data-placement="bottom" data-trigger="focus"
    			data-content="Resources can be used in service plans. It could be rooms, gadgets or other stuff.">
    			<i class="fa fa-question-circle"></i></a>
		</small>
	</h2>


	@if (count($resources))

		<table class="table table-striped table-bordered 
					@if(count($resources)>15)
					 table-sm
					@endif
					 ">
			<thead class="thead-default">
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Type</th>
					<th>Details</th>
					<th>No. of Plans</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
	        @foreach( $resources as $resource )
				<tr class="link" onclick="location.href='{{ url('/admin/resources/' . $resource->id) }}/edit'">
					<td scope="row">{{ $resource->id }}</td>
					<td>{{ ucfirst($resource->name) }}</td>
					<td>{{ $resource->type }}</td>
					<td>{{ $resource->details }}</td>
					<td onclick="location.href='{{ url('admin/resources/'.$resource->id) }}'" class="link" title="Show users with that resource">
						{{ $resource->plans->count() }}</td>
					<td class="nowrap">
						<a class="btn btn-secondary btn-sm" title="Show Plans" href='{{ url('admin/resources/'.$resource->id) }}'><i class="fa fa-filter"></i></a>
						 @if( Auth::user()->isEditor() )
							<a class="btn btn-outline-primary btn-sm" title="Edit" href='{{ url('admin/resources/'.$resource->id) }}/edit'><i class="fa fa-pencil"></i></a>
							<a class="btn btn-danger btn-sm" title="Delete!" href='{{ url('admin/resources/'.$resource->id) }}/delete'><i class="fa fa-trash"></i></a>
						@endif
					</td>
				</tr>
	        @endforeach
			</tbody>
		</table>

    @else

    	No resources found!

	@endif

	
@stop
