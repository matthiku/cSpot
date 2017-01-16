
{{-- new MODAL POPUP to add song/scripture/comment --}}
<div class="float-left mr-2">

	<button     type="button" class="btn btn-outline-primary btn-sm mr-1" 
	     data-toggle="modal" data-target="#searchSongModal" data-item-action="insert-item"
	    data-plan-id="{{$plan->id}}" data-item-id="after-{{ isset($item) ? $item->id : '0' }}" 
	     data-seq-no="after-{{ isset($item) ? $item->seq_no : '0' }}"
	           title="Append new Song, Scripture or Comment to this plan">
	    <i class="fa fa-plus"></i> &nbsp; Add item {{ isset($item) ? $item->seq_no+1 : 1 }}.0
	</button>

	@if ($plan->items->first()  &&  Auth::user()->ownsPlan($plan->id))
		<button type="button" class="hidden-md-down btn btn-outline-secondary btn-sm"
			onclick="location.href='{{ url('cspot/plans/'.$plan->id) }}/items/{{$plan->items->first()->id}}/edit/'" 
			><small>edit items</small></button>
	@endif

</div>