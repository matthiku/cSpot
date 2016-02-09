
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('songs', 'active')



@section('content')


	@include('layouts.flashing')

	@if( Auth::user()->isEditor() )
	<span class="pull-sm-right">
		<a class="btn btn-primary-outline" href={{ url('cspot/songs/create') }}>
			<i class="fa fa-plus"> </i> &nbsp; Add a new song
		</a>
	</span>
	@endif

    <h2>{{ $heading }}</h2>


	@if (count($songs))

		<table class="table table-striped table-bordered 
					@if(count($songs)>15)
					 table-sm
					@endif
					 ">
			<thead class="thead-default">
				<tr>
					<th class="hidden-md-down">#</th>
					<th>Title</th>
					<!-- <th>Title 2</th> -->
					<th class="hidden-md-down">CCLI No.</th>
					<th>Book Ref.</th>
					<th class="hidden-md-down">Author</th>
					<th class="hidden-sm-down">License</th>
					<th class="hidden-sm-down">Youtube ID</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
	        @foreach( $songs as $song )
				<tr class="link" onclick="location.href ='{{ url('cspot/songs/'.$song->id) }}/edit'">
					<td scope="row" class="hidden-md-down">{{ $song->id }}</td>
					<td>{{ $song->title }} {{ $song->title_2<>'' ? '('. $song->title_2 .')' : '' }}</td>
					<!-- <td>{ { $song->title_2 }}</td> -->
					<td class="hidden-md-down">{{ $song->ccli_no }}</td>
					<td>{{ $song->book_ref }}</td>
					<td class="hidden-md-down">{{ $song->author }}</td>
					<td class="hidden-sm-down">{{ $song->license }}</td>
					<td class="hidden-sm-down">@if (substr($song->youtube_id,0,2)=="PL")
							<a target="new" href="https://www.youtube.com/playlist?list={{ $song->youtube_id }}">YT Playlist</a></td>
						@else
							<a target="new" href="https://www.youtube.com/watch?v={{ $song->youtube_id }}">{{ $song->youtube_id }}</a></td>
						@endif
					<td class="nowrap">
						<!-- <a class="btn btn-secondary btn-sm" title="Show Users" href='/cspot/songs/{{$song->id}}'><i class="fa fa-filter"></i></a> -->
						 @if( Auth::user()->isEditor() )
							<a class="btn btn-primary-outline btn-sm" title="Edit" href='{{ url('cspot/songs/'.$song->id) }}/edit'><i class="fa fa-pencil"></i></a>
							<a class="btn btn-danger btn-sm" title="Delete!" href='{{ url('cspot/songs/'.$song->id) }}/delete'><i class="fa fa-trash"></i></a>
						@endif
					</td>
				</tr>
	        @endforeach
			</tbody>
		</table>

    @else

    	<p>No songs found!</p>

	@endif

	
@stop
