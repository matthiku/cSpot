
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<div class="table-responsive">
	<table class="table table-striped table-bordered {{ count($plan->items)>5 ? 'table-sm' : ''}} {{ count($plan->items)>10 ? 'table-xs' : ''}}">
		<thead class="thead-default">
			<tr>
				@if( Auth::user()->isEditor() || Auth::user()->id==$plan->leader_id || Auth::user()->id==$plan->teacher_id )
					<th class="text-right">Move item</th>
				@endif
				<th class="hidden-sm-down center">Order</th>
				<th class="hidden-lg-down center">CCLI No.</th>
				<th class="hidden-xs-down center">Book Ref.</th>
				<th class="hidden-sm-down">Title</th>
				<th class="hidden-sm-down center">Comment</th>
				<th class="hidden-md-up center">Title/Comment</th>
				<th class="hidden-md-down center">Version</th>
				<th class="hidden-md-down center">Key</th>
				@if( Auth::user()->isEditor() || Auth::user()->id==$plan->leader_id || Auth::user()->id==$plan->teacher_id )
					<th class="center">Insert / Delete</th>
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

			<tr class="link" title="click/touch to edit">

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

				<td onclick={{$onclick}} class="hidden-sm-down center" scope="row">{{ $item->seq_no }}</td>

				<td onclick={{$onclick}} class="hidden-lg-down center">
					{{ $item->song_id ? $item->song->ccli_no : '' }}</td>

				<td onclick={{$onclick}} class="hidden-xs-down center">
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

				<td onclick={{$onclick}} class="hidden-sm-down center">{{ $item->comment }}</td>

				<td onclick={{$onclick}} class="hidden-md-up center">
					{{ $item->song_id ? $item->song->title.', ' : '' }}{{ $item->comment }}</td>

				<td onclick={{$onclick}} class="hidden-md-down center">{{ $item->version }}</td>

				<td onclick={{$onclick}} class="hidden-md-down center">{{ $item->key }}</td>

				@if( Auth::user()->isEditor() || Auth::user()->id==$plan->leader_id || Auth::user()->id==$plan->teacher_id )
					<td class="hidden-sm-down center nowrap">
						<a class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="left" title="Insert earlier item" 
							href='{{ url('cspot/plans/'.$plan->id) }}/items/create/{{$item->seq_no-0.1}}'><i class="fa fa-reply"></i></a>
	 					<a class="btn btn-primary-outline btn-sm hidden-lg-down" data-toggle="tooltip" title="Edit" 
							href='{{ url('cspot/plans/'.$plan->id) }}/items/{{$item->id}}/edit/'><i class="fa fa-pencil"></i></a>
						<a class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete!" 
							href='{{ url('cspot/items/'.$item->id) }}/delete'><i class="fa fa-trash"></i></a>
					</td>
					<td class="hidden-md-up center nowrap">
						<a class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="left" title="Insert earlier item" 
							href='{{ url('cspot/plans/'.$plan->id) }}/items/create/{{$item->seq_no-0.1}}'><i class="fa fa-reply"></i></a>
						<a class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="left" title="Delete!" 
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

