
<?php 
	Use Carbon\Carbon; 
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
?>
    


<h1>{{ $today->format("F Y") }}</h1>

<div class="d-flex">
	<div class="calendar-day rounded text-danger">Sunday</div>
	<div class="calendar-day rounded">Monday</div>
	<div class="calendar-day rounded">Tuesday</div>
	<div class="calendar-day rounded">Wednesday</div>
	<div class="calendar-day rounded">Thursday</div>
	<div class="calendar-day rounded">Friday</div>
	<div class="calendar-day rounded">Saturday</div>
</div>


<section id="calendar" class="d-flex flex-wrap">

	{{-- loop through each day for this month' calendar page --}}
	@while ($firstDay->lte($lastDay))

		<div class="calendar-day rounded">
			<span>{{ $firstDay->day }}</span>
		</div>

		<?php $firstDay->addDay(); ?>
	@endwhile

</section>