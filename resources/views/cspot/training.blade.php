
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('setup', 'active')



@section('content')




	{{-- =============================================================== Navigation Bar =================
	--}}

	<nav class="navbar navbar-light bg-faded mb-1">

		<button class="navbar-toggler hidden-sm-up" type="button" 
			data-toggle="collapse" data-target="#exCollapsingNavbar2" aria-controls="exCollapsingNavbar2" aria-expanded="false" aria-label="Toggle navigation">
			&#9776;
		</button>


		<div class="collapse navbar-toggleable-xs" id="exCollapsingNavbar2">


			<a class="navbar-brand bg-info px-1 rounded hidden-md-down big" href="#">{{ $heading }}</a>
			<a class="navbar-brand bg-info px-1 rounded hidden-sm-down hidden-lg-up" href="#">{{ $heading }}</a>



			<ul class="nav navbar-nav">


			    <a class="btn btn-outline-success float-xs-right" href='#' title="Show videos as list or tiles" 
			            onclick="$('#show-as-large-icons').toggle();$('#show-as-filelist').toggle();">
			        <i class="fa fa-list"> </i> &nbsp; List / Tiles
			    </a>


				@if( Auth::user()->isEditor() )
					<li class="nav-item active float-xs-right mr-2">
						<a class="nav-link btn btn-outline-primary" 
						    href="{{ url('cspot/songs/create') }}?type=training">
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


			</ul>

		</div>

	</nav>
   



	@if (count($songs))


		{{-- ===============================================================  TILES View ================
		--}}
		<div class="row" id="show-as-large-icons">


	        @foreach( $songs as $key => $song )

	        	@php
	        		// title can be split in 2 when it contains a ' - ' string
	        		$title = explode(' - ', $song->title);
	        	@endphp

    			<div class="col-xs-12 col-md-6 col-lg-4 col-xl-3">


					<div class="card training-videos-card">

						<h5 class="{{ $song->youtube_id ? 'bg-success' : 'bg-faded' }} text-xs-center training-videos-title">
							{{ $title[0] }}
							<br>
							<span class="small {{ $song->youtube_id ? 'text-white' : '' }}">{!! isset($title[1]) ? $title[1] : '<br>' !!}</span>
						</h5>


						{{-- navigation tabs 
						--}}
						<div class="card-header px-1 pt-0" style="padding-bottom: 5px;">
                            @if ( Auth::user()->isEditor() )
                                <a class="btn btn-outline-primary btn-sm float-xs-right" title="Edit song details" 
										href='{{ url('cspot/songs/'.$song->id) }}/edit'><i class="fa fa-edit"></i></a>
                            @endif
							<ul class="nav nav-tabs card-header-tabs float-xs-left">
								<li class="nav-item">
									<a class="nav-link {{ $song->youtube_id ? 'active' : '' }} ui-training-videos-tabs" href="#" id="pill-{{ $song->id }}-1" 
										onclick="toggleVideoTabs('{{ $song->id }}-1', '{{ $song->id }}-2')">Video</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{ $song->youtube_id ? '' : 'active' }} ui-training-videos-tabs" 	   href="#" id="pill-{{ $song->id }}-2" 
										onclick="toggleVideoTabs('{{ $song->id }}-2', '{{ $song->id }}-1')">Content</a>
								</li>
							</ul>
						</div>


						{{-- card blocks 
						--}}
						<div class="card-block text-xs-center ui-training-videos-blocks" id="tab-{{ $song->id }}-1" style="display: {{ $song->youtube_id ? 'inherit' : 'none' }};">
							<p class="card-text">
								@if ($song->youtube_id)
									<iframe height="200" src="https://www.youtube.com/embed/{{ $song->youtube_id }}" frameborder="0" allowfullscreen></iframe>
								@else
									coming soon...
								@endif
							</p>
						</div>

						<div class="card-block ui-training-videos-blocks" id="tab-{{ $song->id }}-2" style="display: {{ $song->youtube_id ? 'none' : 'inherit' }};">
							<div class="card-text">
								<span class="white-space-pre-wrap">{!! $song->lyrics !!}</span></div>
						</div>
					</div>


    			</div>



				@if ( ($key+1) % 4 == 0)
					<div class="clearfix hidden-lg-down"></div>
				@elseif ( ($key+1) % 3 == 0)
					<div class="clearfix hidden-md-up"></div>

				@elseif ( ($key+1) % 2 == 0 && 1>2)
					<div class="clearfix hidden-sm-down">{{$key}}hidden-sm-down</div>
				@endif


	        @endforeach


		</div><!-- row -->

		<script>
			$( ".show-training-videos-tabs" )
				.tabs({
  					heightStyle: "auto",
  					event: "mouseover",
				});
		</script>




		{{-- ===============================================================  LIST View ================
		--}}
        <div id="show-as-filelist" style="display: none;">

			<table class="table table-striped table-bordered {{ count($songs)>15 ? 'table-sm' : '' }}">



				<tbody>
		        @foreach( $songs as $song )

		        	<?php 
		        		$editLink = Auth::user()->isEditor() 
		        			? 'onclick=\'location.href="'.url('cspot/songs/'.$song->id).'/edit"\'' 
		        			: 'onclick="showYTvideoInModal(`'.$song->youtube_id.'`, this)"'; 
		        	?>


					<tr data-song-title="{{ $song->title }}">



						<td {!! $editLink !!} class="link">
							{{ $song->title }}
						</td>


						<td {!! $editLink !!} class="link hidden-md-down small">{{ mb_strimwidth($song->author, 0, 35, "...") }}</td>


						<td {!! $editLink !!} class="link center hidden-xs-down">{{ $song->book_ref }}</td>



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


						<td class="white-space-pre-wrap">{!! $song->lyrics !!}</td>



						@if( Auth::user()->isLeader() )

							<td class="center hidden-lg-down small">{{ $song->created_at ? $song->created_at->formatLocalized('%d %b \'%y') : $song->created_at }}</td>

							<td class="hidden-xs-down nowrap center">
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

		</div>

    @else

    	<p>No training videos found!</p>

	@endif

	
@stop
