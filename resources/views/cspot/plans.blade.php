
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
	<a class="btn btn-primary-outline pull-xs-right" href="{{ url('cspot/plans/create') }}">
		<i class="fa fa-plus"> </i> &nbsp; Add a new plan
	</a>
	@endif

    <h2>{{ $heading }}</h2>

	@if( Request::is('*/by_user/*') || Request::is('*/by_type/*') )
		<p>
			@if( Request::is('*/all') )
				<a href="{{ url('/').'/'.Request::segment(1).'/'.Request::segment(2).'/'.Request::segment(3).'/'.Request::segment(4) }}">
			@else
				<a href="{{ Request::url() }}/all">
			@endif
			<input type="checkbox" {{Request::is('*/all') ? '' : 'checked'}}>
			show only upcoming service plans</a>
		</p>
	@endif


	@if (count($plans))
		Total: {{ count($plans) }} Services

		<table class="table table-striped table-bordered table-hover
					@if(count($plans)>15)
					 table-sm
					@endif
					 ">
			<thead class="thead-default">
				<tr>
					<th class="hidden-md-down center">#</th>
					<th>Date</th>
					<th>Service Type</th>
					<th class="hidden-sm-down center"># items</th>
					<th class="hidden-xs-down center">Leader</th>
					<th class="hidden-xs-down center">Teacher</th>
					<th class="hidden-sm-up center">Leader, Teacher</th>
					<th class="text-right hidden-md-down">Last updated on</th>
					<th class="hidden-md-down">by</th>
				</tr>
			</thead>


			<tbody>
	        @foreach( $plans as $plan )
				<tr class="link" onclick="location.href='{{ url('cspot/plans/'.$plan->id) }}/edit'"
					data-toggle="tooltip" title="Click on a plan to view/edit it">

					<td class="hidden-md-down center" scope="row">{{ $plan->id }}</td>

					<td class="hidden-md-down">{{ $plan->date->formatLocalized('%A, %d %B %Y') }}</td>
					<td class="hidden-sm-down hidden-lg-up">{{ $plan->date->formatLocalized('%a, %d %B %Y') }}</td>
					<td class="hidden-md-up">{{ $plan->date->formatLocalized('%a, %d %b') }}</td>

					<td>{{ $plan->type->name }}</td>

					<td class="hidden-sm-down center">{{ $plan->items->count() }}</td>
					<td class="hidden-xs-down center">{{ $plan->leader->first_name }}</td>
					<td class="hidden-xs-down center">{{ $plan->teacher->first_name }}</td>
					<td class="hidden-sm-up center">
						{{ $plan->leader->first_name }}{{ $plan->teacher_id<>0 ? ', '.$plan->teacher->first_name : '' }}
					</td>

					<td class="hidden-md-down text-right">{{ $plan->updated_at->formatLocalized('%d-%m-%Y %H:%M') }}</td>
					<td class="hidden-md-down">{{ ucfirst($plan->changer) }}</td>

				</tr>
	        @endforeach

			</tbody>

		</table>

    @else

    	<p>No plans found!</p>

	@endif

	
@stop
