
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

		<table class="table table-striped table-bordered{{ count($bibleversions)>15 ? ' table-sm' : '' }}">
			<thead class="thead-default">
				<tr>
					<th>id</th>
					<th>Name</th>
					<th># Verses</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
	        @foreach( $bibleversions as $bibleversion )
	        	@php $versecount = $bibleversion->bibles->count(); @endphp
				<tr>
					<td scope="row">{{ $bibleversion->id }}</td>
					<td class="link" onclick="location.href='{{ url('/admin/bibleversions/' . $bibleversion->id) }}/edit'">{{ $bibleversion->name }}</td>
					<td>@if ($versecount>0)
							<a class="btn btn-secondary btn-sm" title="Show Books in this Version" href='{{ url('admin/biblebooks?version='.$bibleversion->id) }}'>
								{{ $versecount }}
							</a>
						@else
							0
						@endif
					</td>
					<td class="nowrap">
						@if( Auth::user()->isEditor() )
							<a class="btn btn-outline-primary btn-sm" title="Edit" href='{{ url('admin/bibleversions/'.$bibleversion->id) }}/edit'><i class="fa fa-pencil"></i></a>
							@if ($versecount==0)
								<a class="btn btn-danger btn-sm" title="Delete!" href='{{ url('admin/bibleversions/'.$bibleversion->id) }}/delete'><i class="fa fa-trash"></i></a>
							@endif
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
