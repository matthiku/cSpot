
<?php 
	Use Carbon\Carbon; 

	// get the earliest date we have in the list of events
	$startDay = $plans->first()->date;

	// get the current month
	$startMonth = $plans->first()->date->day(1);
	$lastMonth  = $plans->last()->date;

	$first = true;
?>


<div id="accordion" role="tablist" aria-multiselectable="true">

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

// echo 'startMonth: '.$startMonth.'<br>';
// echo 'lastMonth: '.$lastMonth.'<br><br>';
// echo 'firstDay: '.$firstDay.'<br>';
// echo 'lastDay: '.$lastDay.'<br>';
// dd();
	?>


  	<div class="card">

		<div class="card-header" role="tab" id="heading{{$startMonth->month}}">
		  	<h3 class="mb-0">
		    	<a class="lora" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$startMonth->month}}" aria-expanded="true" aria-controls="collapse{{$startMonth->month}}">
		      	{{ $startDay->format("F Y") }}
		    	</a>
		  	</h3>
		</div>

		<div id="collapse{{$startMonth->month}}" class="collapse{{ $first ? ' show' : ''}}" role="tabpanel" aria-labelledby="heading{{$startMonth->month}}">
		  	<div class="card-block p-1">

				<div class="d-flex flex-column">


					<div class="d-flex korn">
						<div class="calendar-day bg-info rounded mr-1 text-danger"><h3>Sunday</h3></div>
						<div class="calendar-day bg-info rounded mr-1"><h3>Monday</h3></div>
						<div class="calendar-day bg-info rounded mr-1"><h3>Tues<span class="hidden-xs-down">day</span></h3></div>
						<div class="calendar-day bg-info rounded mr-1"><h3>Wed<span class="hidden-sm-down">nesday</span></h3></div>
						<div class="calendar-day bg-info rounded mr-1"><h3>Thurs<span class="hidden-xs-down">day</span></h3></div>
						<div class="calendar-day bg-info rounded mr-1"><h3>Friday</h3></div>
						<div class="calendar-day bg-info rounded">	   <h3>Sat<span class="hidden-sm-down">urday</span></h3></div>
					</div>
					

					<div class="d-flex mt-1">

						{{-- loop through each day for this month' calendar page --}}
						@for ($i = 0; $i < 44; $i++)

							@if ($i > 6  &&  $i % 7  ==  0)
								{{-- every 7 days, start a new row --}}
								</div><div class="d-flex mt-1">
							@endif


							<div class="calendar-day rounded{{ $startDay->month != $firstDay->month ? ' bg-white' : '' }}{{ $i%7<6 ? ' mr-1' : '' }}">

								<h2 class="mb-0{{ $startDay->month != $firstDay->month ? ' text-muted' : '' }}">{{ $firstDay->day }}</h2>

								@foreach ($plans as $plan)

									@if ($plan->date->toDateString() == $firstDay->toDateString())

										<a href="{{ url('cspot/plans/'.$plan->id) }}/edit" class="d-block"
											title="Leader: {{ $plan->leader ? $plan->leader->name : 'unknown' }}, Teacher: {{ $plan->teacher ? $plan->teacher->name : 'n/a' }}">
											<span class="hidden-sm-down text-success">{{ $plan->date->format("H:i") }}</span>
											{{ $plan->type->generic ? $plan->subtitle : $plan->type->name }}
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
	    </div>
	</div>


	<?php 
		$startDay = $firstDay;
		$first = false;
	?>

	@endwhile
</div>
