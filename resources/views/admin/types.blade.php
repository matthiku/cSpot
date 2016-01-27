@extends('layouts.main')

@section('title', $heading)

@section('types', 'active')



@section('content')


	@include('layouts.flashing')

    <h2>{{ $heading }}</h2>


	@if (count($types))

		<table class="table table-striped table-bordered 
					@if(count($types)>5)
					 table-sm
					@endif
					 ">
			<thead class="thead-default">
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>No. of Plans</th>
					 @if(Auth::user()->id===1 || Auth::user()->isAdmin())
						<th>Action</th>
					@endif
				</tr>
			</thead>
			<tbody>
	        @foreach( $types as $type )
				<tr>
					<th scope="row">{{ $type->id }}</th>
					<td>{{ $type->name }}</td>
					<td onclick="location.href='/admin/types/{{$type->id}}'" class="link" title="Show all Plans of this type of Service">
						{{ $type->plans->count() }}</td>
					<td>
						<a class="btn btn-secondary btn-sm" title="Show upcoming Plans" href='/cspot/plans/by_type/{{$type->id}}'><i class="fa fa-filter"></i></a>
						 @if( Auth::user()->isEditor() )
						<a class="btn btn-primary-outline btn-sm" title="Edit" href='/admin/types/{{$type->id}}/edit'><i class="fa fa-pencil"></i></a>
						<a class="btn btn-danger btn-sm" title="Delete!" href='/admin/types/{{$type->id}}/delete'><i class="fa fa-trash"></i></a>
						@endif
					</td>
				</tr>
	        @endforeach
			</tbody>
		</table>

    @else

    	No types found!

	@endif

	@if(Auth::user()->isEditor())
	<a class="btn btn-primary-outline" href='/admin/types/create'>
		<i class="fa fa-plus"> </i> &nbsp; Add a new type
	</a>
	@endif

	
@stop
