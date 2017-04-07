
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')


@section('title', $heading)


@if (Request::is('*/future'))
	@section('future', 'active')
@else
	@section('plans', 'active')
@endif


@section('content')


	@include('layouts.flashing')
	


	{{-- Event List navigation bar 
	--}}
	<div class="row mx-0">
		<div class="col-12 bg-faded py-lg-2 py-1">

			@if( Auth::user()->isEditor() )
				<a class="btn btn-sm btn-outline-success float-right" 
					href="{{ url('cspot/plans/create') }}{{ 
						( Request::has('filterby') && Request::input('filterby')=='type' && Request::has('filtervalue') ) 
							? '?type_id='.Request::input('filtervalue') 
							: '' }}">
					<i class="fa fa-plus"> </i>&nbsp; Create New<span class="hidden-md-down"> Event</span>
				</a>
			@endif



			{{-- SINGLE Event type: Drop-Down menu to select which event type to show --}}
			<form class="form-inline float-right mr-2">
				<div class="form-group">
					@php
						$thisYear = Carbon\Carbon::today()->year;
						$selectedYear = Request::has('year') ? Request::get('year') : '';
					@endphp
					<select class="custom-select form-control form-control-sm pt-0 pb-0" id="selectYear" 
						 onchange="addYearToPlansListSelection(this)">

						<option value="all" {{ Request::has('year') ? '' : 'selected' }}>
							Select Year ... 
						</option>

						@for ($year = $firstYear; $year<=$thisYear; $year++)
							<option {{ $selectedYear == $year ? 'selected' : '' }} 
								value="{{ $year }}">{{ $year }}</option>
						@endfor

					</select>
				</div>
			</form>




			{{-- MULTIPLE Event Type: Checkboxes to filter which event types to show --}}
			<form class="form-inline float-sm-right mr-2">
				<div class="dropdown" id="multi-filter-dropdown" data-url="{{url('cspot/plans')}}?filterby=type&filtervalue=">
					{{-- check if request already is filtering out some plans --}}
					@if ( Request::has('filtervalue') )
						@php 
							$fv = json_decode(Request::input('filtervalue')); 
							if ($fv==Request::input('filtervalue'))		// make sure $fv is always an array!
								$fv = [Request::input('filtervalue')]; 
						@endphp
					@endif
					<button class="btn btn-sm dropdown-toggle {{ isset($fv) && sizeof($fv) ? 'btn-primary' : 'btn-secondary ' }}" 
						type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Show All Of:
					</button>
					<div class="dropdown-menu bg-faded padding-half" aria-labelledby="dropdownMenuButton">
						<h5 class="dropdown-header px-0">Select which types to show:</h5>
						<div class="form-check justify-content-start">
		  					<label class="form-check-label label-normal text-warning" onclick="selectServiceType()">
		    					<input type="checkbox" class="form-check-input" name="multi-filter" checked="true" 
		    							value="futureOnly">
		    					(show only future plans)
		  					</label>
						</div>
						@foreach ($types as $type)
							<div class="form-check justify-content-start">
			  					<label class="form-check-label label-normal" onclick="selectServiceType()">
			    					<input type="checkbox" class="form-check-input" name="multi-filter" 
											{{ isset($fv) && in_array($type->id, $fv) ? 'checked="true"' : '' }}
			    							value="option-{{$type->id}}">
			    					{{ $type->name }}
			  					</label>
							</div>
						@endforeach
						<button type="button" class="btn btn-primary btn-sm btn-block mt-1" onclick="selectServiceType('submit')">Submit selection</button>
					</div>
				</div>
			</form>



			{{-- Time and User Filter: Drop-Down menu to select which events show: future or all, single user or all users --}}
			<form class="form-inline float-right mr-2">
				<div class="form-group">
					<select class="custom-select form-control form-control-sm pt-0 pb-0" id="showfilter" 
						 onchange="toogleAllorFuturePlans($(this).val());">
						<option 
							value="nothing" class="small">Select all/future/etc.:</option>
						<option 
							value="user-all">All my events</option>
						<option 
							value="user-future">My upcoming events</option>
						<option 
							value="allusers-future">All upcoming events</option>
						<option 
							@if ( Request::get('filterby')=='user'   &&  Request::get('show')=='all'   &&   strval(Request::get('filtervalue'))=='all')
								selected
							@endif
							value="allusers-all">All events</option>
					</select>
				</div>
			</form>



		    <h4 class="float-left text-success lora m-0">

		    	<span>{{ $heading }}</span>

				<small class="small ml-3" style="font-size: 50%">
					<a href="#" onclick="$('.events-table').toggle();">
						&#128197; show 
						<span class="events-table hidden">calendar</span>
						<span class="events-table">as list</span></a>
				</small>
		    </h4>

			<center>
				@if ( get_class($plans)=='Illuminate\Pagination\LengthAwarePaginator' && $plans->lastPage() > 1 )
					<span class="events-table hidden">Page {{ $plans->currentPage() }} of {{ $plans->lastPage() }}</span>
				@endif

				@if ( isset($plans)  &&  get_class($plans) != 'Illuminate\Database\Eloquent\Collection' )
					<small>(Total: {{ $plans->total() }} Events)</small>
				@endif
			</center>

		</div>
	</div>





	{{-- show the events in a calendar-like table 
	--}}
	<div class="events-table calendar-container">

		@include ('cspot.snippets.events_calendar')	

	</div>




	@if ( isset($plans) && count($plans) )


		{{-- As an alternative, show events as a list (table) 
		--}}
		<table class="events-table hidden table table-striped table-hover{{ count($plans)>15 ? ' table-sm' : '' }}">

			<thead class="thead-default">
				<tr>
					<th class="hidden-lg-down center">#</th>

					@include('cspot.snippets.theader', ['thfname' => 'date', 'thdisp' => 'Date', 'thsearch'=>false, 'thclass'=>''])
					
					@include('cspot.snippets.theader', ['thfname' => 'type_id', 'thdisp' => 'Service Type', 'thsearch'=>false, 'thclass'=>''])

					<th class="hidden-lg-down center" title="Additional info that appears on the Announcements Slide">Info</th>

					<th class="hidden-lg-down center">Times</th>

					@if( Auth::user()->isUser() )
						<th class="hidden-lg-down center small pa-0" title="Is this a non-public event? (It won't show up on the announcements)">Priv.?</th>

						<th class="center" title="Check when you are available for that particular plan"
							data-toggle="tooltip" title="Check when you are available for that particular plan">
							<small>Available?</small></th>
					@endif

					<th class="hidden-lg-down center"># staff</th>

					<th class="hidden-lg-down center small">resources</th>

					<th class="hidden-lg-down center"># items</th>

					@include('cspot.snippets.theader', ['thfname' => 'leader_id', 'thdisp' => 'Leader', 'thsearch'=>false, 'thclass'=>'hidden-sm-down center'])

					@include('cspot.snippets.theader', ['thfname' => 'teacher_id', 'thdisp' => 'Teacher', 'thsearch'=>false, 'thclass'=>'hidden-sm-down center'])

					<th class="hidden-md-up center">Leader, Teacher</th>
					<th class="text-right hidden-md-down">Updated</th>
				</tr>
			</thead>



			<tbody>

	        @foreach( $plans as $plan )

				<tr><?php $editPlansHtml ="onclick=\"showSpinner();location.href='" . url('cspot/plans/'.$plan->id) . "/edit'";
						  $editPlansHtml.='" data-toggle="tooltip" title="Click a plan to view/edit it"'; ?>

					<td {!! $editPlansHtml !!} class="link hidden-lg-down center" scope="row">{{ $plan->id }}</td>


					<td {!! $editPlansHtml !!} class="link hidden-lg-down">{{ $plan->date->formatLocalized('%A, %d %B %Y') }}</td>
					<td {!! $editPlansHtml !!} class="link hidden-sm-down hidden-xl-up">{{ $plan->date->formatLocalized('%a, %d %B %Y') }}</td>
					<td {!! $editPlansHtml !!} class="link hidden-md-up">{{ $plan->date->formatLocalized('%a, %d %b') }}</td>


					<td {!! $editPlansHtml !!} class="link">{{ $plan->type->generic ? $plan->subtitle : $plan->type->name 
						}}<span class="hidden-md-up small text-muted">{!! (! $plan->subtitle || $plan->type->generic) ? ''  : ' '.$plan->subtitle !!}</span></td>

					<td {!! $editPlansHtml !!} class="link hidden-lg-down center">{{ $plan->type->generic ? '' : $plan->subtitle }}</td>


					<td {!! $editPlansHtml !!} class="link hidden-lg-down center" scope="row">
						@if ($plan->date->hour==0 && $plan->date->minute==0 && ! $plan->date_end )
							n/a
						@else
							{{ $plan->date->formatLocalized('%H:%M') }}{{ $plan->date_end ? '-'.$plan->date_end->formatLocalized('%H:%M') : ''}}
						@endif
					</td>


					@if( Auth::user()->isUser())
						<td class="hidden-lg-down center">
							@if (Auth::user()->isEditor())
								<input type="checkbox" {{ $plan->private ? 'checked="checked"' : '' }}
									onclick="togglePlanPrivate(this, {{ $plan->id }})">
								<span class="c-indicator"></span>
							@endif
							<span id="plan-private-{{ $plan->id }}">
								{!! $plan->private ? '&#10003;' : '' !!}									
							</span>
						</td>

						<td class="center">
							@if( $plan->date > \Carbon\Carbon::yesterday() )
								<a class="hidden-lg-up float-right" title="show team for this plan" href="{{ url('cspot/plans/'.$plan->id) }}/team"><small>staff</small></a>
								<input type="checkbox" {{ isset($userIsPlanMember[$plan->id]) ? 'checked' : '' }}
									onclick="userAvailableForPlan(this, {{ $plan->id }}, {{ Auth::user()->id }})">
								<span class="c-indicator"></span>
								<span class="hidden-sm-down" id="user-available-for-plan-id-{{ $plan->id }}">
									{{ isset($userIsPlanMember[$plan->id]) ? '&#10003;' : '' }}
								</span>
							@endif
						</td>
					@endif


					<td class="hidden-lg-down center">
						<a class="float-right" title="show team for this plan" href="{{ url('cspot/plans/'.$plan->id) }}/team"><small>(show)</small></a>
						{{ $plan->teams->count() ? $plan->teams->count() : '' }}
					</td>

					<td class="hidden-lg-down center">
						<a class="float-right" title="show resources for this plan" href="{{ url('cspot/plans/'.$plan->id) }}/resource"><small>(show)</small></a>
						{{ $plan->resources->count() ? $plan->resources->count() : '' }}
					</td>

					<td {!! $editPlansHtml !!} class="link hidden-lg-down center">{{ $plan->items->count() }}</td>

					<td class="hidden-sm-down center">{{ $plan->leader ? $plan->leader->name : $plan->leader_id }}</td>
					<td class="hidden-sm-down center">{{ $plan->teacher? $plan->teacher->name : $plan->teacher_id }}</td>
					<td class="hidden-md-up center small">
						{{ $plan->leader ? $plan->leader->name : $plan->leader_id }}{{ $plan->teacher_id<>0 ? ', '.$plan->teacher->name : '' }}
					</td>

					<td class="hidden-md-down text-right small">
						{{ isset($plan->updated_at) ? $plan->updated_at->formatLocalized('%d-%m-%Y %H:%M') : 'unknown' 
						}}/{{ ucfirst($plan->changer) }}
					</td>

				</tr>

	        @endforeach

			</tbody>
		</table>


		@if (get_class($plans)=='Illuminate\Pagination\LengthAwarePaginator')
			<div class="events-table hidden">
				<div class="d-flex justify-content-center fixed-bottom">
					{!! $plans->links() !!}
				</div>
			</div>
		@endif


    @else

    	<p>No plans found!</p>

	@endif

	
@stop
