
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('plans', 'active')


@include( 'cspot/snippets/modal', ['modalContent' => '$modalContent', 'modalTitle' => '$modalTitle' ] )




@section('content')


	@include('layouts.flashing')


	@if( Auth::user()->isEditor() && $plan_id==0 )
		<span class="pull-sm-right">
			<a class="btn btn-outline-primary" href="{{ url('cspot/songs/create') }}">
				<i class="fa fa-plus"> </i> &nbsp; Add a new one
			</a>
		</span>
	@endif

	<span class="pull-sm-right m-r-2">
		Show only:
		<a class="btn btn-sm btn-outline-danger" href="{{ url('cspot/songs') }}?filterby=title_2&filtervalue=video">
			<i class="fa fa-tv"> </i> &nbsp; Videoclips
		</a>
		<a class="btn btn-sm btn-outline-warning" href="{{ url('cspot/songs') }}?filterby=title_2&filtervalue=slide">
			<i class="fa fa-clone"> </i> &nbsp; Slides
		</a>
		<a class="btn btn-sm btn-outline-success" href="{{ url('cspot/songs') }}">
			<i class="fa fa-asterisk"> </i> &nbsp; All
		</a>
	</span>


    <h2 class="hidden-xs-down pull-xs-left">{{ $heading }}</h2>
    

    @include('cspot.snippets.fullTextSearch')

	@if ($plan_id>0)
		{{-- page was called from a plan in order to search for a song, so we open the serach box immediately --}}
		<script>$('#fulltext-search').click();</script>
	@endif
    


	@if ($currentPage!=0)
		<center class="hidden-sm-down">
			Page {{ $songs->currentPage() }} of {{ $songs->lastPage() }}<br>
			<small>showing a total of {{ $songs->total() }} songs</small>
		</center>
	@endif


	@if (count($songs))

		<table class="table table-striped table-bordered {{ count($songs)>15 ? 'table-sm' : '' }}">

			<thead class="thead-default">
				<tr>
					@include('cspot.snippets.theader', ['thfname' => 'id', 'thdisp' => '#', 'thsort'=>false, 'thclass'=>'center hidden-md-down'])

					@include('cspot.snippets.theader', ['thfname' => 'title', 'thdisp' => 'Title', 'thsort'=>true, 'thclass'=>''])

					@include('cspot.snippets.theader', ['thfname' => 'author', 'thdisp' => 'Author', 'thsort'=>true, 'thclass'=>'hidden-md-down'])

					@include('cspot.snippets.theader', ['thfname' => 'book_ref', 'thdisp' => 'Book Ref', 'thsort'=>true, 'thclass'=>'small hidden-xs-down'])
						
					<th class="center hidden-sm-down"><small>Chords?</small></th>
					<th class="center hidden-sm-down"><small>Sheets?</small></th>
					<th class="center">Media</th>

					@include('cspot.snippets.theader', ['thfname' => 'items_count', 'thdisp' => 'Usage', 'thsort'=>false, 'thclass'=>'center hidden-md-down'])

					<th class="center hidden-md-down">Last Use</th>
					<th class="center hidden-lg-down">CCLI No.</th>
					<th class="center hidden-lg-down"><small>License</small></th>

					@include('cspot.snippets.theader', ['thfname' => 'created_at', 'thdisp' => 'Created', 'thsort'=>false, 'thclass'=>'center hidden-lg-down'])

					@if( Auth::user()->isUser() )
						<th class="hidden-xs-down">Action</th>
					@endif
				</tr>
			</thead>


			<tbody>
	        @foreach( $songs as $song )

	        	<?php $editLink = Auth::user()->isEditor() ? 'onclick=\'location.href="'.url('cspot/songs/'.$song->id).'/edit"\'' : ''; ?>

				<tr>
					<td {!! $editLink !!} scope="row" class="link hidden-md-down text-xs-center">{{ $song->id }}</td>

					<td {!! $editLink !!} class="link" title="{{ $song->lyrics }}">
						{{ $song->title }} {{ $song->title_2<>'' ? '('. $song->title_2 .')' : '' }}
					</td>

					<td {!! $editLink !!} class="link hidden-md-down">{{ mb_strimwidth($song->author, 0, 35, "...") }}</td>

					<td {!! $editLink !!} class="link center hidden-xs-down">{{ $song->book_ref }}</td>


					<td class="center hidden-sm-down">
						@if ( strlen($song->chords)>20 )
							<i class="fa fa-check"></i>
						@endif
					</td>


					<td class="center hidden-sm-down" title="Are there files (like sheet music) attached to this song?">
						@if ( count($song->files)>0 )
							<i class="fa fa-check"></i>
						@endif
					</td>


					<td class="center">
	                    @if ( $song->hymnaldotnet_id > 0 )
	                        <a target="new" title="See on hymnal.net" data-toggle="tooltip"
	                            href="https://www.hymnal.net/en/hymn/h/{{ $song->hymnaldotnet_id }}">
	                            <i class="fa fa-music"></i> </a> &nbsp; 
	                    @endif
	                    @if ( strlen($song->youtube_id)>0 )
						<!-- <a href="#" class="pull-xs-right" title="Show Lyrics" onclick=""><small>lyrics</small></a> -->
	                        <a href="#" title="Show YouTube video of this song" class="red" data-toggle="tooltip" data-song-title="{{ $song->title }}"
	                        	onclick="showYTvideoInModal('{{ $song->youtube_id }}', this)"><i class="fa fa-youtube-play"></i></a>
	                    @endif
					</td>


					<td class="center hidden-md-down">{{ $song->items_count }}</td>


					<?php $last = $song->lastPlanUsingThisSong(); ?>
					<td class="link center hidden-md-down">
						@if ($last) 
							<a href="{{ url('cspot/plans/'.$last->id) }}" title="open this plan">
							<small>{{ $last->date->formatLocalized('%a, %d %b \'%y') }}</small></a>
						@endif
					</td>


					<td class="center hidden-lg-down">
	                    @if ( $song->ccli_no > 10000 )
	                        <a class="btn btn-sm" type="button" target="new" 
	                            href="{{ env('SONGSELECT_URL').$song->ccli_no }}">
                         	{{ $song->ccli_no }}
                        	</a>
	                    @else
                         	{{ $song->ccli_no }}
	                    @endif
					</td>


					<td class="center hidden-lg-down">{{ $song->license }}</td>


					<td class="center hidden-lg-down">{{ $song->created_at ? $song->created_at->formatLocalized('%d %b \'%y') : $song->created_at }}</td>


					@if( Auth::user()->isUser() )
						<td class="hidden-xs-down nowrap center">
							@if ($plan_id>0)
								<a class="btn btn-secondary btn-sm" title="Add this song to selected Service plan" 
									href='/cspot/plans/{{$plan_id}}/addsong/{{$song->id}}'><i class="fa fa-plus"></i></a>
							@endif

							@if( Auth::user()->isAuthor() )
								<a class="btn btn-outline-primary btn-sm" title="Edit song details" 
									href='/cspot/songs/{{$song->id}}/edit'><i class="fa fa-edit"></i></a>
							@endif

							@if( Auth::user()->isEditor() && $song->items->count()==0)
								<a class="btn btn-danger btn-sm" title="Delete!" 
									href='{{ url('cspot/songs/'.$song->id) }}/delete'><i class="fa fa-trash"></i></a>
							@endif
						</td>
					@endif

				</tr>
	        @endforeach

			</tbody>

		</table>

		@if ($currentPage!=0)
			<center>
				{!! $songs->links() !!}
			</center>
		@endif

    @else

    	<p>No songs found!</p>

	@endif

	
@stop
