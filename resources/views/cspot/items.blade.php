
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover
		{{ count($plan->items)>5 ? 'table-sm' : ''}} {{ count($plan->items)>10 ? 'table-xs' : ''}}">
		<thead class="thead-default">
			<tr>
				@if( Auth::user()->ownsPlan($plan->id) )
					<th class="text-right" 
						data-toggle="tooltip" data-placement="right" title="Move an item one place up or down. The sequence number will be adjusted accordinglyl"
						>Re-order</th>
				@endif
				<th class="hidden-sm-down center"
						data-toggle="tooltip" title="Numbers are automatically assigned according to the position of the item."
					>Order</th>

				<th class="hidden-xs-down center"
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

				<th class="hidden-sm-down center"
						data-toggle="tooltip" title="Instructions for musicians etc."
					>Instructions</th>

				<th class="hidden-lg-down center"
						data-toggle="tooltip" title="Lyrics with chords for guitars"
					><small>Chords?</small></th>

				<th class="center"
						data-toggle="tooltip" title="Links to YouTube videos or sheetmusic for song items."
					>Links</th>

				@if( Auth::user()->ownsPlan($plan->id) )
					<th class="center">Action</th>
				@endif
			</tr>
		</thead>


		<tbody>
	    @foreach( $plan->items as $item )

			<?php 
				// set variable for click-on-item action
				$onclick = 'onclick=location.href='."'".url('cspot/plans/'.$plan->id.'/items/'.$item->id).'/edit'."' ";
				$tooltip = "title=click/touch&nbsp;for&nbsp;details data-toggle=tooltip" ; 
				// check if there is a song_id but no song in the database!
				if ( $item->song_id && ! $item->song()->exists()) { 
					$item->comment="(Song with id ".$item->song_id.' missing!)'; 
					$item->song_id = Null; 
				} 
			?>

			<tr
				@if ($item->deleted_at)
					class="trashed text-muted"
				@endif
				>

				@if( Auth::user()->ownsPlan($plan->id) )
					@if( $item->deleted_at )
						<td></td>
					@else
						<td class="text-right nowrap">
							@if ($item->seq_no > 1)
								<a class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="right" title="Move up" 
									href='{{ url('cspot/items/'.$item->id) }}/move/earlier'><i class="fa fa-angle-double-up"></i></a>
							@endif
							@if ($item->seq_no < (count($plan->items)-$trashedItemsCount) )
								<a class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="right" title="Move down" 
									href='{{ url('cspot/items/'.$item->id) }}/move/later'><i class="fa fa-angle-double-down"></i></a>
							@endif
						</td>
					@endif
				@endif

				<td class="hidden-sm-down center link" scope="row">{{ $item->seq_no }}</td>

				<td {{$onclick}} {{$tooltip}} class="hidden-xs-down center link">
					{{ ($item->song_id) ? $item->song->book_ref : '' }}</td>


				<td {{$onclick}} class="hidden-lg-down" @if ($item->song_id)
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

				<td {{$onclick}} {{$tooltip}} class="hidden-lg-down center link">{{ $item->comment }}</td>

				<td {{$onclick}} {{$tooltip}} class="hidden-xl-up link">
					@if ($item->song_id )
						<i class="fa fa-music"></i>&nbsp;{{ $item->song->title }}
					@endif
					@if (preg_match('/[(][A-Z]{3}[)]/', $item->comment) )
						<i class="fa fa-book"></i>&nbsp;{{ $item->comment }}
					@else
						{{ $item->comment }}
					@endif
				</td>


				<td {{$onclick}} {{$tooltip}} class="hidden-sm-down center link">{{ $item->key }}</td>

				<td class="hidden-lg-down center">
					@if ($item->song_id)
						@if ( strlen($item->song->chords)>20 )
							<i class="fa fa-check"></i>
						@endif
					@endif
				</td>

				<td class="center">
					<big>
					@if ($item->song_id)
	                    @if ( $item->song->hymnaldotnet_id > 0 )
	                        <a target="new" title="See on hymnal.net" data-toggle="tooltip"
	                            href="https://www.hymnal.net/en/hymn/h/{{ $item->song->hymnaldotnet_id }}">
	                            <i class="fa fa-music"></i> </a> &nbsp; 
	                    @endif
	                    @if ( strlen($item->song->youtube_id)>0 )
	                        <a target="new" title="Play on YouTube" class="red" data-toggle="tooltip"
	                        	href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}">
	                             <i class="fa fa-youtube-play"></i></a>
	                    @endif
					@endif
					</big>
				</td>


				@if( Auth::user()->ownsPlan($plan->id) )
					<td class="center nowrap">
						@if ($item->deleted_at)
							<a class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="left" title="Restore this item" 
								href='{{ url('cspot/items/'.$item->id) }}/restore'><i class="fa fa-undo"></i></a>

							<a class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete permanently!" 
								href='{{ url('cspot/items/'.$item->id) }}/permDelete'><i class="fa fa-trash"></i></a>
						@else
							<a class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="left" title="Insert earlier item" 
								href='{{ url('cspot/plans/'.$plan->id) }}/items/create/before/{{$item->id}}'><i class="fa fa-reply"></i></a>

		 					<a class="hidden-sm-down btn btn-primary-outline btn-sm hidden-lg-down" data-toggle="tooltip" title="Edit" 
								href='{{ url('cspot/plans/'.$plan->id) }}/items/{{$item->id}}/edit/'><i class="fa fa-pencil"></i></a>

							<a class="btn btn-warning btn-sm" data-toggle="tooltip" title="Remove" 
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

	<div class="pull-left">
		<a class="btn btn-sm btn-primary-outline" href='{{ url('cspot/plans/'.$plan->id) }}/items/create/{{isset($item) ? $item->seq_no+1 : 1}}'>
			<i class="fa fa-plus"> </i> &nbsp; Add item {{ isset($item) ? $item->seq_no+1 : 1 }}.0
		</a>
	</div>

	@if( $trashedItemsCount )
		<div class="center">
			This plan contains&nbsp;<big>{{ $trashedItemsCount }}</big>&nbsp;'trashed'&nbsp;item{{$trashedItemsCount>1 ? 's' : ''}}: &nbsp;
			<i class="fa fa-list-ul"></i>&nbsp;<a href="#" id="toggleBtn" onclick="toggleTrashed()">Show</a> &nbsp;
			@if( Auth::user()->ownsPlan($plan->id) )
				<a href="{{ url('cspot/plans/'.$plan->id.'/items/trashed/restore') }}" 
					class="text-success"><i class="fa fa-undo"></i>&nbsp;Restore&nbsp;all</a> &nbsp;
				<a href="{{ url('cspot/plans/'.$plan->id.'/items/trashed/delete') }}" 
					class="text-danger"><i class="fa fa-trash"></i
						>&nbsp;Delete&nbsp;{{ $trashedItemsCount>1 ? 'all&nbsp;'.$trashedItemsCount : 'trashed' }}&nbsp;permanently</a>
			@endif
		</div>
	@endif

@endif
