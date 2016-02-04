
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

<div class="table-responsive">
	<table class="table table-striped table-bordered {{ count($plan->items)>5 ? 'table-sm' : ''}} {{ count($plan->items)>10 ? 'table-xs' : ''}}">
		<thead class="thead-default">
			<tr>
				@if( Auth::user()->isEditor() || Auth::user()->id==$plan->leader_id || Auth::user()->id==$plan->teacher_id )
					<th class="text-right">Move item</th>
				@endif
				<th class="hidden-sm-down">Item</th>
				<th class="hidden-md-down">Song No.</th>
				<th class="hidden-xs-down">Book Ref.</th>
				<th class="hidden-sm-down">Title</th>
				<th class="hidden-sm-down">Comment</th>
				<th class="hidden-md-up">Title/Comment</th>
				<th class="hidden-md-down">Version</th>
				<th class="hidden-md-down">Key</th>
				@if( Auth::user()->isEditor() || Auth::user()->id==$plan->leader_id || Auth::user()->id==$plan->teacher_id )
					<th>Action</th>
				@endif
			</tr>
		</thead>
		<tbody>
	    @foreach( $plan->items as $item )
			<tr class="link" onclick="location.href ='{{ url('cspot/plans/'.$plan->id.'/items/'.$item->id) }}/edit'">
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
				<td class="hidden-sm-down" scope="row">{{ $item->seq_no }}</td>
				<td class="hidden-md-down">{{ ($item->song_id) ? $item->song->song_no : '' }}</td>
				<td class="hidden-xs-down">
					{{ ($item->song_id) ? $item->song->book_ref : '' }}
				</td>
				<td class="hidden-sm-down" @if ($item->song_id)
						title="{{ $item->song->lyrics }}"
					@endif
					>
					@if($item->song_id) 
						{{ $item->song->title }} 
						{{ $item->song->title_2 ? ' ('. $item->song->title_2 .')' : '' }}
					@endif
				</td>
				<td class="hidden-sm-down">{{ $item->comment }}</td>
				<td class="hidden-md-up">{{ $item->song_id ? $item->song->title.', ' : '' }}{{ $item->comment }}</td>
				<td class="hidden-md-down">{{ $item->version }}</td>
				<td class="hidden-md-down">{{ $item->key }}</td>
				@if( Auth::user()->isEditor() || Auth::user()->id==$plan->leader_id || Auth::user()->id==$plan->teacher_id )
				<td class="nowrap">
					<a class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="left" title="Insert earlier item" 
						href='{{ url('cspot/plans/'.$plan->id) }}/items/create/{{$item->seq_no-0.1}}'><i class="fa fa-reply"></i></a>
					<a class="btn btn-primary-outline btn-sm" data-toggle="tooltip" title="Edit" 
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

