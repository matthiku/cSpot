
<?php 
	use Carbon\Carbon; 
	$hour  = 9;
	$today = $item->plan->date; 
	$nextWeek = $item->plan->date->addDays(7);
?>

<div class="announce-text-present">


	<div class="text-success font-weight-bold font-italic display-3 m-b-1">
		<img class="pull-xs-left" height="140px" src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}">
		<img class="pull-xs-right" height="140px" src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}">
		<div class="header m-b-0" style="line-height: 1.0; vertical-align: middle;">This Week's Announcements
			<div class="small text-muted">Week from {{$today->formatLocalized('%d %b')}} to {{$nextWeek->formatLocalized('%d %b')}}</div>
		</div>
	</div>


	<table class="table table-bordered">

		<thead>
			<tr>
				<th class="announce-text-present p-b-0 p-t-0 text-danger">Today</th>
				<th class="announce-text-present p-b-0 p-t-0">Monday</th>
				<th class="announce-text-present p-b-0 p-t-0">Tuesday</th>
				<th class="announce-text-present p-b-0 p-t-0">Wed.</th>
				<th class="announce-text-present p-b-0 p-t-0">Thurs.</th>
				<th class="announce-text-present p-b-0 p-t-0">Friday</th>
				<th class="announce-text-present p-b-0 p-t-0">Saturday</th>
				<th class="announce-text-present p-b-0 p-t-0 text-danger">Sunday</th>
			</tr>
		</thead>

		<tbody>

			<tr>
				<td style="vertical-align: initial; line-height: 1;">
				@foreach ($events as $event)

					<?php 
						// extra treatment for the first event
						if ( $event->date->dayOfYear == $item->plan->date->dayOfYear ) {
							// push down the event it's start time is later in the day
							while ($event->date->hour - $hour > 1) {
								echo '<br>';
								$hour += 1;
							}
						}

						// if this event is before the ongoing event, ignore it.
						if ( $event->date->dayOfYear == $today->dayOfYear  &&  $event->date->hour < $today->hour ) {
							continue;
						}

						// event is not on this day, so insert a new column
						if ($event->date->dayOfYear > $today->dayOfYear) {
							$today->addDay();
							$hour = 9;
							echo '</td><td style="vertical-align: initial; line-height: 1;">';
							// push down the event it's start time is later in the day
							while ($event->date->hour - $hour > 1) {
								echo '<br>';
								$hour += 1;
							}
						}

						// insert an empty column until we are at the event date
						while ($event->date->dayOfYear > $today->dayOfYear ) {
							echo '</td><td style="vertical-align: initial; line-height: 1;">';
							$today->addDay();
							$hour = 9;
							// push down the event it's start time is later in the day
							while ($event->date->hour - $hour > 1) {
								echo '<br>';
								$hour += 1;
							}
						}

						// show times in hh:mm am format
						Carbon::setToStringFormat('g:i a');
					?>

					<span class="d-block bg-info nowrap m-t-1">{{ $event->date }}</span>
					<span>{{ $event->type->name }}</span>
					{!! $event->subtitle ? '<br><span class="small">'.$event->subtitle.'</span>' : '' !!}

					<?php 
						// reset date formatting
						Carbon::resetToStringFormat(); 
					?>


				@endforeach					
				</td>
			</tr>

		</tbody>
	</table>

</div>
