@include( 'cspot/snippets/modal', ['modalContent' => '$modalContent', 'modalTitle' => '$modalTitle' ] )

<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<div class="table-responsive">
	<table class="table table-items
		{{ count($plan->items)>5 ? 'table-sm' : ''}} {{ count($plan->items)>10 ? 'table-xs' : ''}}">

		<tbody id="tbody-items">
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

			<tr id="tr-item-{{ str_replace('.', '-', $item->seq_no) }}" data-item-id="{{ $item->id }}" 
				data-old-song-id="{{ ($item->song_id) ? $item->song->id : 'NULL' }}"
				class="{{$item->deleted_at ? 'trashed text-muted' : ''}} {{ $newest_item_id==$item->id ? 'bg-khaki newest-item' : '' }}">


				<td class="drag-item dont-print" scope="row" title="drag item into the new position">
					<span class="hidden-sm-down pull-xs-right text-success">{{ $item->seq_no }}</span>
					<i class="p-r-1 fa fa-bars">
				</td>


				<td class="hidden-md-down center always-print show-songbook-ref" title="click to change"
					data-toggle="modal" data-target="#searchSongModal" data-item-id="{{ $item->id }}"
					data-plan-id="update-song" data-item-id="{{$item->id}}" data-seq-no="{{ $item->seq_no }}" 
					data-action-url="{!! route('cspot.api.items.update', $item->id) !!}">
					{{ ($item->song_id) ? $item->song->book_ref : '' }}</td>


				{{-- show separate column for song title and comment on large devices --}}
				<td class="hidden-lg-down show-song-title" 
					@if ($item->song_id)
						title="{{ substr($item->song->lyrics,0,500) }}" data-toggle="tooltip" 
						@if ($item->seq_no<10)
							data-placement="bottom"
						@endif
						data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><pre class="tooltip-inner tooltip-wide"></pre></div>'
					@endif
					>
					<span class="hover-show" 
						data-toggle="modal" data-target="#searchSongModal" data-item-id="{{ $item->id }}"
						data-plan-id="update-song" data-item-id="{{$item->id}}" data-seq-no="{{ $item->seq_no }}" 
						data-action-url="{!! route('cspot.api.items.update', $item->id) !!}">
						@if($item->song_id) 
							{{ $item->song->title }} 
							{{ $item->song->title_2 ? ' ('. $item->song->title_2 .')' : '' }}
						@endif
					</span>
					<span class="hover-only fa fa-pencil text-muted"></span>
				</td>

				<td onclick="editItemComment(this)" class="hidden-lg-down center comment-cell" title="click to change"
					data-action-url="{!! route('cspot.api.items.update', $item->id) !!}">
					@if ( substr($item->comment, 0,4 )=='http' )
						<a href="{{ $item->comment }}" target="new">
							{{ $item->comment }}
							<i class="fa fa-globe"></i>
						</a>
					@else
						<span class="comment-textcontent hover-show">{{ $item->comment }}</span>
						<!-- allow for inline editing -->
						<span class="{{ $item->comment ? 'hover-only' : ''}} fa fa-pencil text-muted"></span>
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



				<td class="center hidden-xs-down dont-print show-youtube-links">
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
					@if (! $item->deleted_at)
					<a class=" hidden-xs-down" data-toggle="tooltip" data-placement="left" title="Start presentation from here" 
						href='{{ url('cspot/items/'.$item->id) }}/present'>
						&nbsp;<i class="fa fa-tv fa-lg"></i>&nbsp;</a>
					@endif
					@if( Auth::user()->ownsPlan($plan->id) && $plan->date > \Carbon\Carbon::yesterday() )
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
							{{-- insert new item before the current --}}
							<!-- <a class="btn btn-secondary btn-sm" data-toggle="tooltip"
								data-placement="left" title="add song here" 
								href='{{ url('cspot/plans/'.$plan->id) }}/items/create/before/{{$item->id}}'>
								<i class="fa fa-reply"></i></a> -->

							{{-- new MODAL POPUP to add a song --}}
							<button type="button" class="btn btn-secondary btn-sm text-info" data-toggle="modal" data-target="#searchSongModal"
								data-plan-id="{{$plan->id}}" data-item-id="{{$item->id}}" data-seq-no="{{$item->seq_no}}" 
								href='#' title="insert song, scripture or comment before this item">
								<i class="fa fa-plus"></i><i class="fa fa-music"></i>
							</button>

							@if ($item->song_id)
			 					<a class="btn btn-secondary btn-sm hidden-sm-down edit-song-link" data-toggle="tooltip" title="Edit Song" 
									href='{{ url('cspot/songs/'.$item->song->id) }}/edit/'>
										<i class="fa fa-music"></i><i class="fa fa-pencil text-muted"></i>
								</a>
							@else
			 					<a class="btn btn-secondary btn-sm hidden-sm-down" data-toggle="tooltip" title="Edit Item" 
									href='{{ url('cspot/plans/'.$plan->id) }}/items/{{$item->id}}/edit/'>
									&nbsp;&nbsp;<i class="fa fa-pencil fa-lg"></i>&nbsp;</a>
							@endif

							<a class="text-warning hidden-md-down" data-toggle="tooltip" title="Remove this item" data-placement="left"
								href='#' onclick="removeItem(this)"
								data-action-url="{!! route('cspot.api.items.delete', $item->id) !!}">
								&nbsp;<i class="fa fa-trash fa-lg"></i></a>

						@endif
					@endif
				</td>


			</tr>

	    @endforeach

		</tbody>

	</table>
</div>


@if( Auth::user()->ownsPlan($plan->id) && $plan->date > \Carbon\Carbon::yesterday() )

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

