
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('setup', 'active')



@section('content')


	@include('layouts.flashing')

	@if( Auth::user()->isEditor() )
		<a class="btn btn-outline-primary float-right" href='{{ url('admin/bibleversions/create') }}'>
			<i class="fa fa-plus"> </i> &nbsp; Add a new Version
		</a>
	@endif

    <h2>
    	{{ $heading }}
	</h2>


	@if (count($bibleversions))

		<table class="table table-striped table-bordered 
					@if(count($bibleversions)>15)
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
	        @foreach( $bibleversions as $bibleversion )
				<tr class="link" onclick="location.href='{{ url('/admin/bibleversions/' . $bibleversion->id) }}/edit'">
					<td scope="row">{{ $bibleversion->id }}</td>
					<td>{{ $bibleversion->name }}</td>
					<td class="nowrap">
						<a class="btn btn-secondary btn-sm" title="Show Bible" href='{{ url('admin/bibleversions/'.$bibleversion->id) }}'><i class="fa fa-book"></i></a>
						 @if( Auth::user()->isEditor() )
							<a class="btn btn-outline-primary btn-sm" title="Edit" href='{{ url('admin/bibleversions/'.$bibleversion->id) }}/edit'><i class="fa fa-pencil"></i></a>
							<a class="btn btn-danger btn-sm" title="Delete!" href='{{ url('admin/bibleversions/'.$bibleversion->id) }}/delete'><i class="fa fa-trash"></i></a>
						@endif
					</td>
				</tr>
	        @endforeach
			</tbody>
		</table>

    @else

    	No bibleversions found! Add one ...

	@endif

	
@stop
