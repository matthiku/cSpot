
{{-- checkbox to indicate if public note should be shown in the presentation --}}

@if (isset($label))
    <label class="custom-control custom-checkbox {{ $label ? '' : 'mr-0 ' }}mb-0">

        <input type="checkbox" 
              class="custom-control-input" {{ $item->show_comment ? 'checked="checked"' : '' }}
            onclick="toggleShowComment(this, 'show_comment-item-id-{{ $item->id }}', '{{ route('cspot.api.item.update') }}')"
            {{ Auth::user()->ownsPlan($plan->id) ? '' : ' disabled' }}>

        <span class="custom-control-indicator"></span>

		@if ($label)
        	<span class="custom-control-description" id="show_comment-item-id-{{ $item->id }}">
        		{{ $item->show_comment ? 'Notes are shown as Title' : 'Show notes as Title' }} in the presentation
        	</span>
        @endif

    </label>
@endif
