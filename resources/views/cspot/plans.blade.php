
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
		<a class="btn btn-outline-primary pull-xs-right" href="{{ url('cspot/plans/create') }}">
			<i class="fa fa-plus"> </i> &nbsp; Add a new Service/Event
		</a>
	@endif

    <h2 class="pull-xs-left">
    	{{ $heading }}
		<br>
		<small class="pull-xs-left" style="font-size: 50%">
			<a href="#" onclick="toogleAllorFuturePlans()">
				<input type="checkbox" {{Request::get('show')=='all' ? '' : 'checked'}}>
				show only upcoming events</a>
		</small>
    </h2>

	@if (get_class($plans)=='Illuminate\Pagination\LengthAwarePaginator')
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

					@include('cspot.snippets.theader', ['thfname' => 'date', 'thdisp' => 'Date', 'thsort'=>false, 'thclass'=>''])
					
					@include('cspot.snippets.theader', ['thfname' => 'type_id', 'thdisp' => 'Service Type', 'thsort'=>false, 'thclass'=>''])

					<th class="hidden-lg-down center" title="Additional info that appears on the Announcements Slide">Info</th>

					<th class="hidden-lg-down center">Times</th>

					@if( Auth::user()->isUser() )
						<th class="center" title="Check when you are available for that particular plan"
							data-toggle="tooltip" title="Check when you are available for that particular plan">
							<small>Available?</small></th>
					@endif

					<th class="hidden-md-down center"># staff</th>

					<th class="hidden-md-down center small">resources</th>

					<th class="hidden-lg-down center"># items</th>

					@include('cspot.snippets.theader', ['thfname' => 'leader_id', 'thdisp' => 'Leader', 'thsort'=>false, 'thclass'=>'hidden-sm-down center'])

					@include('cspot.snippets.theader', ['thfname' => 'teacher_id', 'thdisp' => 'Teacher', 'thsort'=>false, 'thclass'=>'hidden-sm-down center'])

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
						<td class="center">
							@if( $plan->date > \Carbon\Carbon::yesterday() )
								<a class="hidden-lg-up pull-xs-right" title="show team for this plan" href="{{ url('cspot/plans/'.$plan->id) }}/team">staff</a>
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
						<a class="pull-xs-right" title="show team for this plan" href="{{ url('cspot/plans/'.$plan->id) }}/team"><small>(show)</small></a>
						{{ $plan->teams->count() ? $plan->teams->count() : '' }}
					</td>

					<td class="hidden-md-down center">
						<a class="pull-xs-right" title="show resources for this plan" href="{{ url('cspot/plans/'.$plan->id) }}/resource"><small>(show)</small></a>
						{{ $plan->resources->count() ? $plan->resources->count() : '' }}
					</td>

					<td {!! $editPlansHtml !!} class="link hidden-lg-down center">{{ $plan->items->count() }}</td>

					<td class="hidden-sm-down center">{{ $plan->leader ? $plan->leader->name : $plan->leader_id }}</td>
					<td class="hidden-sm-down center">{{ $plan->teacher? $plan->teacher->name : $plan->teacher_id }}</td>
					<td class="hidden-md-up center">
						{{ $plan->leader ? $plan->leader->name : $plan->leader_id }}{{ $plan->teacher_id<>0 ? ', '.$plan->teacher->name : '' }}
					</td>

					<td class="hidden-md-down text-right">
						{{ isset($plan->updated_at) ? $plan->updated_at->formatLocalized('%d-%m-%Y %H:%M') : 'unknown' }}</td>
					<td class="hidden-md-down">{{ ucfirst($plan->changer) }}</td>

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
