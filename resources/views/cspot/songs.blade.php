
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('plans', 'active')


@include( 'cspot/snippets/modal', ['modalContent' => '$modalContent', 'modalTitle' => '$modalTitle' ] )




@section('content')


	@include('layouts.flashing')



	{{-- 	-	-	-	-	SONGS LIST Navigation Bar 
	--}}

	<nav class="navbar navbar-light bg-faded">

		<button class="navbar-toggler hidden-sm-up" type="button" 
			data-toggle="collapse" data-target="#exCollapsingNavbar2" aria-controls="exCollapsingNavbar2" aria-expanded="false" aria-label="Toggle navigation">
			&#9776;
		</button>


		<div class="collapse navbar-toggleable-xs" id="exCollapsingNavbar2">


			<a class="navbar-brand bg-info px-1 rounded hidden-md-down big" href="#">{{ $heading }}</a>
			<a class="navbar-brand bg-info px-1 rounded hidden-sm-down hidden-lg-up" href="#">{{ $heading }}</a>


			<ul class="nav navbar-nav float-xs-right">
				@if ($currentPage!=0)
					<li class="nav-item hidden-sm-down small float-xs-right">
						Page {{ $songs->currentPage() }} of {{ $songs->lastPage() }}<br>
						@if ( $songs->total() > $songs->perPage())
							<small class="hidden-md-down">showing {{ $songs->perPage() }} of a total of {{ $songs->total() }} songs</small>
						@endif
					</li>
				@endif
			</ul>


			<ul class="nav navbar-nav">

				@if (Request::is('*/songs'))
					<li class="nav-item mr-1">@include('cspot.snippets.fullTextSearch')</li>
				@endif

				@if( Auth::user()->isEditor() && $plan_id==0 )
					<li class="nav-item active">
						<a class="nav-link btn btn-outline-primary" 
						    href="{{ url('cspot/songs/create') }}{{ 
								Request::has('filtervalue')
									? '?type='.Request::input('filtervalue')
									: '' }}">
							<i class="fa fa-plus"> </i> &nbsp;Add New
								@if (Request::has('filtervalue') )
									@if (Request::input('filtervalue')=='video') 
										'<i class="fa fa-tv"> </i>'
									@endif
									@if (Request::input('filtervalue')=='slides')
										'<i class="fa fa-clone"> </i>'
									@endif
								@endif
						</a>
					</li>
				@endif

				{{-- only for songs... --}}
				@if (Request::is('*/songs'))
					<li class="nav-item active">
						@if ( ! Request::has('filtervalue') )
							<a class="nav-link btn btn-outline-danger" title="Show songs ordered by usage, but at least twice used in the past" 
									href="{{ url('cspot/songs') }}?filterby=songs&filtervalue=rare">
								List 'Forgotten' Songs
							</a>
						@endif
						@if ( Request::has('filtervalue') && Request::input('filtervalue')=='rare' )
							<a class="nav-link btn btn-outline-success" title="Show full song list"
									href="{{ url('cspot/songs') }}">
								Show All Songs
							</a>
						@endif
					</li>
				@endif

			</ul>

		</div>

	</nav>




	{{-- if page was called from a plan in order to search for a song, 
		 then we open the search box immediately --}}
	@if ($plan_id>0)
		<script>$('#fulltext-search').click();</script>
	@endif
    



	@if (count($songs))

		<table class="table table-striped table-bordered {{ count($songs)>15 ? 'table-sm' : '' }}">


			@if (Request::is('*/songs'))
				<thead class="thead-default">
					<tr>
						@include('cspot.snippets.theader', ['thfname' => 'id', 'thdisp' => '#', 'thsearch'=>false, 'thclass'=>'center hidden-md-down'])

						@include('cspot.snippets.theader', ['thfname' => 'title', 'thdisp' => 'Title', 'thsearch'=>true, 'thclass'=>''])

						@include('cspot.snippets.theader', ['thfname' => 'author', 'thdisp' => 'Author', 'thsearch'=>true, 'thclass'=>'hidden-md-down'])

						@include('cspot.snippets.theader', ['thfname' => 'book_ref', 'thdisp' => 'Book Ref', 'thsearch'=>true, 'thclass'=>'small hidden-xs-down'])

						<th class="center">Media</th>
						
						@include('cspot.snippets.theader', ['thfname' => 'chords', 'thdisp' => 'Chords?', 'thsearch'=>true, 'thclass'=>'center small hidden-sm-down'])

						<th class="center hidden-sm-down"><small>Sheets?</small></th>

						@include('cspot.snippets.theader', ['thfname' => 'items_count', 'thdisp' => 'Usage', 'thsearch'=>true, 'thclass'=>'small center hidden-md-down'])

						@include('cspot.snippets.theader', ['thfname' => 'last_used', 'thdisp' => 'Last Use', 'thsearch'=>false, 'thclass'=>'center hidden-md-down'])

						<th class="center hidden-lg-down">CCLI No.</th>

						@include('cspot.snippets.theader', ['thfname' => 'license', 'thdisp' => 'License', 'thsearch'=>true, 'thclass'=>'small center hidden-lg-down'])

						@if( Auth::user()->isLeader() )
							@include('cspot.snippets.theader', ['thfname' => 'created_at', 'thdisp' => 'Created', 'thsearch'=>false, 'thclass'=>'small center hidden-lg-down'])

							<th class="center hidden-xs-down">Modify</th>
						@endif
					</tr>
				</thead>
			@endif



			<tbody>
	        @foreach( $songs as $song )

	        	<?php 
	        		$editLink = Auth::user()->isEditor() && Request::is('*/songs') 
	        			? 'onclick=\'location.href="'.url('cspot/songs/'.$song->id).'/edit"\'' 
	        			: Request::is('*/songs')
	        				? ''
	        				: 'onclick="showYTvideoInModal(`'.$song->youtube_id.'`, this)"'; 
	        	?>


				<tr data-song-title="{{ $song->title }}">


					@if (Request::is('*/songs'))
						<th {!! $editLink !!} scope="row" class="link hidden-md-down float-xs-center">{{ $song->id }}</th>
					@endif


					<td {!! $editLink !!} class="link" title="{{ $song->lyrics }}">
						{!! $song->title_2=='video' ? '<i class="fa fa-tv"> </i>' : '' !!}
						{!! $song->title_2=='slides' ? '<i class="fa fa-clone"> </i>' : '' !!}
						{{ $song->title }} {{ ($song->title_2<>'' && $song->title_2<>'video' && $song->title_2<>'slides' && $song->title_2<>'training') ? '('. $song->title_2 .')' : '' }}
					</td>


					<td {!! $editLink !!} class="link hidden-md-down small">{{ mb_strimwidth($song->author, 0, 35, "...") }}</td>


					<td {!! $editLink !!} class="link center hidden-xs-down small">{{ $song->book_ref }}</td>



					<td class="center">
	                    @if ( $song->hymnaldotnet_id > 0 )
	                        <a target="new" title="See on hymnal.net" data-toggle="tooltip"
	                            href="https://www.hymnal.net/en/hymn/h/{{ $song->hymnaldotnet_id }}">
	                            <i class="fa fa-music"></i> </a> &nbsp; 
	                    @endif
	                    @if ( strlen($song->youtube_id)>0 )
	                        <a href="#" title="Play YouTube Video" class="red" data-toggle="tooltip" data-song-title="{{ $song->title }}"
	                        	onclick="showYTvideoInModal('{{ $song->youtube_id }}', this)"><i class="fa fa-youtube-play fa-lg"></i></a>
	                    @endif
					</td>


					@if (Request::is('*/songs'))
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

						<?php $last = $song->lastPlanUsingThisSong();?>


						<td class="center hidden-md-down">
							@if ($song->items_count>1 || !$last)
								<a title="Show list of plans using this song" href="{{ route('songs.show', $song->id) }}">{{ $song->items_count }}</a>
							@endif
							@if ($last && $song->items_count==1)
								<a href="{{ url('cspot/plans/'.$last->id) }}" title="open this plan">1</a>
							@endif
						</td>


						<td class="link center hidden-md-down">
							@if ($last) 
								<a href="{{ url('cspot/plans/'.$last->id) }}" title="open this plan">
								<small>{{ $song->last_time_used->formatLocalized('%a, %d %b \'%y') }}</small></a>
								{{-- <small>{{ $last->date->formatLocalized('%a, %d %b \'%y') }}</small></a> --}}
							@endif
						</td>


						<td class="center hidden-lg-down">
		                    @if ( $song->ccli_no > 1000 && 'MP'.$song->ccli_no!=$song->book_ref )
		                        <a class="btn btn-sm" type="button" target="new" 
		                            href="{{ env('SONGSELECT_URL').$song->ccli_no }}">
	                         	{{ $song->ccli_no }}
	                        	</a>
		                    @else
	                         	{{ $song->ccli_no }}
		                    @endif
						</td>


						<td class="center hidden-lg-down">{{ $song->license }}</td>

					@else
						<td class="white-space-pre-wrap">{{ $song->lyrics }}</td>
					@endif



					@if( Auth::user()->isLeader() )

						<td class="center hidden-lg-down small">{{ $song->created_at ? $song->created_at->formatLocalized('%d %b \'%y') : $song->created_at }}</td>

						<td class="hidden-xs-down nowrap center">
							@if ($plan_id>0)
								<a class="btn btn-secondary btn-sm" title="Add this song to selected Service plan" 
									href='{{ url('cspot/plans/'.$plan_id) }}/addsong/{{$song->id}}'><i class="fa fa-plus"></i></a>
							@endif

							@if( Auth::user()->isAuthor() )
								<a class="btn btn-outline-primary btn-sm" title="Edit song details" 
									href='{{ url('cspot/songs/'.$song->id) }}/edit'><i class="fa fa-edit"></i></a>
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

    	<p>No songs/videos/slides found!</p>

	@endif

	
@stop
