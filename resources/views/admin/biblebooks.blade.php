
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('setup', 'active')

@php
	$version = '';
	if (isset($request->version)) {
		$vers = \App\Models\Bibleversion::find($request->version);
		if ($vers) $version = ' for '.$vers->name;
	}
@endphp

@section('content')


	@include('layouts.flashing')

	@if( Auth::user()->isEditor() )
		<a class="btn btn-outline-primary btn-sm float-right" href='{{ url('admin/biblebooks/create') }}'>
			<i class="fa fa-plus"> </i> &nbsp; Add a new Book Name
		</a>
	@endif


    <h2>{{ $heading . $version }}</h2>



	@if (count($biblebooks))

		<table class="table table-striped table-bordered{{ count($biblebooks)>15 ? ' table-sm' : '' }}">
			<thead class="thead-default">
				<tr>
					<th>#</th>
					<th>Name</th>
					<th># Verses</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
	        @foreach( $biblebooks as $biblebook )
	        	@php
	        	    if (isset($request->version))
            			$versecount = \App\Models\Bible::where('bibleversion_id', $request->version)
            							->where('biblebook_id', $biblebook->id)
            							->count();
        			else 
	        	    	$versecount = $biblebook->bibles->count();
	        	@endphp
				<tr>
					<td scope="row">{{ $biblebook->id }}</td>
					<td class="link" onclick="location.href='{{ url('/admin/biblebooks/' . $biblebook->id) }}/edit'">{{ $biblebook->name }}</td>
					<td>{{ $versecount }}</td>
					<td class="nowrap">
						<a class="btn btn-secondary btn-sm" title="Show Chapters" href='{{ url('admin/biblebooks/'.$biblebook->id) }}'><i class="fa fa-book"></i></a>
						 @if( Auth::user()->isEditor() )
							<a class="btn btn-outline-primary btn-sm" title="Edit" href='{{ url('admin/biblebooks/'.$biblebook->id) }}/edit'><i class="fa fa-pencil"></i></a>
							@if ($versecount==0)
								<a class="btn btn-danger btn-sm" title="Delete!" href='{{ url('admin/biblebooks/'.$biblebook->id) }}/delete'><i class="fa fa-trash"></i></a>
							@endif
						@endif
					</td>
				</tr>
	        @endforeach
			</tbody>
		</table>

    @else

    	No biblebooks found! Add one ...

	@endif

	
@stop
