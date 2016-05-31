@include( 'cspot/snippets/modal', ['modalContent' => '$modalContent', 'modalTitle' => '$modalTitle' ] )

<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover
		{{ count($plan->items)>5 ? 'table-sm' : ''}} {{ count($plan->items)>10 ? 'table-xs' : ''}}">
		<thead class="thead-default">
			<tr>
				<th class="center dont-print" data-placement="right"
						data-toggle="tooltip" title="Drag and Drop items to move them to a different position in the plan!"
					><span class="hidden-sm-down">Order</span>
					</th>

				<th class="hidden-xs-down center always-print"
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

				<th class="hidden-xl-down center"
						data-toggle="tooltip" title="Instructions for musicians etc."
					>Instructions</th>

				<th class="hidden-lg-down center"
						data-toggle="tooltip" title="Lyrics with chords for guitars"
					><small><i class="fa fa-file-code-o"></i></small></th>

				<th class="hidden-lg-down center"
						data-toggle="tooltip" title="Sheet music attached to the song?"
					><small><i class="fa fa-music"></i></small></th>

				<th class="hidden-lg-down center"
						data-toggle="tooltip" title="Image/File attached to the item?"
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
				@if ($item->deleted_at)
					class="trashed text-muted"
				@endif
				>


				<td class="drag-item dont-print" scope="row" title="drag item into the new position">
					<span class="hidden-sm-down pull-xs-right">{{ $item->seq_no }}</span>
					<i class="p-r-1 fa fa-bars">
				</td>


				<td {{$onclick}} {{$tooltip}} class="hidden-xs-down center link always-print">
					{{ ($item->song_id) ? $item->song->book_ref : '' }}</td>


				<!-- show seperate column for song title and comment on large devices -->
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

				<td {{$onclick}} {{$tooltip}} class="hidden-lg-down center link">
					{{ $item->comment }}</td>


				<!-- show combined song-title and comment column on small devices -->
				<td {{$onclick}} {{$tooltip}} class="hidden-xl-up link">
					@if ($item->song_id)
						<i class="fa fa-music"></i>&nbsp;{{ $item->song->title }}
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



				<td {{$onclick}} {{$tooltip}} class="hidden-xl-down center link">
					{{ $item->key }}</td>


				<td class="hidden-lg-down center">
					@if ($item->song_id)
						@if ( strlen($item->song->chords)>20 )
							<i class="fa fa-check"></i>
						@endif
					@endif
				</td>

				<td class="hidden-lg-down center" title="Are there files (like sheet music) attached to this song?">
					@if ($item->song_id)
						@if ( count($item->song->files)>0 )
							<i class="fa fa-check"></i>
						@endif
					@endif
				</td>

				<td {{$onclick}} class="hidden-lg-down center" title="Are there files/images (like announcements) attached to the item?">
					@if ( count($item->files)>0 )
						<i class="fa fa-check"></i>
					@endif
				</td>



				<td class="hidden-xs-down center dont-print">
					<big>
					@if ($item->song_id)
	                    @if ( $item->song->hymnaldotnet_id > 0 )
	                        <a target="new" title="See on hymnal.net" data-toggle="tooltip"
	                            href="https://www.hymnal.net/en/hymn/h/{{ $item->song->hymnaldotnet_id }}">
	                            <i class="fa fa-music"></i> </a> &nbsp; 
	                    @endif
	                    @if ( strlen($item->song->youtube_id)>0 )
	                        <a href="#" title="Play on YouTube" class="red" data-toggle="tooltip"
	                        	onclick="showYTvideoInModal('{{ $item->song->youtube_id }}')">
	                             <i class="fa fa-youtube-play"></i></a>
	                    @endif
					@endif
					</big>
				</td>


				@if( Auth::user()->ownsPlan($plan->id) )
					<td class="center nowrap dont-print">
						@if ($item->deleted_at)
							<a class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="left" title="Restore this item" 
								href='{{ url('cspot/items/'.$item->id) }}/restore'><i class="fa fa-undo"></i></a>

							<a class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete permanently!" 
								href='{{ url('cspot/items/'.$item->id) }}/permDelete'><i class="fa fa-trash"></i></a>
						@else
							<a class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="left" title="Insert new item before this one" 
								href='{{ url('cspot/plans/'.$plan->id) }}/items/create/before/{{$item->id}}'><i class="fa fa-reply"></i></a>

							@if ($item->song_id)
			 					<a class="hidden-sm-down btn btn-primary-outline btn-sm hidden-md-down" data-toggle="tooltip" title="Edit Song" 
									href='{{ url('cspot/songs/'.$item->song->id) }}/edit/'><i class="fa fa-music"></i></a>
							@else
			 					<a class="hidden-sm-down btn btn-primary-outline btn-sm hidden-md-down" data-toggle="tooltip" title="Edit Item" 
									href='{{ url('cspot/plans/'.$plan->id) }}/items/{{$item->id}}/edit/'><i class="fa fa-pencil"></i></a>
							@endif

							<a class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="left" title="Start presentation from here" 
								href='{{ url('cspot/items/'.$item->id) }}/present'><i class="fa fa-tv"></i></a>

							<a class="btn btn-warning btn-sm hidden-md-down" data-toggle="tooltip" title="Remove" 
								href='{{ url('cspot/items/'.$item->id) }}/delete'><i class="fa fa-trash"></i></a>
						@endif
					</td>
				@endif


			</tr>

	    @endforeach

		</tbody>

	</table>
</div>


@if( Auth::user()->ownsPlan($plan->id) )

	@if( $trashedItemsCount )
		<div class="pull-xs-right">
			<i class="fa fa-trash"></i>&nbsp;contains&nbsp;<big>{{ $trashedItemsCount }}</big>&nbsp;item{{$trashedItemsCount>1 ? 's' : ''}}: &nbsp;
			<i class="fa fa-list-ul"></i>&nbsp;<a href="#" id="toggleBtn" onclick="toggleTrashed()">Show</a> &nbsp;
			@if( Auth::user()->ownsPlan($plan->id) )
				<a href="{{ url('cspot/plans/'.$plan->id.'/items/trashed/restore') }}" 
					class="text-success nowrap"><i class="fa fa-undo"></i>&nbsp;Restore&nbsp;all</a> &nbsp;
				<a href="{{ url('cspot/plans/'.$plan->id.'/items/trashed/delete') }}" 
					class="text-danger nowrap"><i class="fa fa-trash"></i
						>&nbsp;Delete&nbsp;{{ $trashedItemsCount>1 ? 'all&nbsp;'.$trashedItemsCount : 'trashed' }}&nbsp;permanently</a>
			@endif
		</div>
	@endif

	<div class="pull-xs-left">
		<a class="btn btn-sm btn-primary-outline" href='{{ url('cspot/plans/'.$plan->id) }}/items/create/{{isset($item) ? $item->seq_no+1 : 1}}'>
			<i class="fa fa-plus"> </i> &nbsp; Add item {{ isset($item) ? $item->seq_no+1 : 1 }}.0
		</a>
	</div>

@endif


<script src="http://www.blueletterbible.org/assets/scripts/blbToolTip/BLB_ScriptTagger-min.js" type="text/javascript"></script>
<script type="text/javascript">
BLB.Tagger.Translation = 'ESV';
BLB.Tagger.HyperLinks = 'all'; // 'all', 'none', 'hover'
BLB.Tagger.HideTanslationAbbrev = false;
BLB.Tagger.TargetNewWindow = true;
BLB.Tagger.Style = 'par'; // 'line' or 'par'
BLB.Tagger.NoSearchTagNames = ''; // HTML element list
BLB.Tagger.NoSearchClassNames = 'noTag doNotTag'; // CSS class list
</script>