
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('setup', 'active')

@php
	$versionText = '';
	// if version was not selected, we just use the first available one for the next step
	$vers = \App\Models\Bibleversion::first();

	if (isset($request->version)) {
		$vers = \App\Models\Bibleversion::find($request->version);
		if ($vers) $versionText = ' for '.$vers->name;
	}
	// get all bible versions for selection box
	$versions = \App\Models\Bibleversion::get();
@endphp

@section('content')


	@include('layouts.flashing')

	@if( Auth::user()->isEditor() )
		<a class="btn btn-outline-primary btn-sm float-right" href='{{ url('admin/biblebooks/create') }}'>
			<i class="fa fa-plus"> </i> &nbsp; Add a new Book Name
		</a>
	@endif

	<select class="custom-select float-right mr-2" onchange="location.href=location.pathname+'?version='+this.value">
		<option selected>Select Version...</option>
		@foreach ($versions as $version)
			<option{{ isset($request->version) && $version->id==$request->version ? ' disabled' : '' }} value="{{ $version->id }}">{{ $version->name }}</option>
		@endforeach
	</select>

	

    <h2>{{ $heading . $versionText }}</h2>




	@if (count($biblebooks))


		<table class="table table-striped table-bordered{{ count($biblebooks)>15 ? ' table-sm' : '' }}">

			<thead class="thead-default">
				<tr>
					<th>#</th>
					<th>Name</th>
					<th># Verses</th>
					<th># Chapters</th>
					@if( Auth::user()->isEditor() )
						<th>Action</th>
					@endif
				</tr>
			</thead>


			<tbody>@foreach( $biblebooks as $biblebook )
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


					@if( Auth::user()->isEditor() )
						<td class="link" title="Edit this name" onclick="location.href='{{ url('/admin/biblebooks/' . $biblebook->id) }}/edit'">{{ $biblebook->name }}</td>
					@elseif ($versecount>0)
						<td>
							<a class="btn btn-secondary btn-sm" title="Show Bible Text" href='{{ url('admin/bibles?version='.$vers->id.'&book='.$biblebook->id) }}'>
								{{ $biblebook->name }}</a>
						</td>
					@else
						<td>{{ $biblebook->name }}</td>
					@endif


					<td>
						@if ($versecount>0)
							<a class="btn btn-secondary btn-sm" title="Show Bible Text" href='{{ url('admin/bibles?version='.$vers->id.'&book='.$biblebook->id) }}'>
								{{ $versecount }}</a>
						@else
							{{ $versecount }}
						@endif
					</td>


					<td>
						@if ($versecount>0)
							<a class="btn btn-secondary btn-sm" title="Show Bible Text" href='{{ url('admin/bibles?version='.$vers->id.'&book='.$biblebook->id) }}'>
								{{ $biblebook->chapters }}</a>
						@else
							{{ $biblebook->chapters }}
						@endif
					</td>

					
					@if( Auth::user()->isEditor() )
						<td class="nowrap">
							<a class="btn btn-outline-primary btn-sm" title="Edit" href='{{ url('admin/biblebooks/'.$biblebook->id) }}/edit'><i class="fa fa-pencil"></i></a>
							@if ($versecount==0)
								<a class="btn btn-danger btn-sm" title="Delete!" href='{{ url('admin/biblebooks/'.$biblebook->id) }}/delete'><i class="fa fa-trash"></i></a>
							@endif
						</td>
					@endif

				</tr>


	        @endforeach</tbody>

		</table>



    @else

    	No biblebooks found! Add one ...

	@endif

	
@stop
