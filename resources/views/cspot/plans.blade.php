
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

		<table class="table table-striped table-bordered 
					@if(count($plans)>15)
					 table-sm
					@endif
					 ">
			<thead class="thead-default">
				<tr>
					<th class="hidden-md-down">#</th>
					<th>Date</th>
					<th>Service Type</th>
					<th>Leader</th>
					<th>Teacher</th>
					<!-- <th>State</th> -->
					<th class="text-right hidden-md-down">Last updated on</th>
					<th class="hidden-md-down">by</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
	        @foreach( $plans as $plan )
				<tr class="link" onclick="location.href='{{ url('cspot/plans/'.$plan->id) }}/edit'">
					<td class="hidden-md-down" scope="row">{{ $plan->id }}</td>
					<td class="hidden-md-down">{{ $plan->date->formatLocalized('%A, %d %B %Y') }}</td>
					<td class="hidden-sm-down hidden-lg-up">{{ $plan->date->formatLocalized('%a, %d %B %Y') }}</td>
					<td class="hidden-md-up">{{ $plan->date->formatLocalized('%a, %d %b') }}</td>
					<td>{{ $plan->type->name }}</td>
					<td>{{ $plan->leader->first_name }}</td>
					<td>{{ $plan->teacher->first_name }}</td>
					<!-- <td>{{ $plan->state }}</td> -->
					<td class="text-right hidden-md-down">{{ $plan->updated_at->formatLocalized('%d-%m-%Y %H:%M') }}</td>
					<td class="hidden-md-down">{{ ucfirst($plan->changer) }}</td>
					<td class="nowrap">
						<!-- <a class="btn btn-secondary btn-sm" title="Show Items" href='/cspot/items/{{$plan->id}}'><i class="fa fa-filter"></i></a> -->
						<!-- if( Auth::user()->isEditor() || Auth::user()->id == $plan->leader_id ) -->
							<a class="btn btn-primary-outline btn-sm" title="Edit" href='{{ url('cspot/plans/'.$plan->id) }}/edit'><i class="fa fa-pencil"></i></a>
						<!-- endif -->
						@if( Auth::user()->isEditor() )
							<a class="btn btn-danger btn-sm" title="Delete!" href='{{ url('cspot/plans/'.$plan->id) }}/delete'><i class="fa fa-trash"></i></a>
						@endif
					</td>
				</tr>
	        @endforeach
			</tbody>
		</table>

    @else

    	<p>No plans found!</p>

	@endif

	
@stop
