
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('plans', 'active')



@section('content')

	@include('layouts.flashing')

	@if( Auth::user()->isEditor() )
	<span class="pull-sm-right">
		<a class="btn btn-primary-outline" href={{ url('cspot/songs/create') }}>
			<i class="fa fa-plus"> </i> &nbsp; Add a new song
		</a>
	</span>
	@endif

    <h2 class="hidden-xs-down pull-xs-left">{{ $heading }}
    </h2>
    	@include('cspot.snippets.fullTextSearch')

    
	<center>
		Page {{ $songs->currentPage() }} of {{ $songs->lastPage() }}<br>
		<small>showing a total of {{ $songs->total() }} songs</small>
	</center>


	@if (count($songs))

		<table class="table table-striped table-bordered 
					@if(count($songs)>15)
					 table-sm
					@endif
					 ">

			<thead class="thead-default">
				<tr>
					<th class="hidden-md-down">#</th>

					@include('cspot.snippets.theader', ['thfname' => 'title', 'thdisp' => 'Title', 'thsort'=>true, 'thclass'=>''])

					@include('cspot.snippets.theader', ['thfname' => 'author', 'thdisp' => 'Author', 'thsort'=>true, 'thclass'=>''])

					<th class="center hidden-xs-down link" onclick="reloadListOrderBy('book_ref')"
						data-toggle="tooltip" title="Sort list by Book Reference">
						Book Ref
						<i class="fa fa-sort {{ Request::is('*/sorted/book_ref*') ? 'text-primary' : '' }}"> </i></th>
						
					<th class="center hidden-sm-down"><small>Chords?</small></th>
					<th class="center hidden-sm-down"><small>Sheets?</small></th>
					<th class="center">Media</th>
					<th class="center hidden-md-down">Usage</th>
					<th class="center hidden-md-down">Last Use</th>
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

					<td scope="row" class="hidden-md-down text-xs-center">{{ $song->id }}</td>

					<td>{{ $song->title }} {{ $song->title_2<>'' ? '('. $song->title_2 .')' : '' }}</td>

					<td class="hidden-md-down">{{ $song->author }}</td>

					<td class="center hidden-xs-down">{{ $song->book_ref }}</td>


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
	                        <a target="new" title="Play on YouTube" class="red" data-toggle="tooltip"
	                        	href="https://www.youtube.com/{{ 
	                        		substr($song->youtube_id,0,2)=="PL" ? 'playlist?list=' : 'watch?v=' 
	                        		}}{{ $song->youtube_id }}">
	                             <i class="fa fa-youtube-play"></i></a>
	                    @endif
					</td>


					<td class="center hidden-md-down">{{ $song->items->count() }}</td>


					<td class="center hidden-md-down">
						<?php  
						 	$last = $song->lastPlanUsingThisSong();
						 	if ($last) { echo $last->date->formatLocalized('%a, %d %b \'%y'); }
						 ?>
					</td>


					<td class="center hidden-lg-down">
	                    @if ( $song->ccli_no > 10000 )
	                        <a class="btn btn-sm" type="button" target="new" 
	                            href="https://olr.ccli.com/search/results?SearchTerm={{ $song->ccli_no }}">
                         	{{ $song->ccli_no }}
                        	</a>
	                    @else
                         	{{ $song->ccli_no }}
	                    @endif
					</td>


					<td class="center hidden-lg-down">{{ $song->license }}</td>


					<td class="hidden-xs-down nowrap center">
						<!-- <a class="btn btn-secondary btn-sm" title="Show Plans using this song" href='/cspot/plans/{{$song->id}}'><i class="fa fa-filter"></i></a> -->
						 @if( Auth::user()->isEditor() )
							<!-- <a class="btn btn-primary-outline btn-sm hidden-lg-down" title="Edit" href='{{ url('cspot/songs/'.$song->id) }}/edit'><i class="fa fa-pencil"></i></a> -->
							@if ($song->items->count()==0)
								<a class="btn btn-danger btn-sm" title="Delete!" href='{{ url('cspot/songs/'.$song->id) }}/delete'><i class="fa fa-trash"></i></a>
							@endif
						@endif
					</td>

				</tr>
	        @endforeach

			</tbody>

		</table>

		<center>
			{!! $songs->links() !!}
		</center>
		<script>
			// add missing classes and links into the auto-geneerated pagination buttons
			$('.pagination').children().each(function() { $(this).addClass('page-item'); });
			$('.page-item>a').each(function() { $(this).addClass('page-link'); });
			var pgActive = $('.active.page-item').html();
			$('.active.page-item').html('<a class="page-link" href="#">'+pgActive+'</a>');
			$('.disabled.page-item').each(function() {
				var innerHtml = $(this).html();
				$(this).html('<a class="page-link" href="#">'+innerHtml+'</a>');
			})
		</script>

    @else

    	<p>No songs found!</p>

	@endif

	
@stop
