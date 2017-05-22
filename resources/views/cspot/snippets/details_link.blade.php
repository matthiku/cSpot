
{{-- Show link to toggle visibility of the Plan Details --}}

<?php Use Carbon\Carbon; ?>


@if ( Auth::user()->isEditor() && isset($plan) && $plan->date >= \Carbon\Carbon::yesterday() )

    <div class="mr-2 d-inline small nowrap cursor-pointer text-primary">
    	<span class="small">
	        <span onclick="$('.plan-details').toggle()" class="small">
	        	<span class="plan-details">show</span><span class="plan-details hidden">hide</span>&nbsp;details
	    	</span>
    	</span>
    </div>
@endif

<div class="ml-2 d-inline small nowrap cursor-pointer text-primary">
	<span class="small">
        @if ($plan->notes->count()  ||  $plan->info)
            &#128458;<span data-toggle="modal" data-target="#addPlanNoteModal" class="small
                {{ Auth::user()->ownsPlan($plan->id)  && $plan->notesRead()  ? 'plan-notes-alert bg-danger text-white rounded px-1' : '' }}">
                plan&nbsp;notes&nbsp;({{ $plan->notes->count() ? $plan->notes->count() : '1' }})</span>
        @else
            &#128455;<span data-toggle="modal" data-target="#addPlanNoteModal" class="small">add&nbsp;note</span>
        @endif
	</span>
</div>

<script>blink($('.plan-notes-alert'))</script>
