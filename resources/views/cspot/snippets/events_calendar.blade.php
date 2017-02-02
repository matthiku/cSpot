
<?php 
	Use Carbon\Carbon; 

	if ($allPlans->count()) {
		// get the earliest date we have in the list of events
		$startDay = $allPlans->first()->date;

		// get the current month
		$startMonth = $allPlans->first()->date->day(1);
		$lastMonth  = $allPlans->last()->date;
	} 

	$first = true;

	if ($allPlans->count()) {
?>


<div id="calendar-tabs" role="tablist" class="p-0">



	<ul class="d-flex flex-wrap">
		<li class="calendar-month-row">
			<a href="#calendar-years">
				<span class="link" onclick="choosePrevNextYearForPlansList();" title="Show previous year">&laquo;</span><red>{{ 
					$startDay->format("Y") }}</red>@if ($startDay->year < Carbon::today()->year)<span 
						class="link" onclick="choosePrevNextYearForPlansList('next');" title="Show newer year">&raquo;</span>
					@endif
			</a>
		</li>
		<li class="calendar-month-row"><a href="#calendar-month-1">Jan<span class="hidden-md-down">uary</span></a></li>
		<li class="calendar-month-row"><a href="#calendar-month-2">Feb<span class="hidden-md-down">ruary</span></a></li>
		<li class="calendar-month-row"><a href="#calendar-month-3">Mar<span class="hidden-md-down">ch</span></a></li>
		<li class="calendar-month-row"><a href="#calendar-month-4">Apr<span class="hidden-md-down">il</span></a></li>
		<li class="calendar-month-row"><a href="#calendar-month-5">May</a></li>
		<li class="calendar-month-row"><a href="#calendar-month-6">Jun<span class="hidden-md-down">e</span></a></li>
		<li class="calendar-month-row"><a href="#calendar-month-7">Jul<span class="hidden-md-down">y</span></a></li>
		<li class="calendar-month-row"><a href="#calendar-month-8">Aug<span class="hidden-md-down">ust</span></a></li>
		<li class="calendar-month-row"><a href="#calendar-month-9">Sep<span class="hidden-md-down">tember</span></a></li>
		<li class="calendar-month-row"><a href="#calendar-month-10">Oct<span class="hidden-md-down">ober</span></a></li>
		<li class="calendar-month-row"><a href="#calendar-month-11">Nov<span class="hidden-md-down">ember</span></a></li>
		<li class="calendar-month-row"><a href="#calendar-month-12">Dec<span class="hidden-md-down">ember</span></a></li>
	</ul>



	@while ($startMonth->timestamp < $lastMonth->timestamp)

		<?php 

			// find the first Sunday to show
			$firstDay = Carbon::parse('first day of ' .  $startDay->format("F Y"));
			// if the first day of this month is not a Sunday, we go back to the previous Sunday
			if ($firstDay->dayOfWeek != 0)
				$firstDay = Carbon::parse('last Sunday of ' .  $startMonth->copy()->subMonth()->format("F Y"));

			// find the last day to show
			$lastDay = Carbon::parse('last day of ' .  $startMonth->format("F Y"));
			// if the last day of a month is not a Saturday, we extend to the Saturday of the following month
			if ($lastDay->dayOfWeek != 6)
				$lastDay  = Carbon::parse('first Saturday of '.$startMonth->copy()->addMonth()->format("F Y"));
		?>


		<div id="calendar-years"></div>

		<div id="calendar-month-{{$startMonth->month}}" class="p-0{{ $first ? ' active' : ''}}">

			<div class="d-flex flex-column calendar-month">


				<div class="d-flex mt-1 korn">
					<div class="calendar-col pt-1 bg-info rounded mr-1 text-danger"><h3>Sunday</h3></div>
					<div class="calendar-col pt-1 bg-info rounded mr-1"><h3>Monday</h3></div>
					<div class="calendar-col pt-1 bg-info rounded mr-1"><h3>Tues<span class="hidden-xs-down">day</span></h3></div>
					<div class="calendar-col pt-1 bg-info rounded mr-1"><h3>Wed<span class="hidden-sm-down">nesday</span></h3></div>
					<div class="calendar-col pt-1 bg-info rounded mr-1"><h3>Thurs<span class="hidden-xs-down">day</span></h3></div>
					<div class="calendar-col pt-1 bg-info rounded mr-1"><h3>Friday</h3></div>
					<div class="calendar-col pt-1 bg-info rounded">	   <h3>Sat<span class="hidden-sm-down">urday</span></h3></div>
				</div>
				

				<div class="d-flex mt-1 calendar-week">

{{-- avoid too many blank-filled lines in the HTML source! --}}
{{-- loop through each day for this month' calendar page --}}
@for ($i = 0; $i < 44; $i++)

	@if ($i > 6  &&  $i % 7  ==  0)
		{{-- after 7 days, start a new row --}}
</div><div class="d-flex mt-1 calendar-week">
	@endif
<div class="calendar-day {{ $firstDay==Carbon::today() ? 'calendar-day-today' : '' }}
	rounded{{ $startDay->month != $firstDay->month ? ' bg-gray' : ' bg-white' }}{{ $i%7<6 ? ' mr-1' : '' }}">

<h3 class="mb-0 py-0 {{ $startDay->month != $firstDay->month ? ' text-muted' : '' }}">{{ $firstDay->day }}
	@if ($firstDay >= Carbon::today())
<a href="{{ url('cspot/plans/by_date') }}/{{ $firstDay->toDateString() }}" title="Add an event for this day" class="float-right text-muted link small">+</a>
	@endif
</h3>
@foreach ($allPlans as $plan)
@if ($plan->date->toDateString() == $firstDay->toDateString())
	<a href="{{ url('cspot/plans/'.$plan->id) }}/edit" class="d-block"
		title="Click to open. Leader: {{ $plan->leader ? $plan->leader->name : 'unknown' }}, Teacher: {{ $plan->teacher ? $plan->teacher->name : 'n/a' }}">
		<span class="hidden-sm-down text-success font-weight-bold">{{ $plan->date->format("H:i") }}</span>
		@if ($plan->type->generic)
			{{ $plan->subtitle }}
		@else
			{{ $plan->type->name }}
			{!! $plan->subtitle ? '<small>('.$plan->subtitle.')</small>' : '' !!}
		@endif
	</a>
@endif
@endforeach
	</div>

	<?php 
		$firstDay->addDay(); 

		if ($firstDay->month != $startMonth->month)
			$startMonth = $firstDay;
	?>
	@break($firstDay->gt($lastDay))
@endfor

				</div>
			</div>

	    </div>


		<?php 
			$startDay = $firstDay;
			$first = false;
		?>

	@endwhile
</div>

<script>
$( "#calendar-tabs" ).tabs({
	active: {{ Carbon::today()->month }},
	disabled: [0],
});
</script>
 
<?php 
	}
?>