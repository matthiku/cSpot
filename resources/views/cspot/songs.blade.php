
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

    <h2 class="hidden-xs-down">{{ $heading }}</h2>


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
					<th class="hidden-md-down">Author</th>
					<th class="center">Book Ref.</th>
					<th class="center hidden-sm-down">Youtube ID</th>
					<th class="center hidden-sm-down">Hymnal.Net</th>
					<th class="center hidden-md-down">Usage</th>
					<th class="center hidden-lg-down">CCLI No.</th>
					<th class="center hidden-lg-down">License</th>
					<th class="hidden-xs-down">Action</th>
				</tr>
			</thead>


			<tbody>
	        @foreach( $songs as $song )
				<tr 
					@if ( Auth::user()->isEditor() )
						class="link" onclick="location.href ='{{ url('cspot/songs/'.$song->id) }}/edit'"
					@endif
				>

					<td scope="row" class="hidden-md-down">{{ $song->id }}</td>

					<td>{{ $song->title }} {{ $song->title_2<>'' ? '('. $song->title_2 .')' : '' }}</td>

					<td class="hidden-md-down">{{ $song->author }}</td>

					<td class="center">{{ $song->book_ref }}</td>

					<td class="center hidden-sm-down">
						@if (substr($song->youtube_id,0,2)=="PL")
							<a target="new" href="https://www.youtube.com/playlist?list={{ $song->youtube_id }}">YT Playlist</a></td>
						@else
							<a target="new" href="https://www.youtube.com/watch?v={{ $song->youtube_id }}">{{ $song->youtube_id }}</a>
						@endif
					</td>

					<td class="center hidden-sm-down">
						<a target="new" href="https://www.hymnal.net/en/hymn/h/{{ $song->hymnaldotnet_id }}">{{ $song->hymnaldotnet_id }}</a>
					</td>

					<td class="center hidden-md-down">{{ $song->items->count() }}</td>

					<td class="center hidden-lg-down">{{ $song->ccli_no }}</td>

					<td class="center hidden-lg-down">{{ $song->license }}</td>

					<td class="hidden-xs-down nowrap center">
						<!-- <a class="btn btn-secondary btn-sm" title="Show Plans using this song" href='/cspot/plans/{{$song->id}}'><i class="fa fa-filter"></i></a> -->
						 @if( Auth::user()->isEditor() )
							<a class="btn btn-primary-outline btn-sm hidden-lg-down" title="Edit" href='{{ url('cspot/songs/'.$song->id) }}/edit'><i class="fa fa-pencil"></i></a>
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
