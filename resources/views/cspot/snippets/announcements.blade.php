
<?php use Carbon\Carbon; ?>

<div class="announce-text-present">


	<h1 class="text-success font-weight-bold font-italic display-3 m-b-2">
		<img class="pull-xs-left" height="140px" src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}">
		<span style="line-height: 140px; vertical-align: middle;">This Week's Announcements</span>
		<img class="pull-xs-right" height="140px" src="{{ url($logoPath.env('CHURCH_LOGO_FILENAME')) }}">
	</h1>


	<table class="table table-bordered">

		<thead>
			<tr>
				<th class="announce-text-present text-danger">Today</th>
				<th class="announce-text-present">Monday</th>
				<th class="announce-text-present">Tuesday</th>
				<th class="announce-text-present">Wed.</th>
				<th class="announce-text-present">Thurs.</th>
				<th class="announce-text-present">Friday</th>
				<th class="announce-text-present">Saturday</th>
				<th class="announce-text-present text-danger">Sunday</th>
			</tr>
		</thead>

		<tbody>

			<tr>
				<?php 
					$hour = 9;
					$today = $item->plan->date; 
				?>

				<td style="vertical-align: initial;">
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
							echo '</td><td style="vertical-align: initial;">';
							// push down the event it's start time is later in the day
							while ($event->date->hour - $hour > 1) {
								echo '<br>';
								$hour += 1;
							}
						}

						// insert an empty column until we are at the event date
						while ($event->date->dayOfYear > $today->dayOfYear ) {
							echo '</td><td style="vertical-align: initial;">';
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

					<span class="d-block bg-info nowrap">{{ $event->date }}</span>
					<span>{{ $event->type->name }}</span>

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
