
<?php 
	Use Carbon\Carbon; 
	use App\Models\Plan;

	$today = Carbon::today();

	// find the first Sunday to show
	$firstDay = Carbon::parse('first day of ' .  $today->format("F Y"));
	if ($firstDay->dayOfWeek != 0)
		// if the first day of this month is not a Sunday, we go back to the previous Sunday
		$firstDay = Carbon::parse('last Sunday of ' .  $today->copy()->subMonth()->format("F Y"));

	// find the last day to show
	$lastDay = Carbon::parse('last day of ' .  $today->format("F Y"));
	if ($lastDay->dayOfWeek != 6)
		$lastDay  = Carbon::parse('first Saturday of '.$today->copy()->addMonth()->format("F Y"));

	$allPlans = Plan::with('type')
				->where('date', '>=', $firstDay)
				->where('date', '<=', $lastDay)
				->orderBy('date')->get();
//dd($allPlans);
?>
    


{{-- <h1>{{ $today->format("F Y") }}</h1>
 --}}

<div id="calendar" class="d-flex flex-column mt-2">


	<div class="d-flex">
		<div class="calendar-day rounded text-danger"><h2>Sunday</h2></div>
		<div class="calendar-day rounded"><h2>Monday</h2></div>
		<div class="calendar-day rounded"><h2>Tuesday</h2></div>
		<div class="calendar-day rounded"><h2>Wednesday</h2></div>
		<div class="calendar-day rounded"><h2>Thursday</h2></div>
		<div class="calendar-day rounded"><h2>Friday</h2></div>
		<div class="calendar-day rounded"><h2>Saturday</h2></div>
	</div>



	<div class="d-flex">

		{{-- loop through each day for this month' calendar page --}}
		@for ($i = 0; $i < 45; $i++)

			@if ($i > 6  &&  $i % 7  ==  0)
				</div><div class="d-flex">
			@endif

			@php // get all events for this date
				// $dayPlans = $allPlans->where('date', 'like', $firstDay->toDateString()."%")->all();
			@endphp


			<div class="calendar-day overflow-hidden rounded{{ $today->month != $firstDay->month ? ' bg-faded' : '' }}">

				<h1 class="mb-0{{ $today->month != $firstDay->month ? ' text-muted' : '' }}">{{ $firstDay->day }}</h1>

				@foreach ($allPlans as $plan)
					@if ($plan->date->toDateString() == $firstDay->toDateString())
						<a href="{{ url('cspot/plans/'.$plan->id) }}/edit" class="d-block">
							<span class="hidden-sm-down">{{ $plan->date->format("H:i") }}</span>
							{{ $plan->type->generic ? $plan->subtitle : $plan->type->name }}
						</a>
					@endif
				@endforeach
			</div>

			<?php $firstDay->addDay(); ?>
			@break($firstDay->gt($lastDay))

		@endfor

	</div>

</div>