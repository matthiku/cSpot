
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
	


	@if( Auth::user()->isEditor() )
		<a class="btn btn-outline-success float-xs-right" 
			href="{{ url('cspot/plans/create') }}{{ 
				( Request::has('filterby') && Request::input('filterby')=='type' && Request::has('filtervalue') ) 
					? '?type_id='.Request::input('filtervalue') 
					: '' }}">
			<i class="fa fa-plus"> </i>&nbsp; Create New<span class="hidden-md-down"> Event</span>
		</a>
	@endif



	<form class="form-inline float-xs-right mr-1">
		<div class="form-group">
			<label for="typefilter">Show only</label>
			<select class="custom-select" id="typefilter" onchange="showSpinner();location.href='{{url('cspot/plans')}}?filterby=type&filtervalue='+$(this).val()">
				<option {{ Request::has('filterby') && Request::get('filterby')=='type' ? '' : 'selected' }} value="all">(select type)</option>
				@foreach ($types as $type)
					<option 
						{{ (Request::has('filterby') && Request::get('filterby')=='type' && Request::get('filtervalue')==$type->id) ? 'selected' : '' }} 
						value="{{$type->id}}">{{$type->name}}</option>
				@endforeach
			</select>
		</div>
	</form>


	<form class="form-inline float-xs-right mr-1">
		<div class="dropdown" id="multi-filter-dropdown" data-url="{{url('cspot/plans')}}?filterby=type&filtervalue=">
			{{-- check if request already is filtering out some plans --}}
			@if ( Request::has('filtervalue') )
				@php 
					$fv = json_decode(Request::input('filtervalue')); 
					if ($fv==Request::input('filtervalue'))		// make sure $fv is always an array!
						$fv = [Request::input('filtervalue')]; 
				@endphp
			@endif
			<button class="btn dropdown-toggle {{ isset($fv) && sizeof($fv) ? 'btn-primary' : 'btn-secondary ' }}" 
				type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Show All Of:
			</button>
			<div class="dropdown-menu bg-faded padding-half" aria-labelledby="dropdownMenuButton">
				<h5 class="dropdown-header">Select which types to show:</h5>
				@foreach ($types as $type)
					<div class="form-check display-block">
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



    <h3 class="float-xs-left text-success lora">
    	{{ $heading }}
		<small class="small" style="font-size: 50%">
			<a href="#" onclick="toogleAllorFuturePlans()">show {{Request::get('show')!='all' ? 'all' : 'only upcoming'}}</a>
		</small>
    </h3>

	@if ( get_class($plans)=='Illuminate\Pagination\LengthAwarePaginator' && $plans->lastPage() > 1 )
		<center>Page {{ $plans->currentPage() }} of {{ $plans->lastPage() }}</center>
	@endif



	@if ( isset($plans) && count($plans) )
		@if (get_class($plans)=='Illuminate\Pagination\LengthAwarePaginator')
			<center><small>(Total: {{ $plans->total() }} Events)</small></center>
		@endif

		<table class="table table-striped table-bordered table-hover
					@if(count($plans)>15)
					 table-sm
					@endif
					 ">



			<thead class="thead-default">
				<tr>
					<th class="hidden-md-down center">#</th>

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

					<th class="hidden-md-down center"># staff</th>

					<th class="hidden-md-down center small">resources</th>

					<th class="hidden-lg-down center"># items</th>

					@include('cspot.snippets.theader', ['thfname' => 'leader_id', 'thdisp' => 'Leader', 'thsearch'=>false, 'thclass'=>'hidden-sm-down center'])

					@include('cspot.snippets.theader', ['thfname' => 'teacher_id', 'thdisp' => 'Teacher', 'thsearch'=>false, 'thclass'=>'hidden-sm-down center'])

					<th class="hidden-md-up center">Leader, Teacher</th>
					<th class="text-right hidden-md-down">Last updated on</th>
					<th class="hidden-md-down">by</th>
				</tr>
			</thead>



			<tbody>

	        @foreach( $plans as $plan )

				<tr><?php $editPlansHtml ="onclick=\"showSpinner();location.href='" . url('cspot/plans/'.$plan->id) . "/edit'";
						  $editPlansHtml.='" data-toggle="tooltip" title="Click a plan to view/edit it"'; ?>

					<td {!! $editPlansHtml !!} class="link hidden-md-down center" scope="row">{{ $plan->id }}</td>

					<td {!! $editPlansHtml !!} class="link hidden-md-down">{{ $plan->date->formatLocalized('%A, %d %B %Y') }}</td>
					<td {!! $editPlansHtml !!} class="link hidden-sm-down hidden-lg-up">{{ $plan->date->formatLocalized('%a, %d %B %Y') }}</td>
					<td {!! $editPlansHtml !!} class="link hidden-md-up">{{ $plan->date->formatLocalized('%a, %d %b') }}</td>

					<td {!! $editPlansHtml !!} class="link">{{ $plan->type->name }}<span class="hidden-md-up small text-muted">{!! $plan->subtitle ? ' '.$plan->subtitle : '' !!}</span></td>

					<td {!! $editPlansHtml !!} class="link hidden-lg-down center">{{ $plan->subtitle }}</td>

					<td {!! $editPlansHtml !!} class="link hidden-lg-down center" scope="row">
						@if ($plan->date->hour==0 && $plan->date->minute==0 && ! $plan->date_end )
							n/a
						@else
							{{ $plan->date->formatLocalized('%H:%M') }}{{ $plan->date_end ? '-'.$plan->date_end->formatLocalized('%H:%M') : ''}}
						@endif
					</td>

					@if( Auth::user()->isUser())
						<td class="hidden-lg-down center">
							<label class="c-input c-checkbox">
								@if (Auth::user()->isEditor())
									<input type="checkbox" {{ $plan->private ? 'checked="checked"' : '' }}
										onclick="togglePlanPrivate(this, {{ $plan->id }})">
									<span class="c-indicator"></span>
								@endif
								<span id="plan-private-{{ $plan->id }}">
									{!! $plan->private ? '&#10004;' : '' !!}									
								</span>
							</label>
						</td>

						<td class="center">
							@if( $plan->date > \Carbon\Carbon::yesterday() )
								<a class="hidden-lg-up float-xs-right" title="show team for this plan" href="{{ url('cspot/plans/'.$plan->id) }}/team">staff</a>
								<label class="c-input c-checkbox">
									<input type="checkbox" {{ isset($userIsPlanMember[$plan->id]) ? 'checked' : '' }}
										onclick="userAvailableForPlan(this, {{ $plan->id }}, {{ Auth::user()->id }})">
									<span class="c-indicator"></span>
								</label>
								<span class="hidden-sm-down text-muted" id="user-available-for-plan-id-{{ $plan->id }}">
									{{ isset($userIsPlanMember[$plan->id]) ? 'yes' : 'no' }}
								</span>
							@endif
						</td>
					@endif

					<td class="hidden-md-down center">
						<a class="float-xs-right" title="show team for this plan" href="{{ url('cspot/plans/'.$plan->id) }}/team"><small>(show)</small></a>
						{{ $plan->teams->count() ? $plan->teams->count() : '' }}
					</td>

					<td class="hidden-md-down center">
						<a class="float-xs-right" title="show resources for this plan" href="{{ url('cspot/plans/'.$plan->id) }}/resource"><small>(show)</small></a>
						{{ $plan->resources->count() ? $plan->resources->count() : '' }}
					</td>

					<td {!! $editPlansHtml !!} class="link hidden-lg-down center">{{ $plan->items->count() }}</td>

					<td class="hidden-sm-down center">{{ $plan->leader ? $plan->leader->name : $plan->leader_id }}</td>
					<td class="hidden-sm-down center">{{ $plan->teacher? $plan->teacher->name : $plan->teacher_id }}</td>
					<td class="hidden-md-up center small">
						{{ $plan->leader ? $plan->leader->name : $plan->leader_id }}{{ $plan->teacher_id<>0 ? ', '.$plan->teacher->name : '' }}
					</td>

					<td class="hidden-md-down text-right small">
						{{ isset($plan->updated_at) ? $plan->updated_at->formatLocalized('%d-%m-%Y %H:%M') : 'unknown' }}</td>
					<td class="hidden-md-down small">{{ ucfirst($plan->changer) }}</td>

				</tr>

	        @endforeach

			</tbody>


		</table>

		@if (get_class($plans)=='Illuminate\Pagination\LengthAwarePaginator')
			<center>
				{!! $plans->links() !!}
			</center>
		@endif

    @else

    	<p>No plans found!</p>

	@endif

	
@stop
