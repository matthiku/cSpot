
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('roles', 'active')



@section('content')

	@include('layouts.flashing')

    <div class="row">
        <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">


			@if( Auth::user()->isEditor() )
				<a class="btn btn-sm btn-outline-primary float-right" href='{{ url('admin/song_parts/create') }}'>
					<i class="fa fa-plus"> </i>
					<span class="hidden-sm-down"> &nbsp; Add New Song Part</span>
					<span class="hidden-md-up">Add New</span>
				</a>
			@endif

		    <h4 class="lora">
		    	{{ $heading }}
		    	<small class="text-muted">
		    		<a tabindex="0" href="#"
		    			data-container="body" data-toggle="popover" data-placement="bottom" data-trigger="focus"
		    			data-content="Song Parts divide the lyrics of a song into various parts like verses and chorus.">
		    			<i class="fa fa-question-circle"></i></a>
				</small>
			</h4>

			<small class="l-h-1">
				When adding lyrics and chords to a song in OnSong format, the individual song parts must be chosen according to this list.
				For more information, see <a href="http://www.onsongapp.com/docs/features/formats/onsong/">the OnSong File Format manual</a>
			</small>

			@if (count($song_parts))

				<table class="table table-striped table-bordered {{ count($song_parts)>10 ? 'table-sm' : '' }}">
					<thead class="thead-default">
						<tr>
							<th>SeqNo</th>
							<th>Name</th>
							<th>Code</th>
							<th>Edit
								<div class="float-right small">Songs</div>
							</th>
						</tr>
					</thead>
					<tbody>
			        @foreach( $song_parts as $song_part )
						<tr>
							<th scope="row">{{ $song_part->sequence }}</th>
							<td>{{ ucfirst($song_part->name) }}</td>
							<td>{{ $song_part->code }}</td>
							<td class="nowrap">
								 @if( Auth::user()->isEditor() )
									<a class="btn btn-outline-primary btn-sm" title="Edit" href='{{ url('admin/song_parts/'.$song_part->id) }}/edit'><i class="fa fa-pencil"></i></a>
									@if ( ! $song_part->onsongs || ! $song_part->onsongs->count() )
										<a class="btn btn-danger btn-sm" title="Delete!" href='{{ url('admin/song_parts/'.$song_part->id) }}/delete'><i class="fa fa-trash"></i></a>
									@endif
								@endif
								{{-- show amount of songs using that song part type and a link to all these songs --}}
								<div class="float-right link small" 
									onclick="location.href='{{ route('songs.index') }}?filterby=onsong&filtervalue={{ $song_part->code }}'">{{ 
										$song_part->onsongs->count() }}</div>
							</td>
						</tr>
			        @endforeach
					</tbody>
				</table>

		    @else

		    	No Song Parts found!

			@endif
		
		</div>
	</div>
	
@stop
