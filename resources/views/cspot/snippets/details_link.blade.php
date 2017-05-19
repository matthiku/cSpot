
{{-- Show link to toggle visibility of the Plan Details --}}

<?php Use Carbon\Carbon; ?>


@if ( Auth::user()->isEditor() && isset($plan) && $plan->date >= \Carbon\Carbon::yesterday() )

    <div class="ml-2 d-inline small">
    	<span class="small">
	        <a href="#" onclick="$('.plan-details').toggle()" class="small">
	        	<span class="plan-details">show</span>
	        	<span class="plan-details hidden">hide</span>
	        	plan details
	    	</a>
    	</span>
    </div>
@endif

<div class="ml-2 d-inline small">
	<span class="small">
        @if ($plan->notes->count()  ||  $plan->info)
            <a href="#" data-toggle="modal" data-target="#addPlanNoteModal" class="small">
                Plan Notes ({{ $plan->notes->count() ? $plan->notes->count() : '1' }})</a>
        @else
            <a href="#" data-toggle="modal" data-target="#addPlanNoteModal" class="small">Add Note</a>
        @endif
	</span>
</div>
