
<?php 
	use Carbon\Carbon;
	$day   = 0; // we start the table with Sunday and we need a total of 8 days
	$hour  = 9; // the day starts at 9 a.m.
	$columnDay = $item->plan->date; 
	$firstThisDay = true;	// first event of the day?
	$nextWeek 	  = $item->plan->date->addDays(7);
	if (! isset($positioned))
		$positioned = 'no';
?>

<div class="announce-text-present" style="{{ $positioned=='yes' ? 'position: absolute; left: auto; top: 0px; width: 100%;' : '' }}">


	<div class="hidden-lg-down text-success font-weight-bold font-italic display-3 mb-1">
		<img class="float-left" height="140px" src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}">
		<img class="float-right" height="140px" src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}">
		<div class="header mb-0" style="line-height: 1.0; vertical-align: middle;">This Week's Announcements
			<div class="smaller neon-green">Week from {{$columnDay->formatLocalized('%d %b')}} to {{$nextWeek->formatLocalized('%d %b')}}</div>
		</div>
	</div>

	<div class="hidden-xl-up text-success font-weight-bold font-italic display-4 mb-1">
		<img class="float-left" height="60px" src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}">
		<img class="float-right" height="60px" src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}">
		<div class="header mb-0" style="line-height: 1.0; vertical-align: middle;">This Week's Announcements
			<div class="smaller neon-green">Week from {{$columnDay->formatLocalized('%d %b')}} to {{$nextWeek->formatLocalized('%d %b')}}</div>
		</div>
	</div>




	<table class="table table-bordered overflow-hidden">

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

					<script>console.log("-------------------------------------------------------------------------------")</script>

					<?php 
						echo '<script>console.log("+++++ Column date is: " + '.json_encode($columnDay).'.date )</script>';
						echo '<script>console.log("+++++ EVENT  date is: " + '.json_encode($event).'.date )</script>';

						// extra treatment for the first event
						if ( $event->date->isSameDay($item->plan->date) ) {
							// push down the event when it's start time is later in the day
							while ($event->date->hour - $hour > 1) {
								echo '<br>';
								$hour += 1;
							}
						}

						// if this event is before the ongoing event, ignore it.
						if ( $event->date->dayOfYear == $columnDay->dayOfYear  &&  $event->date->hour < $columnDay->hour ) {
							echo '<script>console.log("+++++ ignoring event as it is before the ongoing event")</script>';
							$columnDay->setTime(0,0,1);
							continue;
						}

						// if this event is private, ignore it.
						if ( $event->private )
							continue;

						// event is not on this day, so insert a new column
						if ( $event->date->dayOfYear != $columnDay->dayOfYear ) {
							echo '<script>console.log("+++++ inserting a new column: ");';
							echo 'console.log('.json_encode($event->date).'.date)</script>';
							$columnDay->setTime(0,0,1)->addDay();
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

						/* insert an empty column until we are at the event date
							(we have to compare the dates with having identical times. But in order
							 not to change the original dates, we have to create copies on the fly)
						*/
						while ($event->date->copy()->setTime(0,0,0)->gt($columnDay->copy()->setTime(0,0,0)) ) {
							echo '<script>console.log("+++++ inserting an empty column")</script>';
							echo '</td><td style="vertical-align: initial; line-height: 1;">';
							$columnDay->addDay();
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
						echo '<script>console.log("now showing the actual event data")</script>';
					?>
					<div class="{{ $firstThisDay ? '' : 'mt-2'}}">
						<span class="rounded d-block bg-info nowrap">{{ $event->date }}</span>
						<div>{{ $event->type->generic ? '' : $event->type->name }}{!! $event->type->generic ? '' : '<br>' !!}
							 {!! $event->subtitle ? '<span class="neon-green">'.$event->subtitle.'</span>' : '' !!}</div>
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
