
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('roles', 'active')



@section('content')

	@include('layouts.flashing')

    <div class="row">
        <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">


			@if( Auth::user()->isEditor() )
				<a class="btn btn-outline-primary float-xs-right" href='{{ url('admin/song_parts/create') }}'>
					<i class="fa fa-plus"> </i>
					<span class="hidden-sm-down"> &nbsp; Add a new Song Part</span>
					<span class="hidden-md-up">Add New</span>
				</a>
			@endif

		    <h3 class="lora">
		    	{{ $heading }}
		    	<small class="text-muted">
		    		<a tabindex="0" href="#"
		    			data-container="body" data-toggle="popover" data-placement="bottom" data-trigger="focus"
		    			data-content="Song Parts divide the lyrics of a song into various parts like verses and chorus.">
		    			<i class="fa fa-question-circle"></i></a>
				</small>
			</h3>


			@if (count($song_parts))

				<table class="table table-striped table-bordered 
							@if(count($song_parts)>10)
							 table-sm
							@endif
							 ">
					<thead class="thead-default">
						<tr>
							<th>SeqNo</th>
							<th>Name</th>
							<th>Code</th>
							<th>Action
								<div class="float-xs-right small">Songs</div>
							</th>
						</tr>
					</thead>
					<tbody>
			        @foreach( $song_parts as $song_part )
						<tr class="link" onclick="location.href='{{ url('/admin/song_parts/' . $song_part->id) }}/edit'">
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
								<div class="float-xs-right small">{{ $song_part->onsongs->count() }}</div>
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
