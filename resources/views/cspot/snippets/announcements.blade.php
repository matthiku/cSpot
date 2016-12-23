
<?php 
	use Carbon\Carbon;
	$day   = 0; // we start the table with Sunday and we need a total of 8 days
	$hour  = 9; // the day starts at 9 a.m.
	$today = $item->plan->date; 
	$firstThisDay = true;	// first event of the day?
	$nextWeek 	  = $item->plan->date->addDays(7);
?>

<div class="announce-text-present">


	<div class="text-success font-weight-bold font-italic display-3 mb-1">
		<img class="float-xs-left" height="140px" src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}">
		<img class="float-xs-right" height="140px" src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}">
		<div class="header mb-0" style="line-height: 1.0; vertical-align: middle;">This Week's Announcements
			<div class="smaller text-muted">Week from {{$today->formatLocalized('%d %b')}} to {{$nextWeek->formatLocalized('%d %b')}}</div>
		</div>
	</div>


	<table class="table table-bordered">

		<thead>
			<tr>
				<th class="announce-text-present pb-0 pt-0 text-danger">Today</th>
				<th class="announce-text-present pb-0 pt-0">Monday</th>
				<th class="announce-text-present pb-0 pt-0">Tuesday</th>
				<th class="announce-text-present pb-0 pt-0">Wed.</th>
				<th class="announce-text-present pb-0 pt-0">Thurs.</th>
				<th class="announce-text-present pb-0 pt-0">Friday</th>
				<th class="announce-text-present pb-0 pt-0">Saturday</th>
				<th class="announce-text-present pb-0 pt-0 text-danger">Sunday</th>
			</tr>
		</thead>

		<tbody>

			<tr>
				<td style="vertical-align: initial; line-height: 1;">
				@foreach ($events as $event)

					<?php 
						// extra treatment for the first event
						if ( $event->date->isSameDay($item->plan->date) ) {
							// push down the event when it's start time is later in the day
							while ($event->date->hour - $hour > 1) {
								echo '<br>';
								$hour += 1;
							}
						}

						// if this event is before the ongoing event, ignore it.
						if ( $event->date->dayOfYear == $today->dayOfYear  &&  $event->date->hour < $today->hour )
							continue;

						// if this event is private, ignore it.
						if ( $event->private )
							continue;

						// event is not on this day, so insert a new column
						if ($event->date->gt($today)) {
							$today->addDay()->setTime(23,59,59);
							$hour = 9;
							$day += 1;
							$firstThisDay = true;
							echo '</td><td style="vertical-align: initial; line-height: 1;">';
							// push down the event's start time if it is later in the day
							while ($event->date->hour - $hour > 1) {
								echo '<br>';
								$hour += 1;
							}
						}

						// insert an empty column until we are at the event date
						while ($event->date->gt($today) ) {
							echo '</td><td style="vertical-align: initial; line-height: 1;">';
							$today->addDay();
							$day += 1;
							$hour = 9;
							$firstThisDay = true;
							// push down the event's start time if it is later in the day
							while ($event->date->hour - $hour > 1) {
								echo '<br>';
								$hour += 1;
							}
						}

						// for the 2nd event in a day, make sure it is pushed down if it is later in the day
						if (! $firstThisDay) {

							while ($event->date->hour - $hour > 1) {
								echo '<br>';
								$hour += 1;
							}
						}


						// show times in hh:mm am format
						Carbon::setToStringFormat('g:i a');

						// now show the actual event data
					?>
					<div class="{{ $firstThisDay ? '' : 'mt-2'}}">
						<span class="d-block bg-info nowrap">{{ $event->date.' '.$event->date->dayOfYear.' '.$today->dayOfYear }}</span>
						<div>{{ $event->type->generic ? '' : $event->type->name }}{!! $event->type->generic ? '' : '<br>' !!}
							 {!! $event->subtitle ? '<span class="text-muted">'.$event->subtitle.'</span>' : '' !!}</div>
					</div>

					<?php 
						$firstThisDay = false;
						$hour += 3;
						// reset date formatting
						Carbon::resetToStringFormat(); 
					?>


				@endforeach
				@while ( $day < 7 )
					<td>
						<br><br><br><br><br><br><br><br><br>
					</td>
					<?php $day += 1; ?>
				@endwhile

				</td>
			</tr>

		</tbody>
	</table>

</div>
