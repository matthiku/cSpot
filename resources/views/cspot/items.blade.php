@include( 'cspot/snippets/modal', ['modalContent' => '$modalContent', 'modalTitle' => '$modalTitle' ] )

<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<div class="table-responsive">
	<table class="table table-striped table-items
		{{ count($plan->items)>5 ? 'table-sm' : ''}} {{ count($plan->items)>10 ? 'table-xs' : ''}}">
		<thead class="thead-default hidden-xs-up">
			<tr>
				<th class="center dont-print" data-placement="right"
						data-toggle="tooltip" title="Drag and Drop items to move them to a different position in the plan!"
					><span class="hidden-sm-down">Order</span>
					</th>

				<th class="hidden-md-down center always-print"
						data-toggle="tooltip" title="Hymn book reference. MP='Mission Praise'"
					>Book#</th>

				<th class="hidden-lg-down"       
						data-toggle="tooltip" title="Title and subtitle (if any) of the selected song."
					><i class="fa fa-music"></i> Song Title</th>

				<th class="hidden-lg-down center"
						data-toggle="tooltip" title="Bible Readings, additional comments or description of activity."
					>Comment/Bible Reference</th>

				<th class="hidden-xl-up center"  
						data-toggle="tooltip" title="Song Title and/or activity description."
					>Title/Comment</th>

				<th class="hidden-sm-down center dont-print"
						data-toggle="tooltip" title="Lyrics with chords for guitars"
					><small><i class="fa fa-file-code-o"></i></small></th>

				<th class="hidden-sm-down center dont-print"
						data-toggle="tooltip" title="Sheet music attached to the song?"
					><small><i class="fa fa-music"></i></small></th>

				<th class="hidden-sm-down center dont-print"
						data-toggle="tooltip" title="Are there files (like announcements) attached to this item?"
					><small><i class="fa fa-file-picture-o"></i></small></th>

				<th class="hidden-xs-down center dont-print"
						data-toggle="tooltip" title="Links to YouTube videos or sheetmusic for song items."
					>Play</th>

				@if( Auth::user()->ownsPlan($plan->id) )
					<th class="center dont-print">Action</th>
				@endif
			</tr>
		</thead>


		<tbody 
			@if( Auth::user()->ownsPlan($plan->id) )
				id="tbody-items"
			@endif
			>
	    @foreach( $plan->items as $item )

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

			<tr id="tr-item-{{ $item->seq_no }}" data-item-id="{{ $item->id }}"
					class="{{$item->deleted_at ? 'trashed text-muted' : ''}} {{ $newest_item_id==$item->id ? 'bg-khaki newest-item' : '' }}">


				<td class="drag-item dont-print" scope="row" title="drag item into the new position">
					<span class="hidden-sm-down pull-xs-right text-success">{{ $item->seq_no }}</span>
					<i class="p-r-1 fa fa-bars">
				</td>


				<td {{$onclick}} {{$tooltip}} class="hidden-md-down center link always-print">
					{{ ($item->song_id) ? $item->song->book_ref : '' }}</td>


				{{-- show seperate column for song title and comment on large devices --}}
				<td {{$onclick}} class="hidden-lg-down link" @if ($item->song_id)
						title="{{ substr($item->song->lyrics,0,500) }}" data-toggle="tooltip" 
						@if ($item->seq_no<10)
							data-placement="bottom"
						@endif
						data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><pre class="tooltip-inner tooltip-wide"></pre></div>'
					@endif
					>
					@if($item->song_id) 
						{{ $item->song->title }} 
						{{ $item->song->title_2 ? ' ('. $item->song->title_2 .')' : '' }}
					@endif
				</td>

				<td onclick="editItemComment(this)" class="hidden-lg-down center"
					data-action-url="{{ route('cspot.api.items.update', $item->id) }}">
					@if ( substr($item->comment, 0,4 )=='http' )
						<a href="{{ $item->comment }}" target="new">
							{{ $item->comment }}
							<i class="fa fa-globe"></i>
						</a>
					@else
						<span class="comment-textcontent">{{ $item->comment }}</span>
						<!-- allow for inline editing -->
						<span class="fa fa-pencil text-muted"></span>
					@endif
				</td>


				{{-- show combined song-title and comment column on small devices --}}
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



				<td class="hidden-sm-down center" title="Lyrics with chords for guitars">
					@if ($item->song_id)
						@if ( strlen($item->song->chords)>20 )
							<a href="{{ url('cspot/items').'/'.$item->id }}/chords">
								<i class="fa fa-file-code-o"></i></a>
						@endif
					@endif
				</td>

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

				<td {{$onclick}} class="hidden-sm-down center link"
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
				</td>



				<td class="center hidden-xs-down dont-print">
					<big>
					@if ($item->song_id)
	                    @if ( $item->song->hymnaldotnet_id > 0 )
	                        <a target="new" title="See on hymnal.net" data-toggle="tooltip"
	                            href="https://www.hymnal.net/en/hymn/h/{{ $item->song->hymnaldotnet_id }}">
	                            <i class="fa fa-music"></i> </a> &nbsp; 
	                    @endif
	                    @if ( strlen($item->song->youtube_id)>0 )
	                        <a href="#" title="Play here" class="red" data-toggle="tooltip"
	                        	onclick="showYTvideoInModal('{{ $item->song->youtube_id }}', '{{ $item->song->title }}')">
	                            <i class="fa fa-youtube-play"></i></a>
                            <a title="Play in new tab" data-toggle="tooltip" target="new" class="pull-xs-right"
                            	href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}">
                            	<i class="fa fa-external-link"></i></a>
	                    @endif
					@endif
					</big>
				</td>

				{{--  _______________________________________________

							ACTION buttons 
					________________________________________________
				 --}}
				<td class="text-xs-right text-nowrap dont-print">
					{{-- 'start presentation' button visible for all --}}
					<a class=" hidden-xs-down" data-toggle="tooltip" data-placement="left" title="Start presentation from here" 
						href='{{ url('cspot/items/'.$item->id) }}/present'>
						&nbsp;<i class="fa fa-tv fa-lg"></i>&nbsp;</a>
					@if( Auth::user()->ownsPlan($plan->id) && $plan->date > \Carbon\Carbon::yesterday() )
						@if ($item->deleted_at)
							<a class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="left" title="Restore this item" 
								href='{{ url('cspot/items/'.$item->id) }}/restore'>
								<i class="fa fa-undo"></i></a>

        					{{-- check if user is leader of the corresponding plan or author/admin --}}
							@if ( $item->plan->leader_id==Auth::user()->id || Auth::user()->isAuthor() )
								<a class="text-danger" data-toggle="tooltip" title="Delete permanently!" 
									href='{{ url('cspot/items/'.$item->id) }}/permDelete'>
									&nbsp;<i class="fa fa-trash fa-lg"></i></a>
							@endif
						@else
							{{-- insert new item before the current --}}
							<a class="btn btn-secondary btn-sm" data-toggle="tooltip"
								data-placement="left" title="add song here" 
								href='{{ url('cspot/plans/'.$plan->id) }}/items/create/before/{{$item->id}}'>
								<i class="fa fa-reply"></i></a>

							{{-- new MODAL POPUP to add a song --}}
							<button type="button" class="btn btn-secondary btn-sm text-info" data-toggle="modal" data-target="#searchSongModal"
								data-plan-id="{{$plan->id}}" data-item-id="{{$item->id}}" data-seq-no="{{$item->seq_no}}" 
								href='#' title="search for song to insert before this item">
								<i class="fa fa-plus"></i><i class="fa fa-music"></i>
							</button>

							@if ($item->song_id)
			 					<a class="hidden-sm-down" data-toggle="tooltip" title="Edit Song" 
									href='{{ url('cspot/songs/'.$item->song->id) }}/edit/'>
									<span class="fa-stack">
										<i class="fa fa-pencil-square-o fa-stack-2x text-muted"></i>
										<i class="fa fa-music fa-stack-1x"></i>
									</span>
								</a>
							@else
			 					<a class="hidden-sm-down" data-toggle="tooltip" title="Edit Item" 
									href='{{ url('cspot/plans/'.$plan->id) }}/items/{{$item->id}}/edit/'>
									&nbsp;&nbsp;<i class="fa fa-pencil fa-lg"></i>&nbsp;</a>
							@endif

							<a class="text-warning hidden-md-down" data-toggle="tooltip" title="Remove" 
								href='{{ url('cspot/items/'.$item->id) }}/delete'>
								&nbsp;<i class="fa fa-trash fa-lg"></i></a>

						@endif
					@endif
				</td>


			</tr>

	    @endforeach

		</tbody>

	</table>
</div>


@if( Auth::user()->ownsPlan($plan->id) )

	@if( $trashedItemsCount )
		<div class="pull-xs-right m-l-2">
			<i class="fa fa-trash"></i>&nbsp;contains&nbsp;<big>{{ $trashedItemsCount }}</big>&nbsp;item{{$trashedItemsCount>1 ? 's' : ''}}: &nbsp;
			<i class="fa fa-list-ul"></i>&nbsp;<a href="#" id="toggleBtn" onclick="toggleTrashed()">Show</a> &nbsp;
			@if( Auth::user()->ownsPlan($plan->id) )
				<a href="{{ url('cspot/plans/'.$plan->id.'/items/trashed/restore') }}" 
					class="text-success nowrap"><i class="fa fa-undo"></i>&nbsp;Restore&nbsp;all</a> &nbsp;
				{{-- check if user is leader of the corresponding plan or author/admin --}}
				@if ( $item->plan->leader_id==Auth::user()->id || Auth::user()->isAuthor() )
					<a href="{{ url('cspot/plans/'.$plan->id.'/items/trashed/delete') }}" 
						class="text-danger nowrap"><i class="fa fa-trash"></i
							>&nbsp;Delete&nbsp;{{ $trashedItemsCount>1 ? 'all&nbsp;'.$trashedItemsCount : 'trashed' }}&nbsp;permanently</a>
				@endif
			@endif
		</div>
	@endif

	<div class="pull-xs-left">
		<a class="btn btn-sm btn-primary-outline"  onclick="showSpinner()"
			href='{{ url('cspot/plans/'.$plan->id) }}/items/create/{{isset($item) ? $item->seq_no+1 : 1}}'>
			<i class="fa fa-plus"> </i> &nbsp; Add item {{ isset($item) ? $item->seq_no+1 : 1 }}.0
		</a>
	</div>

@endif


<script src="https://www.blueletterbible.org/assets/scripts/blbToolTip/BLB_ScriptTagger-min.js" type="text/javascript"></script>
<script type="text/javascript">
	BLB.Tagger.Translation = 'ESV';
	BLB.Tagger.HyperLinks = 'all'; // 'all', 'none', 'hover'
	BLB.Tagger.HideTanslationAbbrev = false;
	BLB.Tagger.TargetNewWindow = true;
	BLB.Tagger.Style = 'par'; // 'line' or 'par'
	BLB.Tagger.NoSearchTagNames = ''; // HTML element list
	BLB.Tagger.NoSearchClassNames = 'noTag doNotTag'; // CSS class list
</script>

