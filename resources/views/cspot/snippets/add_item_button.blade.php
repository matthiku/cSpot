
{{-- new MODAL POPUP to add song/scripture/comment --}}
<div class="pull-xs-left">

	<button     type="button" class="btn btn-outline-primary btn-sm" 
	     data-toggle="modal" data-target="#searchSongModal" data-item-action="insert-item"
	    data-plan-id="{{$plan->id}}" data-item-id="after-{{ isset($item) ? $item->id : '0' }}" 
	     data-seq-no="after-{{ isset($item) ? $item->seq_no : '0' }}"
	           title="Append new Song, Scripture or Comment to this plan">
	    <i class="fa fa-plus"></i> &nbsp; Add item {{ isset($item) ? $item->seq_no+1 : 1 }}.0
	</button>

</div>