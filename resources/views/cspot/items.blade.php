@include( 'cspot/snippets/modal', ['modalContent' => '$modalContent', 'modalTitle' => '$modalTitle' ] )

<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<div class="table-responsive">
	<table class="table table-items
		{{ count($plan->items)>5 ? 'table-sm m-b-0' : 'm-t-3 p-b-3'}} {{ count($plan->items)>10 ? 'table-xs' : ''}}">

		<tbody id="tbody-items">
	    @foreach( $plan->items as $key => $item )

			<?php 
				// set variable for click-on-item action
				$onclick = 'onclick=showSpinner();location.href='."'".url('cspot/plans/'.$plan->id.'/items/'.$item->id).'/edit'."' ";
				$tooltip = "title=click/touch&nbsp;for&nbsp;details data-toggle=tooltip" ; 
				// check if there is a song_id but no song in the database!
				if ( $item->song_id && ! $item->song()->exists()) { 
					$item->comment="(Song with id ".$item->song_id.' missing!)'; 
					$item->song_id = Null; 
				} 
			?>


			{{-- check if this is a "FLEO" item - only visible to the leader 
			--}}
			@if ( (! $item->forLeadersEyesOnly) || Auth::user()->ownsPlan($plan->id) )

			<tr id="tr-item-{{ str_replace('.', '-', $item->seq_no).($item->deleted_at ? 'trashed' : '') }}" 
				data-item-id="{{ $item->id }}" data-item-update-action="{{ route('cspot.api.items.update', $item->id) }}"
				data-old-song-id="{{ $item->song_id  ?  $item->song->id 	  : 'NULL' }}"
				class="{{ 		   $item->deleted_at ? 'trashed text-muted'   : '' }} 
					   @if ($newest_item_id == $item->id) bg-khaki newest-item
					   		<?php $newest_item_seq_no = str_replace('.', '-', $item->seq_no); ?>
					   @endif
			    ">


				<th class="{{ Auth::user()->isUser() ? 'drag-item ' : ''}}dont-print" scope="row" title="drag item into the new position">
					<span class="hidden-sm-down pull-xs-right text-success">{{ $item->seq_no }}</span>
					@if ( Auth::user()->ownsPlan($plan->id) && $plan->date >= \Carbon\Carbon::today() )
						<i class="p-r-1 fa fa-arrows-v">
					@endif
				</th>

				{{-- for leader's eyes only? 
				--}}
				@if( Auth::user()->ownsPlan($plan->id) )
					<td 	class="hidden-sm-down link" onclick="changeForLeadersEyesOnly(this)" 
							data-value="{{ $item->forLeadersEyesOnly }}"  data-toggle="tooltip" 
							title="{{ $item->forLeadersEyesOnly 
								? "Item visible for leader's eyes only. Click to change!"
								: "Item is visible for all users. Click to change!"
								}}">
						{!! $item->forLeadersEyesOnly 
							? '<i class="fa fa-eye-slash red"></i>' 
							: '<i class="fa fa-eye"></i>' !!}
					</td>
				@endif


				{{-- Song Details editable via popup dialog 
				--}}
				<td class="hidden-md-down center always-print link show-songbook-ref" 
					@if( Auth::user()->isUser() )
						data-toggle="modal" data-target="#searchSongModal" data-item-id="before-{{ $item->id }}"
						data-plan-id="{{ $plan->id }}" data-item-action="update-song" data-seq-no="before-{{ $item->seq_no }}" 
						data-action-url="{!! route('cspot.api.items.update', $item->id) !!}"
						@if ($item->song_id) 
							title="click to change"
						@else
							title="select a song for this item" 
							onmouseover="$(this).children('.add-song-button').toggleClass('text-muted')" 
							onmouseout="$( this).children('.add-song-button').toggleClass('text-muted')" 
						@endif
					@endif
					>
					@if ($item->song_id) 
						{{ $item->song->book_ref }}
					@elseif (Auth::user()->ownsPlan( $plan->id ))
						<span class="add-song-button link text-muted"><i class="fa fa-plus"></i><sup><i class="fa fa-music"></i></sup></span> &nbsp;
					@endif
				</td>


				{{-- show separate column for song title and comment on large devices 
				--}}
				<td class="hidden-lg-down link show-song-title" 
					@if ($item->song_id)
						title="{{ substr($item->song->lyrics,0,500) }}" data-toggle="tooltip" 
						@if ($item->seq_no<10)
							data-placement="bottom"
						@endif
						data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><pre class="tooltip-inner tooltip-wide"></pre></div>'
					@endif
					>
					<span class="hover-show" 
						@if( Auth::user()->isUser() )
							data-toggle="modal" data-target="#searchSongModal" data-item-id="{{ $item->id }}"
							data-plan-id="{{ $plan->id }}" data-item-action="update-song" data-seq-no="{{ $item->seq_no }}" 
						@endif
						data-action-url="{!! route('cspot.api.items.update', $item->id) !!}">
						@if($item->song_id) 
							{{ $item->song->title }} 
							{{ $item->song->title_2 ? ' ('. $item->song->title_2 .')' : '' }}
						@endif
					</span>
					<span class="hover-only fa fa-pencil text-muted"></span>
				</td>


				<!-- COMMENT column - allow for inline editing 
				-->
				<td class="hidden-lg-down center comment-cell" title="click to change"
					@if (Auth::user()->ownsPlan( $plan->id ))
						onmouseover="$(this).children('.add-scripture-ref').show()" onmouseout="$('.add-scripture-ref').hide()"
					@endif
					>

					{{-- is the comment text a link? --}}
					@if ( substr($item->comment, 0,4 )=='http' )
						<a href="{{ $item->comment }}" target="new">{{ $item->comment }}<i class="fa fa-globe"></i></a>

					@elseif ( $plan->date > \Carbon\Carbon::yesterday() && Auth::user()->ownsPlan( $plan->id ))
						<span id="comment-item-id-{{ $item->id }}" class="editable comment-textcontent hover-show">{{ $item->comment }}</span>

						{{-- show editing icon only when comment is not empty and when hovering over it --}}
						@if ($item->comment)
							<span class="fa fa-eraser text-muted" onclick="eraseThisComment(this, {{$item->id}})" title="Discard this comment"></span>
						@endif

						{{-- icon to add scripture reference --}}
						<span class="text-muted add-scripture-ref" style="display: none" title="add scripture reference"
							data-toggle="modal" data-target="#searchSongModal" data-item-id="{{ $item->id }}"
							data-plan-id="{{ $plan->id }}" data-item-action="update-scripture" data-seq-no="{{ $item->seq_no }}" 
							data-action-url="{!! route('cspot.api.items.update', $item->id) !!}">
							<i class="fa fa-book"></i><sup>+</sup>
						</span>
					@else
						{{ $item->comment }}
					@endif

				</td>


				{{-- show combined song-title and comment column on small devices 
				--}}
				<td {{$onclick}} {{$tooltip}} class="hidden-xl-up link">
					@if ($item->song_id)
						<i class="fa fa-music"></i>
						<span class="hidden-lg-up">
							{{ $item->song->book_ref ? $item->song->book_ref.' ' : '' }}
						</span>
						<span class="font-italic">{{ $item->song->title }}</span>
						<small class="bg-info">
					@endif
					@if (preg_match('/[(][A-Z]{3}[)]/', $item->comment) )
						<i class="fa fa-book"></i>&nbsp;{{ $item->comment }}
					@else
						{{ $item->comment }}
					@endif
					@if ($item->song_id)
						</small>
					@endif
				</td>


				{{-- show personal notes as popup 
				--}}
				<td {{$onclick}} class="hidden-sm-down center link"
					title="Your Private Notes:{!! $item->itemNotes->where('user_id', Auth::user()->id)->first() ? "\n".$item->itemNotes->where('user_id', Auth::user()->id)->first()->text."\n" : "\nyour private notes\n" !!}(Click to edit)"
					data-toggle="tooltip" data-placement="bottom"
					data-template='
							<div class="tooltip" role="tooltip">
								<div class="tooltip-arrow"></div>
								<pre class="center tooltip-inner tooltip-medium"></pre>
							</div>'>
					{!! $item->itemNotes->where('user_id', Auth::user()->id)->first() ? '<i class="fa fa-sticky-note-o"> </i>' : '' !!}
				</td>


				@if (Auth::user()->hasMusician())
					{{-- indicate if chords are available for this song 
					--}}
					<td class="hidden-sm-down center" title="Lyrics with chords for guitars">
						@if ($item->song_id)
							@if ( strlen($item->song->chords)>20 )
								<a href="{{ url('cspot/items').'/'.$item->id }}/chords">
									<i class="fa fa-file-code-o"></i></a>
							@endif
						@endif
					</td>
				
					{{-- indicate if leader added instructions for this song 
					--}}
					<td class="hidden-sm-down center red" title="Musical Instructions for this song? Click to see">
						@if ($item->song_id && $item->key )
							<a href="{{ url('cspot/plans/'.$plan->id) }}/items/{{$item->id}}/edit/">&#10071;</a>
						@endif
					</td>
				

					{{-- indicate if sheet music is linked to this song 
					--}}
					<td class="hidden-sm-down center"  title="Sheet music attached to the song"
						@if ( $item->song_id && count($item->song->files)>0 )
							title="{{ $item->song->files[0]->filename }}" data-toggle="tooltip" data-placement="left"
							data-template='
								<div class="tooltip" role="tooltip">
									<div class="tooltip-arrow"></div>
									<pre class="tooltip-inner tooltip-wide"></pre>
									<img src="{{ url(config('files.uploads.webpath')).'/thumb-'.$item->song->files[0]->token }}">
									@if ( count($item->song->files)>1 )
										<img src="{{ url(config('files.uploads.webpath')).'/thumb-'.$item->song->files[1]->token }}">
									@endif
								</div>'
						@endif
						>
						@if ($item->song_id)
							<a href="{{ url('cspot/items').'/'.$item->id }}/sheetmusic">
							@if ( count($item->song->files)>1 )
								{{ count($item->song->files) }}
							@endif
							@if ( count($item->song->files)==1 )
								<i class="fa fa-music"></i>
							@endif
							</a>
						@endif
					</td>
				@endif


				{{-- show if files are attached to this item and show button 
				--}}
				<td class="hidden-sm-down center"
					@if ( count($item->files)>0 )
						title="{{ $item->files[0]->filename }}" data-toggle="tooltip" data-placement="left"
						data-template='
							<div class="tooltip" role="tooltip">
								<div class="tooltip-arrow"></div>
								<pre class="tooltip-inner tooltip-wide"></pre>
								<img src="{{ url(config('files.uploads.webpath')).'/thumb-'.$item->files[0]->token }}">
								@if ( count($item->files)>1 )
									<img src="{{ url(config('files.uploads.webpath')).'/thumb-'.$item->files[1]->token }}">
								@endif
							</div>'
					@endif
					>
					@if ( count($item->files)>1 )
						{{ count($item->files) }}
					@endif
					@if ( count($item->files)==1 )
						<i class="fa fa-file-picture-o"></i>
					@endif
					@if ( $item->key=='announcements' )
						<i class="fa fa-bullhorn" title="Announcements Slide!"></i>
					@elseif ( $plan->date > \Carbon\Carbon::yesterday() && Auth::user()->ownsPlan($plan->id) )
						{{-- MODAL POPUP to attach file (image) to this item --}}
						<a href="#" class="text-muted link" data-toggle="modal" data-target="#searchSongModal"
						    id="add-file-button-item-{{ $item->id }}" data-song-id="{{$item->song_id}}"
							data-plan-id="{{$plan->id}}" data-item-action="add-file" data-item-id="{{$item->id}}" data-seq-no="{{$item->seq_no}}" 
							data-action-url="{!! route('cspot.api.items.update', $item->id) !!}"
							title="attach file (image) to this item">
							<i class="fa fa-image"></i><sup>+</sup>
						</a>
					@endif
				</td>


				{{-- show various links if available, for song 
				--}}
				<td class="center hidden-xs-down dont-print show-youtube-links">
					<big>
					@if ($item->song_id)
	                    @if ( $item->song->hymnaldotnet_id > 0 )
	                        <a target="new" title="Review song on hymnal.net" data-toggle="tooltip" class="m-r-1" 
	                            href="{{ env('HYMNAL.NET_PLAY', 'https://www.hymnal.net/en/hymn/h/').$item->song->hymnaldotnet_id }}">
	                            <i class="fa fa-music"></i> </a>
	                    @endif
	                    @if ( $item->song->ccli_no > 1000 && 'MP'.$item->song->ccli_no != $item->song->book_ref && Auth::user()->hasMusician() )
	                        <a target="new" title="Review song on SongSelect" data-toggle="tooltip" class="m-r-1" 
	                            href="{{ env('SONGSELECT_URL', 'https://songselect.ccli.com/Songs/').$item->song->ccli_no }}">
	                            <img src="{{ url('/') }}/images/songselectlogo.png" width="20"></a>
	                    @endif
	                    @if ( strlen($item->song->youtube_id)>0 )
	                    	@if (Auth::user()->ownsPlan( $plan->id ))
	                            <a title="Play in new tab" data-toggle="tooltip" target="new" class="hidden-md-down pull-xs-right"
	                            	href="{{ env('YOUTUBE_PLAY', 'https://www.youtube.com/watch?v=').$item->song->youtube_id }}">
	                            	<i class="fa fa-external-link"></i></a>
                        	@endif
	                        <a href="#" title="Play here" class="red pull-xs-right m-r-1" data-toggle="tooltip" data-song-title="{{ $item->song->title }}"
	                        	onclick="showYTvideoInModal('{{ $item->song->youtube_id }}', this)">
	                            <i class="fa fa-youtube-play"></i></a>
	                    @endif
					@endif
					</big>
				</td>




				{{--  _______________________________________________

									ACTION buttons 
					  ________________________________________________
				 --}}
				<td class="text-xs-right text-nowrap dont-print">


					{{-- CCLI Song Usage Reporting 
						 (link opens new browser tab of CCLI reporting page!)
					--}}
					@if (  $item->song_id 
						&& $item->song->ccli_no > 1000 && 'MP'.$item->song->ccli_no != $item->song->book_ref 
						&& Auth::user()-> isEditor() 
						&& $plan->date < \Carbon\Carbon::tomorrow() )

						@if ( $item->song->license == 'CCLI' )

							{{-- show a different icon color depending on status of reporting 
							 	 red    - no date in field 'reported_at'  - no action has taken place yet
							 	 yellow - date present, but time is 00:00 - user started reporting, but hasn't confirmed it yet
								 green  - date is set and time > 00:00 	  - user has confirmed that he finished the reporting
							--}}
							@if (! $item->reported_at)

								<a class="btn btn-sm btn-outline-danger hidden-xs-down m-r-1" 
									data-toggle="tooltip" data-placement="left" title="Report Song Usage to CCLI" 
									onclick="reportSongUsageToCCLI(this, {{ $item->id }}, {{ $item->reported_at ? $item->reported_at : 'null' }})" 
									href='{{ env('CCLI_REPORT_URL', 'https://olr.ccli.com/search/results?SearchTerm=').$item->song->ccli_no }}' target="new">
									<i class="fa fa-copyright fa-lg"></i></a>

							@else

								@if ( $item->reported_at->hour==0 && $item->reported_at->minute==0 )
									{{-- reporting process was started but not yet confirmed by the user --}}
									<a class="btn btn-sm btn-outline-warning hidden-xs-down m-r-1" 
										data-toggle="tooltip" data-placement="left" title="Please confirm here when Song Usage Report to CCLI has been completed!" 
										onclick="reportSongUsageToCCLI(this, {{ $item->id }}, '{{ $item->reported_at ? $item->reported_at : 'null' }}')" 
										href="#"><i class="fa fa-copyright fa-lg"></i></a>
								@else
									{{-- reporting process complete --}}
									<a class="btn btn-sm btn-outline-success hidden-xs-down narrow"
										data-toggle="tooltip" data-placement="left" title="Song Usage has already been reported to CCLI."
										href="#"><i class="fa fa-copyright"></i><i class="fa fa-check"></i></a>

								@endif

							@endif
						@else
							<small>({{ $item->song->license }})</small>
						@endif
					@endif


					{{-- 'start presentation' button visible for all 
					--}}
					@if (! $item->deleted_at)
					<a class=" hidden-xs-down" data-toggle="tooltip" data-placement="left" title="Start presentation from here" 
						href='{{ url('cspot/items/'.$item->id) }}/present'>
						&nbsp;<i class="fa fa-tv fa-lg"></i>&nbsp;</a>
					@endif

					@if( (Auth::user()->ownsPlan($plan->id) && $plan->date >= \Carbon\Carbon::yesterday()) || Auth::user()->isAdmin() )
						<span class="trashedButtons" style="display: {{ $item->deleted_at ? 'initial' : 'none' }}">
							<a class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="left" title="Restore this item" 
								href='{{ url('cspot/items/'.$item->id) }}/restore'>
								<i class="fa fa-undo"></i></a>

        					{{-- check if user is leader of the corresponding plan or author/admin --}}
							@if ( $item->plan->leader_id==Auth::user()->id || Auth::user()->isAuthor() )
								<a class="text-danger" data-toggle="tooltip" title="Delete permanently!" 
									href='{{ url('cspot/items/'.$item->id) }}/permDelete'>
									&nbsp;<i class="fa fa-trash fa-lg"></i></a>
							@endif
						</span>

						@if (! $item->deleted_at)


							{{-- new MODAL POPUP to add song/scripture/comment --}}
							<button type="button" class="btn btn-secondary btn-sm text-info" data-toggle="modal" data-target="#searchSongModal"
								data-plan-id="{{$plan->id}}" data-item-id="{{$item->id}}" data-seq-no="before-{{$item->seq_no}}" data-item-action="insert-item"
								href='#' title="insert song, scripture or comment before this item">
								<i class="fa fa-indent"></i><sup>+</sup>
							</button>


							{{-- new DROPDOWN MENU for editing or deleting items --}}
							<div class="btn-group {{ count($plan->items)-3 < $key  &&  $key > 0 ? 'dropup' : '' }}">
								<button type="button" class="btn btn-secondary btn-sm dropdown-toggle" 
									data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="fa fa-ellipsis-v hidden-xs-down"></i>
								</button>

								<div class="dropdown-menu dropdown-menu-right">
									@if ($item->song_id)
					 				<a class="dropdown-item edit-song-link" href='{{ url('cspot/songs/'.$item->song->id) }}/edit/'>
										<i class="fa fa-music"></i><i class="fa fa-pencil text-muted"></i>&nbsp;Edit Song</a>
									@endif
					 				<a class="dropdown-item" href='{{ url('cspot/plans/'.$plan->id) }}/items/{{$item->id}}/edit/'>
										<i class="fa fa-pencil fa-lg"></i>&nbsp; &nbsp;Edit Item</a>

									<a class="dropdown-item text-warning" href='#' onclick="removeItem(this)"
										data-action-url="{!! route('cspot.api.items.delete', $item->id) !!}">
										<i class="fa fa-trash fa-lg"></i>&nbsp; &nbsp;Remove item</a>
								</div>
							</div>

						@endif
					@endif
				</td>


			</tr>
			@endif {{-- (is it a FLEO item?) --}}

	    @endforeach

		@if (count($plan->items) < 6)
			<tr><td class="pull-xs-right">
			
				@if (count($plan->items) == 0 ) 
					Plan has no items yet:</td><td>
				@endif

				@include ('cspot.snippets.add_item_button')

			</td></tr>
		@endif

		</tbody>

	</table>
</div>


@if( Auth::user()->ownsPlan($plan->id) )
	<div 	class="small pull-xs-right m-l-2" id="showCachedItems" 
		 	style="display: {{ $plan->planCaches()->count() ? 'initial' : 'none' }}">
		Cache contains {{ $plan->planCaches()->count() }} pre-rendered items. 
		<a href="#" onclick="clearServerCache({{ $plan->id }});"><i class="fa fa-trash"></i>&nbsp;Delete.</a>
	</div>
@endif

@if( Auth::user()->ownsPlan($plan->id) && $plan->date >= \Carbon\Carbon::yesterday() )

	<div class="pull-xs-right m-l-2" id="trashedItems" 
		 style="display: {{ $trashedItemsCount ? 'initial' : 'none' }}">
		<i class="fa fa-trash"></i>&nbsp;contains&nbsp;<big id="trashedItemsCount">{{ $trashedItemsCount }}</big>&nbsp;item{{$trashedItemsCount>1 ? 's' : ''}}: &nbsp;
		<i class="fa fa-list-ul"></i>&nbsp;<a href="#" id="toggleBtn" onclick="toggleTrashed()">Show</a> &nbsp;
		@if( Auth::user()->ownsPlan($plan->id) )
			<a href="{{ url('cspot/plans/'.$plan->id.'/items/trashed/restore') }}" 
				class="text-success nowrap"><i class="fa fa-undo"></i>&nbsp;Restore&nbsp;all</a> &nbsp;
			{{-- check if user is leader of the corresponding plan or author/admin --}}
			@if ( $plan->leader_id==Auth::user()->id || Auth::user()->isAuthor() )
				<a href="{{ url('cspot/plans/'.$plan->id.'/items/trashed/delete') }}" 
					class="text-danger nowrap"><i class="fa fa-trash"></i
						>&nbsp;Delete&nbsp;{{ $trashedItemsCount>1 ? 'all&nbsp;'.$trashedItemsCount : 'trashed' }}&nbsp;permanently</a>
			@endif
		@endif
	</div>

	@if (count($plan->items) > 5)
		@include ('cspot.snippets.add_item_button')
	@endif

@endif


@if ( isset($newest_item_seq_no) )
	<script>
		// go to the just inserted new item
		$(document).ready( function() {
			window.location.href = "#tr-item-{{ $newest_item_seq_no }}";
			// alternative scroll solution, see http://stackoverflow.com/a/13736194/3202115
			// document.getElementById('tr-item-{{ $newest_item_seq_no }}').scrollIntoView();
		});
	</script>
@endif


<script src="https://www.blueletterbible.org/assets/scripts/blbToolTip/BLB_ScriptTagger-min.js" type="text/javascript"></script>
<script type="text/javascript">
	BLB.Tagger.Translation 			= 'ESV';
	BLB.Tagger.HyperLinks 			= 'all'; // 'all', 'none', 'hover'
	BLB.Tagger.HideTanslationAbbrev = false;
	BLB.Tagger.TargetNewWindow 		= true;
	BLB.Tagger.Style 				= 'par'; // 'line' or 'par'
	BLB.Tagger.NoSearchTagNames 	= ''; // HTML element list
	BLB.Tagger.NoSearchClassNames 	= 'noTag doNotTag'; // CSS class list
</script>

