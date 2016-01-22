@extends('layouts.main')

@section('title', $heading)

@section('items', 'active')



@section('content')

	@include('layouts.sidebar')

	@include('layouts.flashing')

    <h2>{{ $heading }}</h2>


	@if (count($default_items))

		<table class="table table-striped table-bordered 
					@if(count($default_items)>15)
					 table-sm
					@endif
					 ">
			<thead class="thead-default">
				<tr>
					<th>#</th>
					<th>Service Type</th>
					<th>Sequence No.</th>
					<th>Text</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
	        @foreach( $default_items as $default_item )
				<tr>
					<th scope="row">{{ $default_item->id }}</th>
					<td>{{ $default_item->type_id.' ('.$default_item->type->name.')'  }}</td>
					<td>{{ $default_item->seq_no }}</td>
					<td>{{ $default_item->text }}</td>
					<td>
						<!-- <a class="btn btn-secondary btn-sm" title="Show Users" href='/admin/default_items/{{$default_item->id}}'><i class="fa fa-filter"></i></a> -->
						 @if( Auth::user()->isEditor() )
							<a class="btn btn-primary-outline btn-sm" title="Edit" href='/admin/default_items/{{$default_item->id}}/edit'><i class="fa fa-pencil"></i></a>
							<a class="btn btn-danger btn-sm" title="Delete!" href='/admin/default_items/{{$default_item->id}}/delete'><i class="fa fa-trash"></i></a>
						@endif
					</td>
				</tr>
	        @endforeach
			</tbody>
		</table>

    @else

    	No default items found!

	@endif

	@if( Auth::user()->isEditor() )
	<a class="btn btn-primary-outline" href='/admin/default_items/create'>
		<i class="fa fa-plus"> </i> &nbsp; Add a new default_item
	</a>
	@endif

	
@stop
