
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
