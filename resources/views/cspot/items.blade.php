
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<div class="table-responsive">
	<table class="table table-striped table-bordered {{ count($plan->items)>5 ? 'table-sm' : ''}} {{ count($plan->items)>10 ? 'table-xs' : ''}}">
		<thead class="thead-default">
			<tr>
				@if( Auth::user()->isEditor() || Auth::user()->id==$plan->leader_id || Auth::user()->id==$plan->teacher_id )
					<th class="text-right" data-toggle="tooltip" title="Move an item one place up or down.">
						Move item</th>
				@endif
				<th class="hidden-sm-down center">Order</th>
				<th class="hidden-xs-down center">Book Ref.</th>
				<th class="hidden-sm-down">Title</th>
				<th class="hidden-sm-down center">Comment</th>
				<th class="hidden-md-up center">Title/Comment</th>
				<th class="hidden-md-down center">Version</th>
				<th class="hidden-lg-down center"><small>Chords?</small></th>
				<th class="hidden-sm-down center">Links</th>
				@if( Auth::user()->isEditor() || Auth::user()->id==$plan->leader_id || Auth::user()->id==$plan->teacher_id )
					<th class="center">Action</th>
				@endif
			</tr>
		</thead>


		<tbody>
	    @foreach( $plan->items as $item )

			<?php 
				$onclick = 'location.href='."'".url('cspot/plans/'.$plan->id.'/items/'.$item->id).'/edit'."'"; 
				// check if there is a song id but the song is not i the database
				if ( $item->song_id && ! $item->song()->exists()) { 
					$item->comment="(Song with id ".$item->song_id.' missing!)'; 
					$item->song_id = Null; 
				} 
			?>

			<tr title="click/touch for details" data-toggle="tooltip">

				@if( Auth::user()->isEditor() || Auth::user()->id==$plan->leader_id || Auth::user()->id==$plan->teacher_id )
					<td class="text-right nowrap">
						@if ($item->seq_no > 1)
							<a class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="left" title="Move up" 
								href='{{ url('cspot/items/'.$item->id) }}/move/earlier'><i class="fa fa-angle-double-up"></i></a>
						@endif
						@if ($item->seq_no < count($plan->items))
							<a class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="right" title="Move down" 
								href='{{ url('cspot/items/'.$item->id) }}/move/later'><i class="fa fa-angle-double-down"></i></a>
						@endif
					</td>
				@endif

				<td class="hidden-sm-down center link" scope="row">{{ $item->seq_no }}</td>

				<td onclick={{$onclick}} class="hidden-xs-down center link">
					{{ ($item->song_id) ? $item->song->book_ref : '' }}</td>

				<td onclick={{$onclick}} class="hidden-sm-down" @if ($item->song_id)
						title="{{ $item->song->lyrics }}"
					@endif
					>
					@if($item->song_id) 
						{{ $item->song->title }} 
						{{ $item->song->title_2 ? ' ('. $item->song->title_2 .')' : '' }}
					@endif
				</td>

				<td onclick={{$onclick}} class="hidden-sm-down center link">{{ $item->comment }}</td>

				<td onclick={{$onclick}} class="hidden-md-up center link">
					{{ $item->song_id ? $item->song->title.', ' : '' }}{{ $item->comment }}</td>

				<td onclick={{$onclick}} class="hidden-md-down center link">{{ $item->version }}</td>

				<td class="hidden-lg-down center">
					@if ($item->song_id)
						@if ( strlen($item->song->chords)>20 )
							<i class="fa fa-check"></i>
						@endif
					@endif
				</td>

				<td class="hidden-sm-down center">
					<big>
					@if ($item->song_id)
	                    @if ( $item->song->hymnaldotnet_id > 0 )
	                        <a target="new" title="See on hymnal.net" 
	                            href="https://www.hymnal.net/en/hymn/h/{{ $item->song->hymnaldotnet_id }}">
	                            <i class="fa fa-music"></i> </a> &nbsp; 
	                    @endif
	                    @if ( strlen($item->song->youtube_id)>0 )
	                        <a target="new" title="Play on YouTube" class="red" 
	                        	href="https://www.youtube.com/watch?v={{ $item->song->youtube_id }}">
	                             <i class="fa fa-youtube-play"></i></a>
	                    @endif
					@endif
					</big>
				</td>


				@if( Auth::user()->isEditor() || Auth::user()->id==$plan->leader_id || Auth::user()->id==$plan->teacher_id )
					<td class="center nowrap">

						<a class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="left" title="Insert earlier item" 
							href='{{ url('cspot/plans/'.$plan->id) }}/items/create/{{$item->seq_no-0.1}}'><i class="fa fa-reply"></i></a>

	 					<a class="hidden-sm-down btn btn-primary-outline btn-sm hidden-lg-down" data-toggle="tooltip" title="Edit" 
							href='{{ url('cspot/plans/'.$plan->id) }}/items/{{$item->id}}/edit/'><i class="fa fa-pencil"></i></a>

						<a class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete!" 
							href='{{ url('cspot/items/'.$item->id) }}/delete'><i class="fa fa-trash"></i></a>
					</td>
				@endif


			</tr>

	    @endforeach

		</tbody>

	</table>
</div>


@if( Auth::user()->isEditor() || Auth::user()->id==$plan->leader_id || Auth::user()->id==$plan->teacher_id )
	<a class="btn btn-sm btn-primary-outline" href='{{ url('cspot/plans/'.$plan->id) }}/items/create/{{isset($item) ? $item->seq_no+1 : 1}}'>
		<i class="fa fa-plus"> </i> &nbsp; Add item {{ isset($item) ? $item->seq_no+1 : 1 }}.0
	</a>
@endif

