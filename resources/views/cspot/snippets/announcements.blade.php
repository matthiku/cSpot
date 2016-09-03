
<?php use Carbon\Carbon; ?>

<div class="text-present">


	<h1 class="text-success font-weight-bold font-italic m-b-2">Announcements for this Week</h1>


	<table class="table table-bordered">

		<thead>
			<tr>
				<th class="center text-danger">Today</th>
				<th class="center">Monday</th>
				<th class="center">Tuesday</th>
				<th class="center">Wed.</th>
				<th class="center">Thurs.</th>
				<th class="center">Friday</th>
				<th class="center">Saturday</th>
				<th class="center text-danger">Sunday</th>
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
