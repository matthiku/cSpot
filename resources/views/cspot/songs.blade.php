@extends('layouts.main')

@section('title', $heading)

@section('songs', 'active')



@section('content')


	@include('layouts.flashing')

    <h2>{{ $heading }}</h2>


	@if (count($songs))

		<table class="table table-striped table-bordered 
					@if(count($songs)>15)
					 table-sm
					@endif
					 ">
			<thead class="thead-default">
				<tr>
					<th>#</th>
					<th>Title</th>
					<!-- <th>Title 2</th> -->
					<th>Song No.</th>
					<th>Book Ref.</th>
					<th>Author</th>
					<th>Youtube ID</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
	        @foreach( $songs as $song )
				<tr>
					<th scope="row">{{ $song->id }}</th>
					<td>{{ $song->title }}</td>
					<!-- <td>{ { $song->title_2 }}</td> -->
					<td>{{ $song->song_no }}</td>
					<td>{{ $song->book_ref }}</td>
					<td>{{ $song->author }}</td>
					<td><a target="new" href="https://www.youtube.com/watch?v={{ $song->youtube_id }}">{{ $song->youtube_id }}</a></td>
					<td>
						<!-- <a class="btn btn-secondary btn-sm" title="Show Users" href='/cspot/songs/{{$song->id}}'><i class="fa fa-filter"></i></a> -->
						 @if( Auth::user()->isEditor() )
							<a class="btn btn-primary-outline btn-sm" title="Edit" href='/cspot/songs/{{$song->id}}/edit'><i class="fa fa-pencil"></i></a>
							<a class="btn btn-danger btn-sm" title="Delete!" href='/cspot/songs/{{$song->id}}/delete'><i class="fa fa-trash"></i></a>
						@endif
					</td>
				</tr>
	        @endforeach
			</tbody>
		</table>

    @else

    	<p>No songs found!</p>

	@endif

	@if( Auth::user()->isEditor() )
	<a class="btn btn-primary-outline" href='/cspot/songs/create'>
		<i class="fa fa-plus"> </i> &nbsp; Add a new song
	</a>
	@endif

	
@stop
