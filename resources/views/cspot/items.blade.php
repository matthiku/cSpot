
<table class="table table-striped table-bordered 
			@if(count($plan->items)>3)
			 table-sm
			@endif
			 ">
	<thead class="thead-default">
		<tr>
			<th>#</th>
			<th class="hidden-md-down">Song ID</th>
			<th>Song No.</th>
			<th>Title</th>
			<th>Comment</th>
			<th class="hidden-md-down">Version</th>
			<th class="hidden-md-down">Key</th>
			@if( Auth::user()->isEditor() || Auth::user()->id==$plan->leader_id || Auth::user()->id==$plan->teacher_id )
				<th>Action</th>
			@endif
		</tr>
	</thead>
	<tbody>
    @foreach( $plan->items as $item )
		<tr>
			<th scope="row">{{ $item->seq_no }}</th>
			<td class="hidden-md-down">{{ ($item->song_id) ? $item->song_id : '' }}</td>
			<td>{{ ($item->song_id) ? $item->song->number : '' }}</td>
			<td>{{ ($item->song_id) ? $item->song->title : '' }}</td>
			<td>{{ $item->comment }}</td>
			<td class="hidden-md-down">{{ $item->version }}</td>
			<td class="hidden-md-down">{{ $item->key }}</td>
			@if( Auth::user()->isEditor() || Auth::user()->id==$plan->leader_id || Auth::user()->id==$plan->teacher_id )
			<td>
				<a class="btn btn-secondary btn-sm" title="Insert earlier item" href='/cspot/items/create/{{$plan->id}}/{{$item->seq_no-0.1}}'><i class="fa fa-plus"></i></a>
				<a class="btn btn-primary-outline btn-sm" title="Edit" href='/cspot/plans/{{$plan->id}}/items/{{$item->id}}/edit/'><i class="fa fa-pencil"></i></a>
				<a class="btn btn-danger btn-sm" title="Delete!" href='/cspot/items/{{$item->id}}/delete'><i class="fa fa-trash"></i></a>
			</td>
			@endif
		</tr>
    @endforeach
	</tbody>
</table>

@if( Auth::user()->isEditor() || Auth::user()->id==$plan->leader_id || Auth::user()->id==$plan->teacher_id )
	<a class="btn btn-primary-outline" href='/cspot/items/create/{{$plan->id}}/{{$item->seq_no+1}}'>
		<i class="fa fa-plus"> </i> &nbsp; Add item {{$item->seq_no+1}}
	</a>
@endif

<hr>
