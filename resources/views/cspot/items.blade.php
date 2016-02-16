
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<div class="table-responsive">
	<table class="table table-striped table-bordered {{ count($plan->items)>5 ? 'table-sm' : ''}} {{ count($plan->items)>10 ? 'table-xs' : ''}}">
		<thead class="thead-default">
			<tr>
				@if( Auth::user()->ownsPlan($plan->id) )
					<th class="text-right" data-toggle="tooltip" title="Move an item one place up or down.">
						Re-order</th>
				@endif
				<th class="hidden-sm-down center">Order</th>
				<th class="hidden-xs-down center">Book Ref.</th>
				<th class="hidden-sm-down"       >Title</th>
				<th class="hidden-sm-down center">Comment</th>
				<th class="hidden-md-up center"  >Title/Comment</th>
				<th class="hidden-md-down center">Version</th>
				<th class="hidden-lg-down center"><small>Chords?</small></th>
				<th class=" center">Links</th>
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

				<td {{$onclick}} class="hidden-sm-down" @if ($item->song_id)
						title="{{ $item->song->lyrics }}" data-toggle="tooltip" data-placement="bottom"
						data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><pre class="tooltip-inner tooltip-wide"></pre></div>'
					@endif
					>
					@if($item->song_id) 
						{{ $item->song->title }} 
						{{ $item->song->title_2 ? ' ('. $item->song->title_2 .')' : '' }}
					@endif
				</td>

				<td {{$onclick}} {{$tooltip}} class="hidden-sm-down center link">{{ $item->comment }}</td>

				<td {{$onclick}} {{$tooltip}} class="hidden-md-up center link">
					{{ $item->song_id ? $item->song->title.', ' : '' }}{{ $item->comment }}</td>

				<td {{$onclick}} {{$tooltip}} class="hidden-md-down center link">{{ $item->version }}</td>

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
								href='{{ url('cspot/plans/'.$plan->id) }}/items/create/{{$item->seq_no-0.1}}'><i class="fa fa-reply"></i></a>

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
				<a href="#" class="text-success"><i class="fa fa-undo"></i>&nbsp;Restore&nbsp;all</a> &nbsp;
				<a href="#" class="text-danger"><i class="fa fa-trash"></i>&nbsp;Delete&nbsp;all&nbsp;permanently</a>
			@endif
		</div>
	@endif

@endif
