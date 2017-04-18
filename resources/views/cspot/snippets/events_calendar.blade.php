
<?php 
	Use Carbon\Carbon; 

	$today = Carbon::today();
	
	if (Request::has('year')) {
		$calendarYear = Request::get('year');
		$startDay  = Carbon::parse('first day of January '. $calendarYear);
	} 
	else
		$startDay  = Carbon::parse('first day of January '. $today->year);

	$calendarYear = $startDay->year;
	
	$lastMonth  = Carbon::parse('last day of December '. $calendarYear);

	// get the current month
	$startMonth = $startDay->day(1);

	$first = true;

	$thisYear = $today->year;
	$selectedYear = $calendarYear;
?>


<div id="calendar-tabs" role="tablist" class="p-0">



	<ul class="d-flex flex-wrap month-names-row">
		<li class="calendar-month-row calendar-year bg-white lh-1">
			<a href="#calendar-years" class="mt-2">
				<span class="link hidden-md-down" onclick="choosePrevNextYearForPlansList();" title="Show previous year">&laquo;</span
				><span class="link" onclick="$('.select-year').show();"
					><red class="hidden-md-down">{{ $calendarYear }}</red
					><red class="hidden-lg-up smaller">{{ $calendarYear }}</red></span
					><span class="link hidden-md-down" onclick="choosePrevNextYearForPlansList('next');" title="Show next year">&raquo;</span>
			</a>
		</li>
		<li class="calendar-month-row calendar-month-1"><a href="#calendar-month-1">J<span class="hidden-sm-down">an</span><span class="hidden-lg-down">uary</span></a></li>
		<li class="calendar-month-row calendar-month-2"><a href="#calendar-month-2">F<span class="hidden-sm-down">eb</span><span class="hidden-lg-down">ruary</span></a></li>
		<li class="calendar-month-row calendar-month-3"><a href="#calendar-month-3">M<span class="hidden-sm-down">ar</span><span class="hidden-lg-down">ch</span></a></li>
		<li class="calendar-month-row calendar-month-4"><a href="#calendar-month-4">A<span class="hidden-sm-down">pr</span><span class="hidden-lg-down">il</span></a></li>
		<li class="calendar-month-row calendar-month-5"><a href="#calendar-month-5">M<span class="hidden-sm-down">ay</span></a></li>
		<li class="calendar-month-row calendar-month-6"><a href="#calendar-month-6">J<span class="hidden-sm-down">un</span><span class="hidden-lg-down">e</span></a></li>
		<li class="calendar-month-row calendar-month-7"><a href="#calendar-month-7">J<span class="hidden-sm-down">ul</span><span class="hidden-lg-down">y</span></a></li>
		<li class="calendar-month-row calendar-month-8"><a href="#calendar-month-8">A<span class="hidden-sm-down">ug</span><span class="hidden-lg-down">ust</span></a></li>
		<li class="calendar-month-row calendar-month-9"><a href="#calendar-month-9">S<span class="hidden-sm-down">ep</span><span class="hidden-lg-down">tember</span></a></li>
		<li class="calendar-month-row calendar-month-10"><a href="#calendar-month-10">O<span class="hidden-sm-down">ct</span><span class="hidden-lg-down">ober</span></a></li>
		<li class="calendar-month-row calendar-month-11"><a href="#calendar-month-11">N<span class="hidden-sm-down">ov</span><span class="hidden-lg-down">ember</span></a></li>
		<li class="calendar-month-row calendar-month-12"><a href="#calendar-month-12">D<span class="hidden-sm-down">ec</span><span class="hidden-lg-down">ember</span></a></li>
	</ul>


	<span class="hidden select-year form-inline bg-info rounded p-1" style="z-index: 555; position: absolute; top: 10px; left: 5px; max-width: 170px;">
		<span class="ml-3 link float-right" onclick="$('.select-year').hide();">X</span>
		<select class="custom-select form-control form-control-sm pt-0 pb-0" 
				onchange="addYearToPlansListSelection(this)">
			@for ($year = $firstYear; $year<=$thisYear; $year++)
				<option {{ $selectedYear == $year ? 'selected' : '' }} 
					value="{{ $year }}">{{ $year }}</option>
			@endfor
		</select>
	</span>


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
					<div class="calendar-col pt-1 bg-info rounded mr-1"><h3>Thurs<span class="hidden-sm-down">day</span></h3></div>
					<div class="calendar-col pt-1 bg-info rounded mr-1"><h3>Friday</h3></div>
					<div class="calendar-col pt-1 bg-info rounded">	   <h3>Sat<span class="hidden-sm-down">urday</span></h3></div>
				</div>
				

				<div class="d-flex mt-1 calendar-week">



{{-- no indentation below in order to avoid too many blank-filled lines in the HTML source! --}}


{{-- loop through each day for this month' calendar page --}}
@for ($i = 0; $i < 44; $i++)

	@if ($i > 6  &&  $i % 7  ==  0)
		{{-- after 7 days, start a new row --}}
</div><div class="d-flex mt-1 calendar-week">
	@endif
<div class="calendar-day{{
		$firstDay==$today 
			? ' calendar-day-today' 
			: '' }}{{ 
		$startDay->month != $firstDay->month 
			? ' bg-gray' 
			: ' bg-white' }}{{
		($firstDay < $today && $startDay->month == $firstDay->month) 
	   		? ' calendar-day-past' 
	   		: '' }}{{ 
		$i%7<6 
			? ' mr-1' 
			: '' }} rounded">

<div><h3 class="mb-0 p-0 rounded float-right{{ 
	$startDay->month != $firstDay->month 
		? ' text-muted' 
		: '' }}{{ 
			$firstDay->dayOfWeek==0 
				? ' text-danger' 
				: '' }}">{{ 
					$firstDay->day 
}}</h3></div>
@foreach ($allPlans as $plan)
  @if ($plan->date->toDateString() == $firstDay->toDateString())
	<a href="{{ url('cspot/plans/'.$plan->id) }}/edit" class="d-block lh-1 mb-1 items-calendar-day{{ 
		$firstDay < $today ? ' text-muted' : '' 
			}}{{ $plan->leader_id==Auth::user()->id || $plan->teacher_id==Auth::user()->id ? ' text-info' : '' }}"
		title="Click to open. Leader: {{ 
			$plan->leader 
				? $plan->leader_id==Auth::user()->id ? 'You' : $plan->leader->name 
				: 'unknown' 
			}}{{ 
				$plan->teacher_id>0
					? ', Teacher: '.($plan->teacher_id==Auth::user()->id ? 'You' : $plan->teacher->name)
					: '' }}">
		<span class="hidden-sm-down text-success font-weight-bold mr-1">{{ $plan->date->format("H:i") }}</span>{!!
			$plan->type->generic
				? ($plan->items->count() ? '<sup>'.$plan->items->count().'</sup> ' : ' ') . $plan->subtitle
				: $plan->type->name . 
					($plan->items->count() ? '<sup>'.$plan->items->count().'</sup> ' : ' ') . 
					($plan->subtitle ? '<small class="hidden-md-down">('.$plan->subtitle.')</small>' : ' ') 
			!!}</a>
  @endif
@endforeach{!!
	(Auth::user()->isEditor() && $firstDay >= $today) 
		? '<a href="'.url('cspot/plans/create').'?date='.$firstDay->toDateString()
			. '" title="Create a new event for this day" class="cal-new-event-btn btn btn-sm btn-outline-info text-muted link py-0 align-top">+</a>'
		: '' !!}</div>

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
	// activate the jQuery ui TABS element
	$( "#calendar-tabs" ).tabs({
		active: {{ $calendarYear < $today->year ? '12' : $today->month }},
		disabled: [0],
		activate: function() {
			calculateEventsPerMonth();
			setIdealCalendarRowHeight();
		}
	});

	// calculate and show the amount of events per month
	function calculateEventsPerMonth()
	{
		var inmonth, outmonth, allyear=0;
		for (var i = 1; i < 13; i++) {
			// do we already have the count being shown?
			if ($('li.calendar-month-'+i+'>.calendar-month-show-events-count').length==0) {
				outmonth = $('#calendar-month-'+i+'>div>.calendar-week>.calendar-day.bg-gray>a.d-block').length;
				inmonth  = $('#calendar-month-'+i+'>div>.calendar-week>.calendar-day>a.d-block').length;
				allyear += inmonth - outmonth;
				jQuery('<small/>', { 
						text: '('+(inmonth-outmonth)+')',
						class: 'calendar-month-show-events-count',
						title: 'events count for this month'
					})
					.appendTo('li.calendar-month-'+i)
			}

			// correctly position the counter in the top-right corner
			$('li.calendar-month-'+i+'>.calendar-month-show-events-count')
				.position({ 
					my: 'right-2 top-1',
					at: 'right top',
					of: 'li.calendar-month-'+i 
				});
		}		
		// add the counter for the whole year
		if ( $('li.calendar-year>.calendar-year-show-events-count').length == 0 )
			jQuery('<small/>', { 
					text: '('+(allyear)+')',
					class: 'calendar-year-show-events-count',
					title: 'events count for the whole year'
				})
				.appendTo('li.calendar-year')
		// correctly position the counter in the top-right corner
		$('li.calendar-year>.calendar-year-show-events-count')
			.position({ 
				my: 'right-1 top-1',
				at: 'right top',
				of: 'li.calendar-year'
			});
	}

	// once the page is fully loaded, properly format the calendar and show the events counters
	$(document).ready( function() 
	{
		setIdealCalendarRowHeight();

		calculateEventsPerMonth();
		$(window).resize( function() {
			calculateEventsPerMonth();
			setIdealCalendarRowHeight();
		})
	})

</script>
