
<table class="table table-striped table-bordered 
			@if(count($plan->items)>3)
			 table-sm
			@endif
			 ">
	<thead class="thead-default">
		<tr>
			<th>#</th>
			<th>Song ID</th>
			<th>Song No.</th>
			<th>Title</th>
			<th>Comment</th>
			<th>Version</th>
			<th>Key</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
    @foreach( $plan->items as $item )
		<tr>
			<th scope="row">{{ $item->seq_no }}</th>
			<td>{{ $item->song_id }}</td>
			<td>{{ $item->song->number }}</td>
			<td>{{ $item->song->title }}</td>
			<td>{{ $item->comment }}</td>
			<td>{{ $item->version }}</td>
			<td>{{ $item->key }}</td>
			<td>
				<!-- <a class="btn btn-secondary btn-sm" title="Show Users" href='/cspot/items/{{$item->id}}'><i class="fa fa-filter"></i></a> -->
				 @if( Auth::user()->isEditor() )
					<a class="btn btn-primary-outline btn-sm" title="Edit" href='/cspot/items/{{$item->id}}/edit'><i class="fa fa-pencil"></i></a>
					<a class="btn btn-danger btn-sm" title="Delete!" href='/cspot/items/{{$item->id}}/delete'><i class="fa fa-trash"></i></a>
				@endif
			</td>
		</tr>
    @endforeach
	</tbody>
</table>
